<script setup lang="ts">
import { TextArea } from '@/components/ui/textarea';
import type { GlowUpOptionItem } from '@/components/properties/workspace/types';
import { buildPrompt, type RoomType, type Style } from '@/lib/glowupPrompt';
import { computed, ref, watch } from 'vue';

interface Props {
    roomOptions: GlowUpOptionItem[];
    styleOptions: GlowUpOptionItem[];
    initialRoomType: string | null;
    initialStyle: string | null;
    seedKey: number | string | null;
    processing: boolean;
    errors: Record<string, string | undefined>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'regenerate', payload: { room_type: string; style: string; prompt: string }): void;
}>();

const roomType = ref<string | null>(null);
const style = ref<string | null>(null);
const prompt = ref('');
const dirty = ref(false);

const canRegenerate = computed(
    () =>
        Boolean(roomType.value && style.value && prompt.value.trim().length >= 20) &&
        !props.processing,
);

const applyAutoPrompt = () => {
    if (!roomType.value || !style.value) {
        return;
    }

    const { positive } = buildPrompt({
        room: roomType.value as RoomType,
        style: style.value as Style,
        includeNegatives: false,
    });
    prompt.value = positive;
};

const resetPrompt = () => {
    dirty.value = false;
    applyAutoPrompt();
};

const handlePromptInput = (value: string) => {
    prompt.value = value;
    dirty.value = true;
};

const handleRegenerate = () => {
    if (!roomType.value || !style.value) {
        return;
    }

    emit('regenerate', {
        room_type: roomType.value,
        style: style.value,
        prompt: prompt.value.trim(),
    });
};

watch(
    () => props.seedKey,
    () => {
        roomType.value = props.initialRoomType ?? null;
        style.value = props.initialStyle ?? null;
        dirty.value = false;
        if (roomType.value && style.value) {
            applyAutoPrompt();
        } else {
            prompt.value = '';
        }
    },
    { immediate: true },
);

watch([roomType, style], () => {
    if (!dirty.value) {
        applyAutoPrompt();
    }
});
</script>

<template>
    <div class="space-y-4">
        <div>
            <p class="text-xs font-semibold tracking-[0.3em] text-accent/40 uppercase">Regenerate</p>
            <p class="text-sm text-accent/60">Try a different room type or style.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <label class="flex flex-col gap-2 text-xs text-accent/50">
                Room type
                <select
                    v-model="roomType"
                    class="w-full rounded-[12px] border border-white/10 bg-surface px-3 py-2 text-sm text-accent shadow-neu-in focus:border-[#6e33ff] focus:outline-none"
                >
                    <option v-for="room in roomOptions" :key="room.value" :value="room.value">
                        {{ room.label }}
                    </option>
                </select>
            </label>
            <label class="flex flex-col gap-2 text-xs text-accent/50">
                Style
                <select
                    v-model="style"
                    class="w-full rounded-[12px] border border-white/10 bg-surface px-3 py-2 text-sm text-accent shadow-neu-in focus:border-[#6e33ff] focus:outline-none"
                >
                    <option v-for="styleOption in styleOptions" :key="styleOption.value" :value="styleOption.value">
                        {{ styleOption.label }}
                    </option>
                </select>
            </label>
        </div>
        <div class="space-y-2">
            <TextArea
                :model-value="prompt"
                label="Prompt"
                :rows="4"
                placeholder="Prompt will update based on your selections."
                @update:model-value="handlePromptInput"
            />
            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-accent/50">
                <span>Adjust the prompt if you want a different render.</span>
                <button type="button" class="font-semibold text-[#6e33ff]" @click="resetPrompt">
                    Regenerate prompt
                </button>
            </div>
            <p v-if="errors.prompt" class="text-xs text-[#EA5455]">
                {{ errors.prompt }}
            </p>
        </div>
        <button
            type="button"
            class="inline-flex items-center justify-center rounded-[14px] bg-primary/50 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="!canRegenerate"
            @click="handleRegenerate"
        >
            Generate another GlowUp
        </button>
    </div>
</template>
