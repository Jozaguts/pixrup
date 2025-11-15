<script setup lang="ts">
import type { Component } from 'vue';
import { computed } from 'vue';

interface Props {
    icon: Component;
    title: string;
    message: string;
    ctaLabel: string;
    variant?: 'default' | 'error';
    hint?: string;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
    hint: undefined,
});

const emit = defineEmits<{
    (e: 'action'): void;
}>();

const badgeClasses = computed(() => {
    if (props.variant === 'error') {
        return 'bg-[#fee2e2] text-[#b91c1c]';
    }
    return 'bg-white/80 text-[#7c4dff]';
});
</script>

<template>
    <div
        class="flex flex-col items-center justify-center gap-4 rounded-[28px] bg-gradient-to-b from-[#ede9ff] via-white to-white px-8 py-12 text-center text-[#1f2937] shadow-[0_18px_55px_rgba(124,77,255,0.08)]"
    >
        <div
            class="flex size-16 items-center justify-center rounded-2xl bg-white/90 shadow-[0_12px_30px_rgba(124,77,255,0.18)]"
        >
            <component
                :is="icon"
                class="size-7 text-[#7c4dff]"
                aria-hidden="true"
            />
        </div>
        <div>
            <p class="text-lg font-semibold">{{ title }}</p>
            <p class="mt-2 text-sm text-gray-500">
                {{ message }}
            </p>
        </div>
        <p
            v-if="hint"
            class="rounded-full bg-white/70 px-4 py-1 text-xs font-medium text-gray-500"
        >
            {{ hint }}
        </p>
        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-[18px] px-5 py-2 text-sm font-semibold shadow-md transition focus-visible:ring-2 focus-visible:ring-[#7c4dff]/50"
            :class="[
                variant === 'error'
                    ? 'bg-[#f87171] text-white'
                    : 'bg-[#7c4dff] text-white',
            ]"
            @click="emit('action')"
        >
            {{ ctaLabel }}
        </button>
        <span
            class="text-xs font-semibold uppercase tracking-[0.25em]"
            :class="badgeClasses"
        >
            pixr spyhunt
        </span>
    </div>
</template>
