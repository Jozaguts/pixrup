<script setup lang="ts">
import { ShieldCheck, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    value: number;
    min: number;
    max: number;
    confidence: 'High' | 'Medium' | 'Low';
    sampleCount: number;
    radiusLabel: string;
    sourceNote: string;
}

const props = defineProps<Props>();

const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
});

const clamp = (value: number, min: number, max: number) =>
    Math.min(Math.max(value, min), max);

const progress = computed(() => {
    if (props.max === props.min) {
        return 50;
    }
    const bounded = clamp(props.value, props.min, props.max);
    return ((bounded - props.min) / (props.max - props.min)) * 100;
});

const confidenceBadge = computed(() => {
    switch (props.confidence) {
        case 'High':
            return 'bg-[#d1fae5] text-[#047857]';
        case 'Medium':
            return 'bg-[#fef3c7] text-[#92400e]';
        case 'Low':
            return 'bg-[#fee2e2] text-[#b91c1c]';
        default:
            return 'bg-gray-100 text-gray-700';
    }
});
</script>

<template>
    <div class="space-y-5 rounded-[28px] bg-white p-6 shadow-[0_12px_30px_rgba(15,23,42,0.08)]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-gray-400">
                    Value Estimate
                </p>
                <p class="text-2xl font-semibold text-[#111827]">
                    {{ formatter.format(value) }}
                </p>
            </div>
            <span
                class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                :class="confidenceBadge"
            >
                <ShieldCheck class="size-4" />
                {{ confidence }} confidence
            </span>
        </div>

        <div>
            <div class="flex items-center justify-between text-xs font-medium text-gray-500">
                <span>{{ formatter.format(min) }}</span>
                <span>{{ formatter.format(max) }}</span>
            </div>
            <div class="relative mt-2 h-3 rounded-full bg-[#f3f4f6]">
                <div
                    class="absolute h-full rounded-full bg-gradient-to-r from-[#a78bfa] via-[#7c4dff] to-[#5b21b6] transition-all"
                    :style="{ width: `${progress}%` }"
                />
            </div>
        </div>

        <div class="rounded-2xl bg-[#f7f4ff] p-4 text-sm text-gray-600">
            <p class="font-semibold text-[#111827]">
                Based on {{ sampleCount }} recent
                {{ sampleCount === 1 ? 'sale' : 'sales' }} within
                {{ radiusLabel }}.
            </p>
            <p class="mt-1 text-sm text-gray-600">
                {{ sourceNote }}
            </p>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-xs uppercase tracking-[0.3em] text-gray-400">
                Powered by PixrSpyHunt
            </p>
            <Sparkles class="size-5 text-[#7c4dff]" aria-hidden="true" />
        </div>
    </div>
</template>
