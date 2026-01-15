<script setup lang="ts">
import type { GlowUpState } from '@/components/properties/workspace/types';
import { useGlowUpJobs } from '@/composables/useGlowUpJobs';
import propertiesRoutes from '@/routes/properties';
import { useForm } from '@inertiajs/vue3';
import { Sparkles } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import GlowUpConfigurePanel from './GlowUpConfigurePanel.vue';
import GlowUpResultModal from './GlowUpResultModal.vue';
import GlowUpResultSlider from './GlowUpResultSlider.vue';
import { defaultRoomTypes, defaultStyleOptions } from './glowupConstants';
import WorkspaceModuleHeader from '@/components/properties/workspace/WorkspaceModuleHeader.vue';

interface Props {
    propertyId: number | string;
    glowUp?: GlowUpState | null;
}

const props = defineProps<Props>();

const glowUpState = computed(() => props.glowUp ?? null);
const numericPropertyId = computed(() => Number(props.propertyId));

const {
    jobs,
    attachForm,
    attachToProperty,
    createForm,
    detachFromProperty,
    submitJob,
    refreshJobs,
    setActiveJob,
    setImage,
    captureFromCamera,
    previewUrl,
    canUseCamera,
    isUploading,
    limitReached,
    usage,
} = useGlowUpJobs({
    propertyId: numericPropertyId.value,
    glowUp: glowUpState,
});

const roomOptions = computed(() => glowUpState.value?.options?.room_types ?? defaultRoomTypes);
const styleOptions = computed(() => glowUpState.value?.options?.styles ?? defaultStyleOptions);
const maxUpload = computed(() => glowUpState.value?.limits?.max_upload_size_mb ?? 10);
const roomLabelMap = computed(
    () => new Map(roomOptions.value.map((room) => [room.value, room.label])),
);
const formatRoomLabel = (roomType: string) =>
    roomLabelMap.value.get(roomType) ?? roomType.replace(/_/g, ' ');

const selectedFileLabel = computed(() => createForm.image?.name ?? 'Select an image');

const isGenerateDisabled = computed(() => {
    if (limitReached.value || isUploading.value) {
        return true;
    }

    return !createForm.image || !createForm.room_type || !createForm.style;
});

const isResultOpen = ref(false);
const modalJobId = ref<number | null>(null);
const modalJob = computed(() => jobs.value.find((job) => job.id === modalJobId.value) ?? null);
const showHistory = ref(false);
const historyButtonLabel = computed(() => (showHistory.value ? 'Back to configure' : 'View history'));
const processedJobs = computed(() =>
    jobs.value.filter((job) => job.status === 'done' && job.before_url && job.after_url),
);
const glowUpAttachments = computed(() => glowUpState.value?.attachments ?? []);
const attachmentMap = computed(() => {
    const map = new Map<number, { save_to_property: boolean; add_to_report: boolean }>();
    glowUpAttachments.value.forEach((attachment) => {
        const jobId = Number(attachment.job_id);
        if (!jobId) {
            return;
        }
        const entry = map.get(jobId) ?? {
            save_to_property: false,
            add_to_report: false,
        };
        if (attachment.action === 'save_to_property') {
            entry.save_to_property = true;
        }
        if (attachment.action === 'add_to_report') {
            entry.add_to_report = true;
        }
        map.set(jobId, entry);
    });
    return map;
});
const getAttachmentState = (jobId: number) =>
    attachmentMap.value.get(jobId) ?? { save_to_property: false, add_to_report: false };

const regenerateForm = useForm({
    room_type: null as string | null,
    style: null as string | null,
    source_job_id: null as number | null,
});

const handleGenerate = () => {
    if (isGenerateDisabled.value) {
        return;
    }

    modalJobId.value = null;
    submitJob();
    isResultOpen.value = true;
};

const handleRegenerate = (payload: { room_type: string; style: string }) => {
    if (!modalJob.value?.id) {
        return;
    }

    regenerateForm.clearErrors();
    regenerateForm.room_type = payload.room_type;
    regenerateForm.style = payload.style;
    regenerateForm.source_job_id = modalJob.value.id;

    const route = propertiesRoutes.glowup.jobs.store.url({
        property: numericPropertyId.value,
    });

    regenerateForm.post(route, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            refreshJobs();
            isResultOpen.value = true;
        },
    });
};

const handleAttach = (action: 'save_to_property' | 'add_to_report') => {
    if (!modalJob.value?.id) {
        return;
    }

    attachToProperty(modalJob.value.id, action, { onSuccess: refreshJobs });
};

const handleFileSelected = (file: File) => {
    setImage(file);
};

const downloadJob = (url: string | null) => {
    if (!url) {
        return;
    }
    const anchor = document.createElement('a');
    anchor.href = url;
    anchor.target = '_blank';
    anchor.rel = 'noreferrer';
    anchor.click();
};

const handleHistoryAction = (jobId: number, action: 'save_to_property' | 'add_to_report', isAttached: boolean) => {
    if (isAttached) {
        detachFromProperty(jobId, action, { onSuccess: refreshJobs });
        return;
    }

    attachToProperty(jobId, action, { onSuccess: refreshJobs });
};

const formatJobDate = (value?: string | null) => {
    if (!value) {
        return '';
    }
    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return '';
    }
    return parsed.toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

watch(
    () => jobs.value[0]?.id,
    (latestId) => {
        if (!isResultOpen.value || !latestId) {
            return;
        }

        if (latestId !== modalJobId.value) {
            modalJobId.value = latestId;
            setActiveJob(latestId);
        }
    },
);
</script>

<template>
    <section class="mt-5 flex flex-col gap-6 text-accent">
        <WorkspaceModuleHeader
            eyebrow="PixrGlowUp"
            title="Before / After AI Studio"
            description="Turn your photos into catalog-ready visuals for reports and clients."
        >
            <template #actions>
                <div class="flex items-start gap-3 rounded-[16px] bg-surface shadow-neu-in p-4">
                    <Sparkles class="h-5 w-5 text-[#6e33ff]" />
                    <div class="flex flex-col gap-2">
                        <p class="text-xs tracking-[0.35em] text-accent/40 uppercase">Monthly usage</p>
                        <p class="text-sm font-semibold text-accent">
                            <span v-if="!usage.is_unlimited">{{ usage.used }} / {{ usage.limit }} GlowUps</span>
                            <span v-else>{{ usage.used }} renders</span>
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    class="neu-button inline-flex w-fit items-center rounded-[12px] bg-background px-3 py-1 text-xs font-semibold text-accent/70 transition hover:text-accent"
                    @click="showHistory = !showHistory"
                >
                    {{ historyButtonLabel }}
                </button>
            </template>
        </WorkspaceModuleHeader>

        <Transition name="fade" mode="out-in">
            <GlowUpConfigurePanel
                v-if="!showHistory"
                key="configure"
                :room-options="roomOptions"
                :style-options="styleOptions"
                :max-upload="maxUpload"
                :preview-url="previewUrl"
                :selected-file-label="selectedFileLabel"
                :can-use-camera="canUseCamera"
                :is-generate-disabled="isGenerateDisabled"
                :is-uploading="isUploading"
                :limit-reached="limitReached"
                :room-type="createForm.room_type"
                :style="createForm.style"
                :user-instructions="createForm.user_instructions"
                :errors="createForm.errors"
                @update:room-type="(value) => (createForm.room_type = value)"
                @update:style="(value) => (createForm.style = value)"
                @update:user-instructions="(value) => (createForm.user_instructions = value)"
                @generate="handleGenerate"
                @capture="captureFromCamera"
                @file-selected="handleFileSelected"
                @refresh="refreshJobs"
            />
            <section v-else key="history" class="npo-form-shadow flex flex-col gap-5 rounded-[22px] bg-surface p-4">
                <div class="space-y-2">
                    <p class="text-xs font-semibold tracking-[0.35em] text-accent/40 uppercase">Processed history</p>
                    <h3 class="text-lg font-semibold text-accent">GlowUp results</h3>
                    <p class="text-sm text-accent/50">Review every processed image with before/after sliders.</p>
                </div>

                <div v-if="processedJobs.length" class="grid gap-6 md:grid-cols-2">
                    <article
                        v-for="job in processedJobs"
                        :key="job.id"
                        class="rounded-[18px] bg-background p-4 shadow-neu-in"
                    >
                        <GlowUpResultSlider :before="job.before_url" :after="job.after_url ?? job.before_url" />
                        <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-accent/50">
                            <span>{{ formatRoomLabel(job.room_type) }} · {{ job.style }}</span>
                            <span v-if="job.created_at">{{ formatJobDate(job.created_at) }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-semibold tracking-wide uppercase">
                            <span
                                v-if="getAttachmentState(job.id).save_to_property"
                                class="rounded-full bg-primary/50 px-2 py-1 text-white"
                            >
                                Attached
                            </span>
                            <span
                                v-if="getAttachmentState(job.id).add_to_report"
                                class="rounded-full bg-primary/60 px-2 py-1 text-white"
                            >
                                In report
                            </span>
                            <span
                                v-if="
                                    !getAttachmentState(job.id).save_to_property &&
                                    !getAttachmentState(job.id).add_to_report
                                "
                                class="rounded-[12px] bg-surface px-4 py-2 text-accent/50 shadow-neu-in"
                            >
                                Not attached
                            </span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                            <button
                                type="button"
                                class="neu-button inline-flex items-center rounded-[12px] bg-surface px-3 py-2 text-accent/80 transition hover:text-accent disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="attachForm.processing"
                                @click="
                                    handleHistoryAction(
                                        job.id,
                                        'save_to_property',
                                        getAttachmentState(job.id).save_to_property,
                                    )
                                "
                            >
                                {{ getAttachmentState(job.id).save_to_property ? 'Detach image' : 'Attach image' }}
                            </button>
                            <button
                                type="button"
                                class="neu-button inline-flex items-center rounded-full bg-surface px-3 py-2 text-accent/80 transition hover:text-accent disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="attachForm.processing"
                                @click="
                                    handleHistoryAction(
                                        job.id,
                                        'add_to_report',
                                        getAttachmentState(job.id).add_to_report,
                                    )
                                "
                            >
                                {{ getAttachmentState(job.id).add_to_report ? 'Remove from report' : 'Add to report' }}
                            </button>
                        </div>
                    </article>
                </div>
                <div v-else class="rounded-[16px] bg-background p-4 text-sm text-accent/60 shadow-neu-in">
                    No processed GlowUps yet. Generate a result to see it here.
                </div>
            </section>
        </Transition>

        <GlowUpResultModal
            :open="isResultOpen"
            :job="modalJob"
            :room-options="roomOptions"
            :style-options="styleOptions"
            :regenerating="regenerateForm.processing"
            :attaching="attachForm.processing"
            :errors="regenerateForm.errors"
            @close="isResultOpen = false"
            @download="downloadJob"
            @regenerate="handleRegenerate"
            @attach="handleAttach"
        />
    </section>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 200ms ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
