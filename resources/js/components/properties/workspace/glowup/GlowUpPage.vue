<script setup lang="ts">
import { useGlowUpJobs } from '@/composables/useGlowUpJobs';
import type { GlowUpState } from '@/components/properties/workspace/types';
import { Camera, CloudUpload, Download, FileText, Loader2, RefreshCw, Save, ShieldAlert, Sparkles } from 'lucide-vue-next';
import { computed, ref, watch, onBeforeUnmount } from 'vue';
import GlowUpResultSlider from './GlowUpResultSlider.vue';
import { TextArea } from '@/components/ui/textarea';
import { buildPrompt, type RoomType, type Style } from '@/lib/glowupPrompt';
import { gsap } from '@/lib/gsap';

interface Props {
    propertyId: number | string;
    glowUp?: GlowUpState | null;
}

const props = defineProps<Props>();

const glowUpState = computed(() => props.glowUp ?? null);
const numericPropertyId = computed(() => Number(props.propertyId));

const {
    jobs,
    activeJob,
    activeJobId,
    latestCompletedJob,
    createForm,
    attachForm,
    submitJob,
    attachToProperty,
    refreshJobs,
    setActiveJob,
    setImage,
    captureFromCamera,
    previewUrl,
    canUseCamera,
    isUploading,
    limitReached,
    remaining,
    usage,
} = useGlowUpJobs({
    propertyId: numericPropertyId.value,
    glowUp: glowUpState,
});

const dropRef = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);

const roomOptions = computed(
    () => glowUpState.value?.options?.room_types ?? defaultRoomTypes,
);
const styleOptions = computed(
    () => glowUpState.value?.options?.styles ?? defaultStyleOptions,
);

const maxUpload = computed(
    () => glowUpState.value?.limits?.max_upload_size_mb ?? 10,
);

const usageProgress = computed(() => {
    if (!usage.limit || usage.limit <= 0) {
        return 0;
    }

    return Math.min(100, Math.round((usage.used / usage.limit) * 100));
});

const selectedFileLabel = computed(
    () => createForm.image?.name ?? 'Select an image',
);

const promptDraft = ref('');
const promptDirty = ref(false);
const autoPromptActive = ref(false);
let typingTween: gsap.core.Tween | null = null;
const skeletonActive = ref(false);
let skeletonTimeout: number | null = null;

const canShowPrompt = computed(
    () =>
        Boolean(createForm.image && createForm.room_type && createForm.style),
);

const stopTyping = () => {
    if (typingTween) {
        typingTween.kill();
        typingTween = null;
    }
};

const stopSkeleton = () => {
    if (skeletonTimeout !== null) {
        window.clearTimeout(skeletonTimeout);
        skeletonTimeout = null;
    }
    skeletonActive.value = false;
};

const triggerSkeleton = (duration: number) => {
    stopSkeleton();
    skeletonActive.value = true;
    skeletonTimeout = window.setTimeout(() => {
        skeletonActive.value = false;
        skeletonTimeout = null;
    }, duration * 1000);
};

const applyPromptWithTyping = (text: string) => {
    const trimmed = text.trim();
    stopTyping();
    stopSkeleton();

    if (!trimmed) {
        autoPromptActive.value = false;
        promptDraft.value = '';
        createForm.prompt = '';
        promptDirty.value = false;
        return;
    }

    autoPromptActive.value = true;
    const state = { progress: 0 };
    const target = trimmed.length;
    const duration = Math.min(7, Math.max(0.8, target / 30));
    triggerSkeleton(duration);

    typingTween = gsap.to(state, {
        progress: target,
        duration,
        ease: 'none',
        onUpdate: () => {
            promptDraft.value = trimmed.slice(0, Math.round(state.progress));
        },
        onComplete: () => {
            promptDraft.value = trimmed;
            autoPromptActive.value = false;
            typingTween = null;
        },
    });
};

watch(
    [() => createForm.room_type, () => createForm.style, () => createForm.image],
    ([room, style, image], [prevRoom, prevStyle, prevImage]) => {
        if (!room || !style || !image) {
            stopTyping();
            stopSkeleton();
            autoPromptActive.value = false;
            promptDirty.value = false;
            promptDraft.value = '';
            createForm.prompt = '';
            return;
        }

        if (
            room !== prevRoom ||
            style !== prevStyle ||
            image !== prevImage
        ) {
            promptDirty.value = false;
        }

        if (!promptDirty.value) {
            const { positive } = buildPrompt({
                room: room as RoomType,
                style: style as Style,
                includeNegatives: false,
            });
            promptDirty.value = false;
            applyPromptWithTyping(positive);
        }
    },
    { immediate: true },
);

watch(
    () => promptDraft.value,
    (value) => {
        createForm.prompt = value;
        if (!autoPromptActive.value) {
            promptDirty.value = true;
        }
    },
);

const regeneratePrompt = () => {
    if (!canShowPrompt.value || !createForm.room_type || !createForm.style) {
        return;
    }

    promptDirty.value = false;
    const { positive } = buildPrompt({
        room: createForm.room_type as RoomType,
        style: createForm.style as Style,
        includeNegatives: false,
    });
    applyPromptWithTyping(positive);
};

const handlePromptInput = (value: string) => {
    stopTyping();
    stopSkeleton();
    autoPromptActive.value = false;
    promptDraft.value = value;
};

const promptHelper = computed(() =>
    canShowPrompt.value
        ? 'Modify the prompt if you want to guide the AI differently.'
        : 'Add an image, room type, and style to preview the prompt.',
);

onBeforeUnmount(() => {
    stopTyping();
    stopSkeleton();
});

const isGenerateDisabled = computed(() => {
    if (limitReached.value || isUploading.value) {
        return true;
    }

    return !createForm.image || !createForm.prompt?.trim();
});

const statusTokens: Record<
    string,
    { label: string; badge: string; dot: string; copy: string }
> = {
    pending: {
        label: 'Queued',
        badge: 'bg-[#FFF4DA] text-[#9A6B00]',
        dot: 'bg-[#FFB74A]',
        copy: 'Preparing your scene for processing.',
    },
    processing: {
        label: 'Processing',
        badge: 'bg-[#E9EDFF] text-[#2E3A8C]',
        dot: 'bg-[#4C5FD5]',
        copy: 'Applying materials, color, and post-production.',
    },
    done: {
        label: 'Ready',
        badge: 'bg-[#E4F9F0] text-[#0B6B4F]',
        dot: 'bg-[#1DBE78]',
        copy: 'The render is ready to use.',
    },
    error: {
        label: 'Error',
        badge: 'bg-[#FFE6E6] text-[#9A1B1B]',
        dot: 'bg-[#EA5455]',
        copy: 'Something went wrong with the provider. Try again.',
    },
};

const handleBrowse = () => {
    dropRef.value?.click();
};

const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const [file] = Array.from(input.files ?? []);
    if (file) {
        setImage(file);
    }
};

const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    const [file] = Array.from(event.dataTransfer?.files ?? []);
    if (file) {
        setImage(file);
    }
    isDragging.value = false;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const downloadJob = (url: string | null) => {
    if (!url) return;
    const anchor = document.createElement('a');
    anchor.href = url;
    anchor.target = '_blank';
    anchor.rel = 'noreferrer';
    anchor.click();
};

const defaultRoomTypes = [
    { value: 'living_room', label: 'Living room' },
    { value: 'kitchen', label: 'Kitchen' },
    { value: 'bathroom', label: 'Bathroom' },
    { value: 'bedroom', label: 'Bedroom' },
    { value: 'facade', label: 'Facade' },
];

const defaultStyleOptions = [
    { value: 'modern', label: 'Modern' },
    { value: 'minimalist', label: 'Minimalist' },
    { value: 'luxury', label: 'Luxury' },
    { value: 'rustic', label: 'Rustic' },
    { value: 'outdoor_resort', label: 'Outdoor resort' },
];

const formatDate = (input?: string | null) => {
    if (!input) {
        return '—';
    }

    const date = new Date(input);

    if (Number.isNaN(date.getTime())) {
        return '—';
    }

    return date.toLocaleString();
};
</script>

<template>
    <section class="flex flex-col gap-6">
        <header
            class="neu-surface flex flex-col gap-4 rounded-3xl p-6 shadow-neu-out md:flex-row md:items-center md:justify-between"
        >
            <div>
                <p class="text-xs font-semibold tracking-[0.35em] text-gray-400 uppercase">
                    PixrGlowUp
                </p>
                <h2 class="mt-1 text-2xl font-semibold text-[#1f2937]">
                    Before / After AI Studio
                </h2>
                <p class="text-sm text-gray-500">
                    Turn your photos into catalog-ready visuals for reports and clients.
                </p>
            </div>
            <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 shadow-neu-in">
                <Sparkles class="h-5 w-5 text-[#7c4dff]" />
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-gray-400">
                        Monthly usage
                    </p>
                    <p class="text-sm font-semibold text-[#1f2937]">
                        <span v-if="usage.limit !== null"
                            >{{ usage.used }} / {{ usage.limit }} GlowUps</span
                        >
                        <span v-else>{{ usage.used }} renders</span>
                    </p>
                </div>
            </div>
        </header>

        <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr] h-[calc(100vh-120px)]">
            <div class="space-y-6 min-h-0 overflow-y-auto custom-scroll">
                <article class="neu-surface space-y-6 rounded-3xl p-6 shadow-neu-out">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold tracking-[0.35em] text-gray-400 uppercase">
                                1. Configure the space
                            </p>
                            <h3 class="text-lg font-semibold text-[#1f2937]">
                                Upload or capture a photo
                            </h3>
                            <p class="text-sm text-gray-500">
                                Supported formats JPG/PNG ({{ maxUpload }}MB). Choose a room and style for the AI.
                            </p>
                        </div>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-2xl border border-white/60 px-4 py-2 text-sm font-semibold text-[#7c4dff] cursor-pointer"
                            @click="refreshJobs"
                        >
                            <RefreshCw class="h-4 w-4" />
                            Refresh
                        </button>
                    </div>

                    <div
                        class="flex flex-col gap-4 rounded-2xl border-2 border-dashed border-[#d1d5db] bg-white/80 p-6 text-center transition hover:border-[#7c4dff] hover:bg-white"
                        :class="{
                            'border-[#7c4dff] bg-white': isDragging,
                        }"
                        @dragover="handleDragOver"
                        @drop="handleDrop"
                        @dragleave="handleDragLeave"
                    >
                        <CloudUpload class="mx-auto h-10 w-10 text-[#7c4dff]" />
                        <p class="text-base font-semibold text-[#1f2937]">
                            {{ selectedFileLabel }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Drag your photo here or
                            <button
                                type="button"
                                class="text-[#7c4dff]"
                                @click="handleBrowse"
                            >
                                browse your files
                            </button>
                        </p>
                        <input
                            ref="dropRef"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleFileChange"
                        />
                        <p v-if="createForm.errors.image" class="text-sm text-red-500">
                            {{ createForm.errors.image }}
                        </p>
                        <div
                            v-if="previewUrl"
                            class="mx-auto mt-3 flex w-full max-w-md flex-col items-center gap-3"
                        >
                            <img
                                :src="previewUrl"
                                alt="Preview"
                                class="h-40 w-full rounded-2xl object-cover shadow"
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 ">
                        <label class="flex flex-col gap-2 text-sm text-gray-500">
                            Room type
                            <select
                                v-model="createForm.room_type"
                                class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm text-[#1f2937] shadow-inner focus:border-[#7c4dff] focus:outline-none"
                            >
                                <option
                                    v-for="room in roomOptions"
                                    :key="room.value"
                                    :value="room.value"
                                >
                                    {{ room.label }}
                                </option>
                            </select>
                        </label>
                        <label class="flex flex-col gap-2 text-sm text-gray-500">
                            Desired style
                            <select
                                v-model="createForm.style"
                                class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm text-[#1f2937] shadow-inner focus:border-[#7c4dff] focus:outline-none"
                            >
                                <option
                                    v-for="style in styleOptions"
                                    :key="style.value"
                                    :value="style.value"
                                >
                                    {{ style.label }}
                                </option>
                            </select>
                        </label>
                        <div class="md:col-span-2 flex flex-col gap-3">
                            <div class="relative">
                                <TextArea
                                    :model-value="promptDraft"
                                    label="Prompt (editable)"
                                    :rows="6"
                                    placeholder="Prompt will appear once an image is selected."
                                    :disabled="!canShowPrompt"
                                    @update:model-value="handlePromptInput"
                                />
                                <div
                                    class="pointer-events-none top-[auto] absolute inset-1 h-[80%] rounded-[20px] transition-opacity duration-300 "
                                    :class="[
                                        skeletonActive ? 'opacity-90' : 'opacity-0',
                                        canShowPrompt ? 'visible' : 'invisible',
                                        'skeleton-overlay',
                                    ]"
                                />
                            </div>
                            <div class="flex flex-wrap items-center justify-between text-xs text-gray-500">
                                <span>{{ promptHelper }}</span>
                                <button
                                    type="button"
                                    class="font-semibold text-[#7c4dff] disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="!canShowPrompt"
                                    @click="regeneratePrompt"
                                >
                                    Regenerate prompt
                                </button>
                            </div>
                            <p
                                v-if="createForm.errors.prompt"
                                class="text-sm text-[#B91C1C]"
                            >
                                {{ createForm.errors.prompt }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-2xl bg-[#7c4dff] px-5 py-3 text-sm font-semibold text-white shadow-lg disabled:opacity-60"
                            :disabled="isGenerateDisabled"
                            @click="submitJob"
                        >
                            <Loader2
                                v-if="isUploading"
                                class="h-4 w-4 animate-spin"
                            />
                            <Sparkles v-else class="h-4 w-4" />
                            Generate GlowUp
                        </button>
                        <button
                            v-if="canUseCamera"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-2xl border border-[#7c4dff]/40 px-5 py-3 text-sm font-semibold text-[#7c4dff]"
                            @click="captureFromCamera"
                        >
                            <Camera class="h-4 w-4" />
                            Capture from camera
                        </button>
                        <p v-if="limitReached" class="flex items-center gap-2 text-sm text-[#9A1B1B]">
                            <ShieldAlert class="h-4 w-4" />
                            Limit reached. Upgrade your plan for more renders.
                        </p>
                    </div>

                    <div class="space-y-2 rounded-2xl bg-[#f9fafb] p-4">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Credit usage</span>
                            <span v-if="usage.limit !== null"
                                >{{ usage.used }} / {{ usage.limit }}</span
                            >
                            <span v-else>{{ usage.used }} renders</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-200">
                            <div
                                class="h-2 rounded-full bg-gradient-to-r from-[#7c4dff] to-[#c084fc] transition-all"
                                :style="{ width: `${usageProgress}%` }"
                            />
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ remaining === Infinity ? 'Unlimited usage.' : `${remaining} remaining this cycle.` }}
                        </p>
                    </div>
                </article>

                <article
                    v-if="activeJob && activeJob.status !== 'done'"
                    class="neu-surface space-y-4 rounded-3xl p-6 shadow-neu-out"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold tracking-[0.35em] text-gray-400 uppercase">
                                2. Processing
                            </p>
                            <h3 class="text-lg font-semibold text-[#1f2937]">
                                {{ statusTokens[activeJob.status]?.label ?? 'Status' }}
                            </h3>
                        </div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold"
                            :class="statusTokens[activeJob.status]?.badge"
                        >
                            <span
                                class="h-2 w-2 rounded-full"
                                :class="statusTokens[activeJob.status]?.dot"
                            />
                            {{ activeJob.status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">
                        {{ statusTokens[activeJob.status]?.copy }}
                    </p>
                    <div class="h-2 rounded-full bg-gray-100">
                        <div
                            class="h-2 rounded-full bg-[#7c4dff] transition-all"
                            :style="{ width: `${activeJob.progress ?? 45}%` }"
                        />
                    </div>
                </article>

                <article
                    v-if="latestCompletedJob"
                    class="neu-surface space-y-5 rounded-3xl p-6 shadow-neu-out"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold tracking-[0.35em] text-gray-400 uppercase">
                                3. Result
                            </p>
                            <h3 class="text-lg font-semibold text-[#1f2937]">
                                Before / After ready
                            </h3>
                        </div>
                        <span class="rounded-full bg-[#E4F9F0] px-4 py-1.5 text-xs font-semibold text-[#0B6B4F]">
                            {{ latestCompletedJob.room_type }}
                        </span>
                    </div>
                    <GlowUpResultSlider
                        :before="latestCompletedJob.before_url"
                        :after=" latestCompletedJob.after_url ?? latestCompletedJob.before_url"
                        label="Move the slider to compare"
                    />
                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-2xl bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white"
                            :disabled="attachForm.processing"
                            @click="attachToProperty(latestCompletedJob.id, 'save_to_property')"
                        >
                            <Save class="h-4 w-4" />
                            Save to property
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-2xl border border-[#7c4dff]/40 px-4 py-2 text-sm font-semibold text-[#7c4dff]"
                            :disabled="attachForm.processing"
                            @click="attachToProperty(latestCompletedJob.id, 'add_to_report')"
                        >
                            <FileText class="h-4 w-4" />
                            Add to report
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-2xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600"
                            @click="downloadJob(latestCompletedJob.after_url)"
                        >
                            <Download class="h-4 w-4" />
                            Download
                        </button>
                    </div>
                </article>
            </div>

            <aside class="min-h-0 overflow-hidden neu-surface space-y-4 rounded-3xl p-6 shadow-neu-out">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-[#1f2937]">
                        GlowUp history
                    </h3>
                    <button
                        type="button"
                        class="text-sm font-semibold text-[#7c4dff] cursor-pointer"
                        @click="refreshJobs"
                    >
                        Refresh
                    </button>
                </div>
                <div v-if="jobs.length === 0" class="rounded-2xl bg-[#f9fafb] p-6 text-center">
                    <p class="font-semibold text-[#1f2937]">No transformations yet</p>
                    <p class="text-sm text-gray-500">
                        Generate your first GlowUp to see progress here.
                    </p>
                </div>
                <ul v-else class="h-full overflow-y-auto space-y-4  custom-scroll">
                    <li
                        v-for="job in jobs"
                        :key="job.id"
                        :class="[
                            'rounded-2xl p-4 border transition cursor-pointer',
                            job.id === activeJobId ? 'border-[#7c4dff]/50 bg-white' : 'border-transparent bg-white/70',
                        ]"
                        @click="setActiveJob(job.id)"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-[#1f2937]">
                                    {{ job.room_type }} · {{ job.style }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ formatDate(job.created_at) }}
                                </p>
                            </div>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold"
                                :class="statusTokens[job.status]?.badge ?? 'bg-gray-100 text-gray-600'"
                            >
                                <span
                                    class="h-2 w-2 rounded-full"
                                    :class="statusTokens[job.status]?.dot ?? 'bg-gray-400'"
                                />
                                {{ statusTokens[job.status]?.label ?? job.status }}
                            </span>
                        </div>
                        <div class="mt-3 flex items-center gap-3">
                            <img
                                :src="job.before_url"
                                alt=""
                                class="h-16 w-16 rounded-xl object-cover"
                            />
                            <img
                                :src="job.after_url ?? job.before_url"
                                alt=""
                                class="h-16 w-16 rounded-xl object-cover"
                            />
                        </div>
                    </li>
                </ul>
            </aside>
        </div>
    </section>
</template>

<style scoped lang="postcss">
.custom-scroll {
    @apply bg-primary-400 overflow-y-auto rounded-md;
    scrollbar-width: thin;
    scrollbar-color: rgba(110,52,255, .50) var(--neu-surface);
    padding-right: 1rem;
    padding-bottom: 2rem;
}

.skeleton-overlay {
    background: linear-gradient(
        120deg,
        rgba(124, 77, 255, 0.15) 0%,
        rgba(235, 229, 255, 0.6) 50%,
        rgba(124, 77, 255, 0.15) 100%
    );
    background-size: 200% 100%;
    animation: glowupSkeleton 3.2s ease-in-out infinite;
}

@keyframes glowupSkeleton {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}
</style>
