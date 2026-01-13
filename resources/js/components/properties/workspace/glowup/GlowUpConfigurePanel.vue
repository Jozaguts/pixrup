<script setup lang="ts">
import { TextArea } from '@/components/ui/textarea';
import type { GlowUpOptionItem } from '@/components/properties/workspace/types';
import { Camera, CloudUpload, Loader2, RefreshCw, ShieldAlert, Sparkles } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    roomOptions: GlowUpOptionItem[];
    styleOptions: GlowUpOptionItem[];
    maxUpload: number;
    previewUrl: string | null;
    selectedFileLabel: string;
    canUseCamera: boolean;
    isGenerateDisabled: boolean;
    isUploading: boolean;
    limitReached: boolean;
    promptDraft: string;
    promptHelper: string;
    skeletonActive: boolean;
    canShowPrompt: boolean;
    roomType: string | null;
    style: string | null;
    errors: Record<string, string | undefined>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:roomType', value: string | null): void;
    (e: 'update:style', value: string | null): void;
    (e: 'prompt-input', value: string): void;
    (e: 'regenerate'): void;
    (e: 'generate'): void;
    (e: 'capture'): void;
    (e: 'file-selected', file: File): void;
    (e: 'refresh'): void;
}>();

const fileRef = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);

const roomTypeModel = computed({
    get: () => props.roomType,
    set: (value) => emit('update:roomType', value),
});

const styleModel = computed({
    get: () => props.style,
    set: (value) => emit('update:style', value),
});

const handleBrowse = () => {
    fileRef.value?.click();
};

const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const [file] = Array.from(input.files ?? []);
    if (file) {
        emit('file-selected', file);
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
        emit('file-selected', file);
    }
    isDragging.value = false;
};

const handleDragLeave = () => {
    isDragging.value = false;
};
</script>

<template>
    <article class="npo-form-shadow flex flex-col gap-6 rounded-[18px] bg-surface p-6 text-accent">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-2">
                <p class="text-xs font-semibold tracking-[0.35em] text-accent/40 uppercase">
                    1. Configure the space
                </p>
                <h3 class="text-lg font-semibold text-accent">Upload or capture a photo</h3>
                <p class="text-sm text-accent/50">
                    Supported formats JPG/PNG ({{ maxUpload }}MB). Choose a room and style for the AI.
                </p>
            </div>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-[14px] bg-background px-4 py-2 text-xs font-semibold text-[#6e33ff] shadow-neu-in"
                @click="emit('refresh')"
            >
                <RefreshCw class="h-4 w-4" />
                Refresh
            </button>
        </header>

        <div
            class="flex flex-col gap-4 rounded-[18px] border border-white/10 bg-background p-5 text-center shadow-neu-in transition"
            :class="{ 'border-[#6e33ff] bg-background/80': isDragging }"
            @dragover="handleDragOver"
            @drop="handleDrop"
            @dragleave="handleDragLeave"
        >
            <CloudUpload class="mx-auto h-10 w-10 text-[#6e33ff]" />
            <div class="space-y-1">
                <p class="text-base font-semibold text-accent">{{ selectedFileLabel }}</p>
                <p class="text-sm text-accent/50">
                    Drag your photo here or
                    <button type="button" class="text-[#6e33ff]" @click="handleBrowse">
                        browse your files
                    </button>
                </p>
            </div>
            <input ref="fileRef" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
            <p v-if="errors.image" class="text-sm text-[#EA5455]">{{ errors.image }}</p>
            <div v-if="previewUrl" class="mx-auto mt-2 w-full max-w-md">
                <img
                    :src="previewUrl"
                    alt="Preview"
                    class="h-44 w-full rounded-[16px] object-cover shadow-neu-in"
                />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <label class="flex flex-col gap-2 text-sm text-accent/50">
                Room type
                <select
                    v-model="roomTypeModel"
                    class="w-full rounded-[14px] border border-white/10 bg-background px-4 py-3 text-sm text-accent shadow-neu-in focus:border-[#6e33ff] focus:outline-none"
                >
                    <option v-for="room in roomOptions" :key="room.value" :value="room.value">
                        {{ room.label }}
                    </option>
                </select>
            </label>
            <label class="flex flex-col gap-2 text-sm text-accent/50">
                Desired style
                <select
                    v-model="styleModel"
                    class="w-full rounded-[14px] border border-white/10 bg-background px-4 py-3 text-sm text-accent shadow-neu-in focus:border-[#6e33ff] focus:outline-none"
                >
                    <option v-for="style in styleOptions" :key="style.value" :value="style.value">
                        {{ style.label }}
                    </option>
                </select>
            </label>
        </div>

        <div class="flex flex-col gap-3">
            <div class="relative">
                <TextArea
                    :model-value="promptDraft"
                    label="Prompt (editable)"
                    :rows="6"
                    placeholder="Prompt will appear once an image is selected."
                    :disabled="!canShowPrompt"
                    @update:model-value="emit('prompt-input', $event)"
                />
                <div
                    class="pointer-events-none absolute inset-1 top-[auto] h-[80%] rounded-[20px] transition-opacity duration-300"
                    :class="[
                        skeletonActive ? 'opacity-90' : 'opacity-0',
                        canShowPrompt ? 'visible' : 'invisible',
                        'skeleton-overlay',
                    ]"
                />
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-accent/50">
                <span>{{ promptHelper }}</span>
                <button
                    type="button"
                    class="font-semibold text-[#6e33ff] disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!canShowPrompt"
                    @click="emit('regenerate')"
                >
                    Regenerate prompt
                </button>
            </div>
            <p v-if="errors.prompt" class="text-sm text-[#EA5455]">
                {{ errors.prompt }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-[16px] bg-[#6e33ff] px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(110,51,255,0.35)] disabled:opacity-60"
                :disabled="isGenerateDisabled"
                @click="emit('generate')"
            >
                <Sparkles v-if="!isUploading" class="h-4 w-4" />
                <Loader2 v-else class="h-4 w-4 animate-spin" />
                Generate GlowUp
            </button>
            <button
                v-if="canUseCamera"
                type="button"
                class="inline-flex items-center gap-2 rounded-[16px] border border-[#6e33ff]/40 bg-background px-5 py-3 text-sm font-semibold text-[#6e33ff] shadow-neu-in"
                @click="emit('capture')"
            >
                <Camera class="h-4 w-4" />
                Capture from camera
            </button>
            <p v-if="limitReached" class="flex items-center gap-2 text-sm text-[#EA5455]">
                <ShieldAlert class="h-4 w-4" />
                Limit reached. Upgrade your plan for more renders.
            </p>
        </div>
    </article>
</template>

<style scoped lang="postcss">
.skeleton-overlay {
    background: linear-gradient(
        120deg,
        rgba(110, 51, 255, 0.15) 0%,
        rgba(235, 229, 255, 0.6) 50%,
        rgba(110, 51, 255, 0.15) 100%
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
