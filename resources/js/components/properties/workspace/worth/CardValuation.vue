<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    value?: number | null;
    valueLow?: number | null;
    valueHigh?: number | null;
    confidence?: number | null;
    fetchedAt?: string | null;
    isStale?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    value: null,
    valueLow: null,
    valueHigh: null,
    confidence: null,
    fetchedAt: null,
    isStale: false,
});

const formatCurrency = (amount: number | null | undefined) => {
    if (amount === null || amount === undefined) {
        return '—';
    }

    return `$${Intl.NumberFormat('en-US', {
        maximumFractionDigits: 0,
    }).format(amount)}`;
};

const formattedValue = computed(() => formatCurrency(props.value));

const formattedRange = computed(() => {
    if (props.valueLow === null && props.valueHigh === null) {
        return null;
    }

    return `${formatCurrency(props.valueLow)} – ${formatCurrency(
        props.valueHigh,
    )}`;
});

const formattedConfidence = computed(() => {
    if (props.confidence === null || props.confidence === undefined) {
        return '—';
    }

    return `${Intl.NumberFormat('en-US', {
        maximumFractionDigits: 1,
    }).format(props.confidence)}%`;
});

const fetchedCopy = computed(() => {
    if (!props.fetchedAt) {
        return 'Awaiting first fetch';
    }

    const date = new Date(props.fetchedAt);
    return `Last fetched at ${date.toLocaleString(undefined, {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })}`;
});

const statusLabel = computed(() =>
    props.isStale ? 'Cached appraisal' : 'Live appraisal',
);
</script>

<template>
    <section
        class="flex flex-col gap-4 neu-surface rounded-[26px] p-6 shadow-neu-out transition-all duration-200 ease-in-out"
    >
        <header
            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
        >
            <p
                class="text-xs font-semibold tracking-[0.32em] text-[#7c4dff] uppercase"
            >
                Valuation
            </p>
            <span
                class="inline-flex items-center gap-2 rounded-full bg-[#f4f5fa] px-3 py-1 text-xs font-semibold text-[#6b7280] shadow-[inset_4px_4px_12px_rgba(204,206,214,0.6),inset_-4px_-4px_12px_rgba(255,255,255,0.9)]"
            >
                <span
                    class="inline-flex h-2 w-2 rounded-full"
                    :class="props.isStale ? 'bg-[#f59e0b]' : 'bg-[#1dbf7a]'"
                />
                {{ statusLabel }}
            </span>
        </header>

        <div class="flex flex-col gap-3">
            <p class="text-4xl font-semibold text-[#0d0d12]">
                {{ formattedValue }}
            </p>

            <p v-if="formattedRange" class="text-sm text-[#6b7280]">
                Range {{ formattedRange }}
            </p>
        </div>

        <footer
            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
        >
            <p class="text-sm text-[#6b7280]">
                {{ fetchedCopy }}
            </p>
            <span
                class="inline-flex items-center gap-2 rounded-[18px] bg-white px-4 py-2 text-xs font-semibold text-[#0d0d12] shadow-[8px_8px_20px_rgba(210,212,226,0.5),-8px_-8px_20px_rgba(255,255,255,0.95)]"
            >
                Confidence
                <span class="text-[#7c4dff]">{{ formattedConfidence }}</span>
            </span>
        </footer>
    </section>
</template>
