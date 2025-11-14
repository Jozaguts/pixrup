import type {
    GlowUpJob,
    GlowUpState,
    GlowUpUsage,
} from '@/components/properties/workspace/types';
import propertiesAPI from '@/routes/properties/index';
import glowUpAPI from '@/routes/glowup/index';
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
    const jobs = ref<GlowUpJob[]>(
        sortJobs((initialState?.jobs ?? []).map(normalizeJob)),
    );
    const countedJobIds = new Set<number>(
        jobs.value.filter((job) => job.usage_recorded_at).map((job) => job.id),
    );

    const usage = reactive<GlowUpUsage>({
        used: initialState?.usage?.used ?? 0,
        limit: initialState?.usage?.limit ?? null,
        reset_at: initialState?.usage?.reset_at ?? null,
    });

    const previewUrl = ref<string | null>(null);
    let previewObject: string | null = null;

    const activeJobId = ref<number | null>(jobs.value[0]?.id ?? null);

    const createForm = useForm<{
        room_type: string | null;
        style: string | null;
        image: File | null;
        prompt: string;
    }>({
        room_type: initialState?.options?.room_types?.[0]?.value ?? null,
        style: initialState?.options?.styles?.[0]?.value ?? null,
        image: null,
        prompt: '',
    });

    const attachForm = useForm({
        action: 'save_to_property',
        notes: '',
    });

    const page = usePage<{ flash?: { glowupJob?: GlowUpJobPayload | null } }>();
    const lastFlashJobId = ref<number | null>(null);
    let subscription: ReturnType<
        NonNullable<typeof window.Echo>['private']
    > | null = null;

    const limitReached = computed(
        () =>
            usage.limit !== null &&
            usage.limit > 0 &&
            usage.used >= usage.limit,
    );

    const remaining = computed(() => {
        if (usage.limit === null) {
            return Infinity;
        }

        return Math.max(0, usage.limit - usage.used);
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

        if (
            incoming.usage_recorded_at &&
            !countedJobIds.has(incoming.id) &&
            usage.limit !== null
        ) {
            countedJobIds.add(incoming.id);
            usage.used = Math.min(usage.limit, (usage.used ?? 0) + 1);
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
            createForm.prompt = '';
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

        const trimmedPrompt = (createForm.prompt ?? '').trim();

        if (!trimmedPrompt) {
            createForm.setError(
                'prompt',
                'Provide a prompt before generating.',
            );
            return;
        }

        createForm.prompt = trimmedPrompt;
        createForm.clearErrors();

        createForm.post(
            propertiesAPI.glowup.jobs.store.url({
                property: propertyId,
            }),
            {
                forceFormData: true,
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    setImage(null);
                    createForm.prompt = '';
                },
            },
        );
    };

    const attachToProperty = (
        jobId: number,
        action: 'save_to_property' | 'add_to_report',
        notes?: string,
    ) => {
        attachForm.transform(() => ({
            action,
            notes,
        }));

        attachForm.post(
            glowUpAPI.jobs.attach.url({
                glowupJob: jobId,
            }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
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
                usage.limit = state.usage.limit ?? null;
                usage.reset_at = state.usage.reset_at ?? null;
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

        if (window.Echo && propertyId) {
            subscription = window.Echo.private(`glowup.jobs.${propertyId}`);
            subscription.listen(
                '.GlowUpJobUpdated',
                (event: { job: GlowUpJobPayload }) => {
                    if (event?.job) {
                        upsertJob(normalizeJob(event.job));
                    }
                },
            );
        }
    });

    onBeforeUnmount(() => {
        if (typeof window !== 'undefined' && subscription) {
            window.Echo?.leave(`glowup.jobs.${propertyId}`);
            subscription = null;
        }

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
