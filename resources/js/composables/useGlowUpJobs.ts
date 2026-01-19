import type {
    GlowUpJob,
    GlowUpState,
    GlowUpUsage,
} from '@/components/properties/workspace/types';
import { onRealtimeEvent } from '@/lib/realtimeEvents';
import glowupRoutes from '@/routes/glowup/index';
import propertiesRoutes from '@/routes/properties/index';
import type { GlowUpJobPayload } from '@/types';
import { router, useForm, usePage } from '@inertiajs/vue3';
import {
    computed,
    onBeforeUnmount,
    onMounted,
    reactive,
    ref,
    watch,
    type Ref,
} from 'vue';

interface UseGlowUpJobsOptions {
    propertyId: number;
    glowUp: Ref<GlowUpState | null | undefined>;
}

const normalizeJob = (job: GlowUpJob | GlowUpJobPayload): GlowUpJob => ({
    id: Number(job.id),
    property_id: Number(job.property_id),
    room_type: job.room_type ?? 'living_room',
    style: job.style ?? 'modern',
    before_url: job.before_url,
    after_url: job.after_url ?? null,
    status: job.status ?? 'pending',
    error_message: job.error_message ?? null,
    progress: Number(job.progress ?? 0),
    is_terminal: Boolean(job.is_terminal ?? false),
    created_at: job.created_at ?? null,
    updated_at: job.updated_at ?? null,
    usage_recorded_at: job.usage_recorded_at ?? null,
});

const sortJobs = (input: GlowUpJob[]) =>
    [...input].sort((a, b) => {
        const aTime = a.created_at ? new Date(a.created_at).getTime() : 0;
        const bTime = b.created_at ? new Date(b.created_at).getTime() : 0;
        if (aTime === bTime) {
            return b.id - a.id;
        }
        return bTime - aTime;
    });

export const useGlowUpJobs = ({ propertyId, glowUp }: UseGlowUpJobsOptions) => {
    const initialState = glowUp.value;
    const initialUsage = initialState?.usage ?? null;
    const jobs = ref<GlowUpJob[]>(
        sortJobs((initialState?.jobs ?? []).map(normalizeJob)),
    );
    const countedJobIds = new Set<number>(
        jobs.value.filter((job) => job.usage_recorded_at).map((job) => job.id),
    );

    const usage = reactive<GlowUpUsage>({
        used: initialUsage?.used ?? 0,
        limit: initialUsage?.limit ?? 0,
        remaining: initialUsage?.remaining ?? null,
        is_unlimited: initialUsage?.is_unlimited ?? false,
        is_blocked: initialUsage?.is_blocked ?? false,
        can_use: initialUsage?.can_use ?? true,
        percent_used: initialUsage?.percent_used ?? 0,
        reset_at: initialUsage?.reset_at ?? null,
    });

    const previewUrl = ref<string | null>(null);
    let previewObject: string | null = null;

    const activeJobId = ref<number | null>(jobs.value[0]?.id ?? null);

    const createForm = useForm<{
        room_type: string | null;
        style: string | null;
        image: File | null;
        user_instructions: string;
    }>({
        room_type: initialState?.options?.room_types?.[0]?.value ?? null,
        style: initialState?.options?.styles?.[0]?.value ?? null,
        image: null,
        user_instructions: '',
    });

    const attachForm = useForm({
        action: 'save_to_property',
        notes: '',
    });

    const page = usePage<{ flash?: { glowupJob?: GlowUpJobPayload | null } }>();
    const lastFlashJobId = ref<number | null>(null);
    let removeRealtimeListener: (() => void) | null = null;

    const syncUsage = () => {
        usage.is_unlimited = usage.limit === -1;
        usage.is_blocked = usage.limit === 0;

        if (usage.is_unlimited) {
            usage.remaining = null;
            usage.percent_used = null;
            usage.can_use = true;
            return;
        }

        if (usage.is_blocked) {
            usage.remaining = 0;
            usage.percent_used = 0;
            usage.can_use = false;
            return;
        }

        usage.remaining = Math.max(0, usage.limit - usage.used);
        usage.percent_used =
            usage.limit > 0
                ? Math.min(100, Math.round((usage.used / usage.limit) * 100))
                : 0;
        usage.can_use = usage.used < usage.limit;
    };

    syncUsage();

    const limitReached = computed(() => !usage.can_use);

    const remaining = computed(() => {
        if (usage.is_unlimited) {
            return Infinity;
        }

        return Math.max(0, usage.remaining ?? 0);
    });

    const activeJob = computed(
        () =>
            jobs.value.find((job) => job.id === activeJobId.value) ??
            jobs.value[0] ??
            null,
    );

    const completedJobs = computed(() =>
        jobs.value.filter((job) => job.status === 'done' && job.after_url),
    );

    const latestCompletedJob = computed(() => completedJobs.value[0] ?? null);

    const upsertJob = (incoming: GlowUpJob) => {
        const next = [...jobs.value];
        const index = next.findIndex((job) => job.id === incoming.id);
        if (index === -1) {
            next.unshift(incoming);
        } else {
            next.splice(index, 1, incoming);
        }

        jobs.value = sortJobs(next);

        if (!activeJobId.value) {
            activeJobId.value = incoming.id;
        }

        if (incoming.usage_recorded_at && !countedJobIds.has(incoming.id)) {
            countedJobIds.add(incoming.id);
            usage.used = Math.max(0, (usage.used ?? 0) + 1);
            syncUsage();
        }
    };

    const setImage = (file: File | null) => {
        if (previewObject) {
            URL.revokeObjectURL(previewObject);
            previewObject = null;
        }

        createForm.image = file;

        if (file) {
            previewObject = URL.createObjectURL(file);
            previewUrl.value = previewObject;
        } else {
            previewUrl.value = null;
        }
    };

    const captureFromCamera = async () => {
        const [{ Camera, CameraResultType, CameraSource }] = await Promise.all([
            import('@capacitor/camera'),
        ]);

        const photo = await Camera.getPhoto({
            quality: 85,
            resultType: CameraResultType.Uri,
            source: CameraSource.Camera,
        });

        if (!photo.webPath) {
            return;
        }

        const response = await fetch(photo.webPath);
        const blob = await response.blob();
        const file = new File([blob], `glowup-${Date.now()}.jpg`, {
            type: blob.type,
        });

        setImage(file);
    };

    const submitJob = () => {
        if (!createForm.image) {
            createForm.setError('image', 'Add an image before generating.');
            return;
        }

        createForm.clearErrors();

        createForm.post(
            propertiesRoutes.glowup.jobs.store.url({
                property: propertyId,
            }),
            {
                forceFormData: true,
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    setImage(null);
                },
            },
        );
    };

    const attachToProperty = (
        jobId: number,
        action: 'save_to_property' | 'add_to_report',
        options: { notes?: string; onSuccess?: () => void } = {},
    ) => {
        attachForm.transform(() => ({
            action,
            notes: options.notes,
        }));

        attachForm.post(
            glowupRoutes.jobs.attach.url({
                glowupJob: jobId,
            }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    options.onSuccess?.();
                },
            },
        );
    };

    const detachFromProperty = (
        jobId: number,
        action: 'save_to_property' | 'add_to_report',
        options: { onSuccess?: () => void } = {},
    ) => {
        attachForm.transform(() => ({
            action,
            notes: undefined,
        }));

        attachForm.post(
            glowupRoutes.jobs.detach.url({
                glowupJob: jobId,
            }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    options.onSuccess?.();
                },
            },
        );
    };

    const refreshJobs = () => {
        router.reload({
            only: ['property'],
            preserveScroll: true,
        });
    };

    watch(
        () => page.props.flash?.glowupJob,
        (job) => {
            if (job && job.id !== lastFlashJobId.value) {
                lastFlashJobId.value = job.id ?? null;
                upsertJob(normalizeJob(job));
            }
        },
    );

    watch(
        glowUp,
        (state) => {
            if (state?.jobs) {
                jobs.value = sortJobs(state.jobs.map(normalizeJob));
                countedJobIds.clear();
                state.jobs.forEach((job) => {
                    if (job.usage_recorded_at) {
                        countedJobIds.add(job.id);
                    }
                });
            }

            if (state?.usage) {
                usage.used = state.usage.used ?? 0;
                usage.limit = state.usage.limit ?? 0;
                usage.remaining = state.usage.remaining ?? null;
                usage.is_unlimited = state.usage.is_unlimited ?? false;
                usage.is_blocked = state.usage.is_blocked ?? false;
                usage.can_use = state.usage.can_use ?? true;
                usage.percent_used = state.usage.percent_used ?? 0;
                usage.reset_at = state.usage.reset_at ?? null;
                syncUsage();
            }

            if (!createForm.room_type && state?.options?.room_types?.length) {
                createForm.room_type = state.options.room_types[0].value;
            }

            if (!createForm.style && state?.options?.styles?.length) {
                createForm.style = state.options.styles[0].value;
            }
        },
        { deep: true },
    );

    onMounted(() => {
        if (typeof window === 'undefined') {
            return;
        }

        removeRealtimeListener = onRealtimeEvent('glowup:job', (job) => {
            if (Number(job.property_id) !== Number(propertyId)) {
                return;
            }
            upsertJob(normalizeJob(job));
        });
    });

    onBeforeUnmount(() => {
        removeRealtimeListener?.();
        removeRealtimeListener = null;

        if (previewObject) {
            URL.revokeObjectURL(previewObject);
        }
    });

    const canUseCamera = computed(
        () => typeof window !== 'undefined' && !!navigator.mediaDevices,
    );
    const isUploading = computed(() => createForm.processing);

    return {
        jobs,
        activeJob,
        activeJobId,
        latestCompletedJob,
        createForm,
        attachForm,
        submitJob,
        attachToProperty,
        detachFromProperty,
        refreshJobs,
        setActiveJob: (jobId: number) => {
            activeJobId.value = jobId;
        },
        setImage,
        captureFromCamera,
        previewUrl,
        canUseCamera,
        isUploading,
        limitReached,
        remaining,
        usage,
    };
};
