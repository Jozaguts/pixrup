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
        class="flex flex-col gap-4  rounded-[12px] transition-all duration-200 ease-in-out"
    >
        <header
            class="flex flex-col"
        >
            <p
                class="text-accent font-semibold tracking-[0.32em]  uppercase"
            >
                Valuation
            </p>
            <small class="text-xs text-accent/50">
                {{ fetchedCopy }}
            </small>
          <div class="flex gap-2 mt-2">
                <span
                    class="shadow-neu-in active inline-flex items-center rounded-sm px-2 py-1 text-xs font-medium text-accent ring ring-white"
                >
                <span
                    class="inline-flex h-2 w-2 rounded-full mx-2"
                    :class="props.isStale ? 'bg-[#f59e0b]' : 'bg-[#1dbf7a]'"
                />
                {{ statusLabel }}
            </span>
              <span
                  class="shadow-neu-in active inline-flex items-center rounded-sm px-2 py-1 text-xs font-medium text-accent ring ring-white"
              >
                Confidence
                <span class="mx-2">{{ formattedConfidence }}</span>
            </span>
          </div>
        </header>

        <div class="flex flex-col gap-3 mt-4">
            <p class="text-4xl font-semibold text-accent shadow-neu-in rounded-[12px] p-4">
                {{ formattedValue }}
            </p>

            <p v-if="formattedRange" class="text-sm text-accent/50">
                Range {{ formattedRange }}
            </p>
        </div>
    </section>
</template>
