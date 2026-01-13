<script setup lang="ts">
import type { GlowUpState } from '@/components/properties/workspace/types';
import { useGlowUpJobs } from '@/composables/useGlowUpJobs';
import { useGlowUpPrompt } from '@/composables/useGlowUpPrompt';
import propertiesRoutes from '@/routes/properties';
import { useForm } from '@inertiajs/vue3';
import { Sparkles } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import GlowUpConfigurePanel from './GlowUpConfigurePanel.vue';
import GlowUpResultModal from './GlowUpResultModal.vue';
import { defaultRoomTypes, defaultStyleOptions } from './glowupConstants';

interface Props {
    propertyId: number | string;
    glowUp?: GlowUpState | null;
}

const props = defineProps<Props>();

const glowUpState = computed(() => props.glowUp ?? null);
const numericPropertyId = computed(() => Number(props.propertyId));

const {
    jobs,
    createForm,
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

const selectedFileLabel = computed(() => createForm.image?.name ?? 'Select an image');

const {
    promptDraft,
    promptHelper,
    skeletonActive,
    canShowPrompt,
    regeneratePrompt,
    handlePromptInput,
} = useGlowUpPrompt(createForm);

const isGenerateDisabled = computed(() => {
    if (limitReached.value || isUploading.value) {
        return true;
    }

    return !createForm.image || !createForm.prompt?.trim();
});

const isResultOpen = ref(false);
const modalJobId = ref<number | null>(null);
const modalJob = computed(
    () => jobs.value.find((job) => job.id === modalJobId.value) ?? null,
);

const regenerateForm = useForm({
    room_type: null as string | null,
    style: null as string | null,
    prompt: '',
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

const handleRegenerate = (payload: { room_type: string; style: string; prompt: string }) => {
    if (!modalJob.value?.id) {
        return;
    }

    modalJobId.value = null;
    regenerateForm.clearErrors();
    regenerateForm.room_type = payload.room_type;
    regenerateForm.style = payload.style;
    regenerateForm.prompt = payload.prompt;
    regenerateForm.source_job_id = modalJob.value.id;

    const route = propertiesRoutes.glowup.jobs.store.url({
        property: numericPropertyId.value,
    });

    regenerateForm.post(route, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            isResultOpen.value = true;
        },
    });
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
    <section class="flex flex-col gap-6 text-accent mt-5">
        <header class="npo-form-shadow flex flex-col gap-4 rounded-[22px] p-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold tracking-[0.35em] text-accent/40 uppercase">PixrGlowUp</p>
                <h2 class="text-2xl font-semibold text-accent">Before / After AI Studio</h2>
                <p class="text-sm text-accent/50">
                    Turn your photos into catalog-ready visuals for reports and clients.
                </p>
            </div>
            <div class="flex items-center gap-3 rounded-[16px] bg-surface px-4 py-3 shadow-neu-in">
                <Sparkles class="h-5 w-5 text-[#6e33ff]" />
                <div>
                    <p class="text-xs tracking-[0.35em] text-accent/40 uppercase">Monthly usage</p>
                    <p class="text-sm font-semibold text-accent">
                        <span v-if="!usage.is_unlimited">{{ usage.used }} / {{ usage.limit }} GlowUps</span>
                        <span v-else>{{ usage.used }} renders</span>
                    </p>
                </div>
            </div>
        </header>

        <GlowUpConfigurePanel
            :room-options="roomOptions"
            :style-options="styleOptions"
            :max-upload="maxUpload"
            :preview-url="previewUrl"
            :selected-file-label="selectedFileLabel"
            :can-use-camera="canUseCamera"
            :is-generate-disabled="isGenerateDisabled"
            :is-uploading="isUploading"
            :limit-reached="limitReached"
            :prompt-draft="promptDraft"
            :prompt-helper="promptHelper"
            :skeleton-active="skeletonActive"
            :can-show-prompt="canShowPrompt"
            :room-type="createForm.room_type"
            :style="createForm.style"
            :errors="createForm.errors"
            @update:room-type="(value) => (createForm.room_type = value)"
            @update:style="(value) => (createForm.style = value)"
            @prompt-input="handlePromptInput"
            @regenerate="regeneratePrompt"
            @generate="handleGenerate"
            @capture="captureFromCamera"
            @file-selected="handleFileSelected"
            @refresh="refreshJobs"
        />

        <GlowUpResultModal
            :open="isResultOpen"
            :job="modalJob"
            :room-options="roomOptions"
            :style-options="styleOptions"
            :regenerating="regenerateForm.processing"
            :errors="regenerateForm.errors"
            @close="isResultOpen = false"
            @download="downloadJob"
            @regenerate="handleRegenerate"
        />
    </section>
</template>
