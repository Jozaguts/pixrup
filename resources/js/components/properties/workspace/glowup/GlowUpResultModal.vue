<script setup lang="ts">
import type { GlowUpJob, GlowUpOptionItem } from '@/components/properties/workspace/types';
import { Download, FileText, Paperclip, Sparkles, X } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import GlowUpResultSlider from './GlowUpResultSlider.vue';
import GlowUpRegeneratePanel from './GlowUpRegeneratePanel.vue';
import { statusTokens } from './glowupConstants';

interface Props {
    open: boolean;
    job: GlowUpJob | null;
    roomOptions: GlowUpOptionItem[];
    styleOptions: GlowUpOptionItem[];
    regenerating: boolean;
    attaching: boolean;
    errors: Record<string, string | undefined>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'download', url: string | null): void;
    (e: 'regenerate', payload: { room_type: string; style: string }): void;
    (e: 'attach', action: 'save_to_property' | 'add_to_report'): void;
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
const regenState = ref<{
    isDirty: boolean;
    canRegenerate: boolean;
    payload: { room_type: string; style: string };
} | null>(null);

const canAttach = computed(() => hasResult.value && !props.attaching);
const canRegenerate = computed(
    () =>
        Boolean(
            hasResult.value &&
                regenState.value?.isDirty &&
                regenState.value?.canRegenerate &&
                !props.regenerating,
        ),
);
const actionLabel = computed(() =>
    hasResult.value && regenState.value?.isDirty ? 'Regenerate GlowUp' : 'Download',
);
const actionIcon = computed(() =>
    hasResult.value && regenState.value?.isDirty ? Sparkles : Download,
);
const actionDisabled = computed(() => {
    if (!hasResult.value) {
        return true;
    }
    if (regenState.value?.isDirty) {
        return !canRegenerate.value;
    }
    return false;
});

const handleRegenStateChange = (
    state: { isDirty: boolean; canRegenerate: boolean; payload: { room_type: string; style: string } },
) => {
    regenState.value = state;
};

const handlePrimaryAction = () => {
    if (!hasResult.value) {
        return;
    }

    if (regenState.value?.isDirty) {
        if (!canRegenerate.value) {
            return;
        }
        emit('regenerate', regenState.value.payload);
        return;
    }

    emit('download', afterUrl.value);
};

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
                class="relative flex w-full max-w-3xl flex-col overflow-hidden rounded-[20px] bg-surface p-6 text-accent shadow-[0_20px_50px_rgba(15,23,42,0.55)] max-h-[90vh]"
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

                <div class="mt-5 flex-1 overflow-y-auto pr-1">
                    <div class="rounded-[16px] bg-background p-4 shadow-neu-in">
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
                                    class="h-2 animate-pulse rounded-full bg-primary/50 transition-all"
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
                            @state-change="handleRegenStateChange"
                        />
                    </div>
                </div>

                <footer class="mt-5 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <div
                            class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                            :class="token.badge"
                        >
                            <span class="h-2 w-2 rounded-full" :class="token.dot" />
                            {{ props.job?.status ?? 'pending' }}
                        </div>
                        <button
                            v-if="hasResult"
                            type="button"
                            class="neu-button inline-flex items-center gap-2 rounded-[14px] bg-surface px-4 py-2 text-sm font-semibold text-accent  transition hover:shadow-[inset_3px_3px_8px_rgba(0,0,0,0.25),inset_-3px_-3px_8px_rgba(255,255,255,0.08)] disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="!canAttach"
                            @click="emit('attach', 'save_to_property')"
                        >
                            <Paperclip class="h-4 w-4 text-[#6e33ff]" />
                            Attach image
                        </button>
                        <button
                            v-if="hasResult"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-[14px] bg-surface px-4 py-2 text-sm font-semibold text-accent neu-button transition hover:shadow-[inset_3px_3px_8px_rgba(0,0,0,0.25),inset_-3px_-3px_8px_rgba(255,255,255,0.08)] disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="!canAttach"
                            @click="emit('attach', 'add_to_report')"
                        >
                            <FileText class="h-4 w-4 text-[#6e33ff]" />
                            Add to report
                        </button>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-[14px] bg-primary/50 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="actionDisabled"
                        @click="handlePrimaryAction"
                    >
                        <component :is="actionIcon" class="h-4 w-4" />
                        {{ actionLabel }}
                    </button>
                </footer>
            </div>
        </div>
    </teleport>
</template>
