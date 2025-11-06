<script setup lang="ts">
import { computed } from 'vue';
import type { WorthTrendPoint } from '../types';

interface Props {
    points?: WorthTrendPoint[];
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    points: () => [],
    title: 'Market momentum (90 days)',
});

const normalized = computed(() => {
    if (props.points.length === 0) {
        return [];
    }

    const maxValue = Math.max(
        ...props.points.map((point) => point.value ?? 0),
        0,
    );

    const safeMax = maxValue <= 0 ? 1 : maxValue;

    return props.points.map((point, index) => ({
        id: point.label ?? `trend-${index}`,
        label: point.label ?? `P${index + 1}`,
        height: Math.max(16, (point.value / safeMax) * 72),
        value: point.value,
    }));
});
</script>

<template>
    <section
        class="neu-surface flex flex-col gap-4 rounded-[26px] p-6 shadow-neu-out transition-all duration-200 ease-in-out"
    >
        <header class="flex items-center justify-between">
            <div>
                <p
                    class="text-xs font-semibold tracking-[0.3em] text-[#6b7280] uppercase"
                >
                    Analytics
                </p>
                <h3 class="text-lg font-semibold text-[#0d0d12]">
                    {{ props.title }}
                </h3>
            </div>
        </header>

        <div
            v-if="normalized.length"
            class="grid h-40 grid-cols-[repeat(auto-fit,minmax(36px,1fr))] items-end gap-3"
        >
            <div
                v-for="point in normalized"
                :key="point.id"
                class="flex flex-col items-center justify-end gap-3 text-xs tracking-[0.2em] text-[#6b7280] uppercase"
            >
                <div
                    class="w-full rounded-full bg-gradient-to-b from-[#7c4dff] to-[#16b1ff] shadow-[4px_8px_18px_rgba(124,77,255,0.25)] transition-all duration-200 ease-in-out"
                    :style="{ height: `${point.height}px` }"
                />
                <span>{{ point.label }}</span>
            </div>
        </div>
        <p
            v-else
            class="rounded-[22px] bg-[#f4f5fa] px-4 py-6 text-sm text-[#6b7280] shadow-[inset_10px_10px_24px_rgba(210,212,226,0.55),inset_-10px_-10px_24px_rgba(255,255,255,0.95)]"
        >
            Trend data is not available yet. Fetch a valuation to unlock market
            momentum analytics.
        </p>
    </section>
</template>
