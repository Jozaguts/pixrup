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

const statusLabel = computed(() => (props.isStale ? 'Cached appraisal' : 'Live appraisal'));
const lowFormatValue = computed(() => formatCurrency(props.valueLow));
const highFormatValue = computed(() => formatCurrency(props.valueHigh));
</script>

<template>
    <section class="flex flex-col gap-4 rounded-[12px] p-4 shadow-neu-in transition-all duration-200 ease-in-out">
        <header class="flex flex-col">
            <p class="font-semibold tracking-[0.32em] text-accent uppercase">Valuation</p>
            <div class="flex">
                <span class="active inline-flex items-center rounded-sm py-1 text-xs font-medium text-accent">
                    <span
                        class="mx-2 inline-flex h-2 w-2"
                        :class="props.isStale ? 'bg-[#f59e0b]' : 'bg-[#1dbf7a]'"
                    />
                    {{ statusLabel }}
                </span>
            </div>
        </header>

        <div class="mt-4 flex flex-col gap-3 text-accent">
            <div class="flex justify-between">
                <p>Low estimate value:</p>
                <span class="text-base font-semibold text-accent">
                    {{ lowFormatValue }}
                </span>
            </div>

            <div class="flex justify-between">
                <p>High estimate value:</p>
                <span class="text-base font-semibold text-accent">
                    {{ highFormatValue }}
                </span>
            </div>
        </div>
    </section>
</template>
