<script setup lang="ts">
import type { GlowUpJob, GlowUpOptionItem } from '@/components/properties/workspace/types';
import { Download, X } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted } from 'vue';
import GlowUpResultSlider from './GlowUpResultSlider.vue';
import GlowUpRegeneratePanel from './GlowUpRegeneratePanel.vue';
import { statusTokens } from './glowupConstants';

interface Props {
    open: boolean;
    job: GlowUpJob | null;
    roomOptions: GlowUpOptionItem[];
    styleOptions: GlowUpOptionItem[];
    regenerating: boolean;
    errors: Record<string, string | undefined>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'download', url: string | null): void;
    (e: 'regenerate', payload: { room_type: string; style: string; prompt: string }): void;
}>();

const token = computed(() => {
    const status = props.job?.status ?? 'pending';
    return statusTokens[status] ?? statusTokens.pending;
});

const hasResult = computed(() =>
    Boolean(props.job?.after_url && props.job?.status === 'done'),
);

const beforeUrl = computed(() => props.job?.before_url ?? null);
const afterUrl = computed(() => props.job?.after_url ?? props.job?.before_url ?? null);

const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
        emit('close');
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <teleport to="body">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="emit('close')" />
            <div
                class="relative w-full max-w-3xl rounded-[20px] bg-surface p-6 text-accent shadow-[0_20px_50px_rgba(15,23,42,0.55)]"
            >
                <header class="flex items-start justify-between gap-4">
                    <div class="space-y-2">
                        <p class="text-xs font-semibold tracking-[0.3em] text-accent/40 uppercase">
                            GlowUp status
                        </p>
                        <h3 class="text-lg font-semibold text-accent">{{ token.label }}</h3>
                        <p class="text-sm text-accent/50">{{ token.copy }}</p>
                    </div>
                    <button
                        type="button"
                        class="rounded-full bg-background p-2 text-accent/60 shadow-neu-in"
                        @click="emit('close')"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </header>

                <div class="mt-5 rounded-[16px] bg-background p-4 shadow-neu-in">
                    <div v-if="hasResult" class="space-y-4">
                        <GlowUpResultSlider
                            v-if="beforeUrl"
                            :before="beforeUrl"
                            :after="afterUrl"
                            label="Move the slider to compare"
                        />
                    </div>
                    <div v-else class="space-y-4">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="h-2 w-2 rounded-full" :class="token.dot" />
                            <span class="text-accent/70">
                                {{ props.job?.status ?? 'Starting' }}
                            </span>
                        </div>
                        <div class="h-2 rounded-full bg-surface/60">
                            <div
                                class="h-2 rounded-full bg-[#6e33ff] transition-all"
                                :style="{ width: `${props.job?.progress ?? 35}%` }"
                            />
                        </div>
                        <p v-if="props.job?.error_message" class="text-xs text-[#EA5455]">
                            {{ props.job.error_message }}
                        </p>
                    </div>
                </div>

                <div v-if="hasResult" class="mt-6 rounded-[16px] bg-background p-4 shadow-neu-in">
                    <GlowUpRegeneratePanel
                        :room-options="roomOptions"
                        :style-options="styleOptions"
                        :initial-room-type="job?.room_type ?? null"
                        :initial-style="job?.style ?? null"
                        :seed-key="job?.id ?? null"
                        :processing="regenerating"
                        :errors="errors"
                        @regenerate="emit('regenerate', $event)"
                    />
                </div>

                <footer class="mt-5 flex flex-wrap items-center justify-between gap-3">
                    <div
                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                        :class="token.badge"
                    >
                        <span class="h-2 w-2 rounded-full" :class="token.dot" />
                        {{ props.job?.status ?? 'pending' }}
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-[14px] bg-[#6e33ff] px-4 py-2 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(110,51,255,0.35)] disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="!hasResult"
                        @click="emit('download', afterUrl)"
                    >
                        <Download class="h-4 w-4" />
                        Download
                    </button>
                </footer>
            </div>
        </div>
    </teleport>
</template>
