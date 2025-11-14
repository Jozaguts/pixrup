<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    rentalValue?: number | null;
    occupancyRate?: number | null;
    notes?: string | null;
}

const props = withDefaults(defineProps<Props>(), {
    rentalValue: null,
    occupancyRate: null,
    notes: null,
});

const formattedRental = computed(() => {
    if (props.rentalValue === null || props.rentalValue === undefined) {
        return '—';
    }

    return `$${Intl.NumberFormat('en-US', {
        maximumFractionDigits: 0,
    }).format(props.rentalValue)}/mo`;
});

const occupancyCopy = computed(() => {
    if (props.occupancyRate === null || props.occupancyRate === undefined) {
        return 'Occupancy data pending';
    }

    return `${Intl.NumberFormat('en-US', {
        maximumFractionDigits: 0,
    }).format(props.occupancyRate)}% avg. occupancy`;
});

const noteCopy = computed(
    () =>
        props.notes ??
        'All rental projections assume standard lease terms and stabilized marketing.',
);
</script>

<template>
    <section
        class="flex flex-col gap-4 neu-surface rounded-[26px] p-6 shadow-neu-out transition-all duration-200 ease-in-out"
    >
        <header class="flex flex-col gap-1">
            <p
                class="text-xs font-semibold tracking-[0.3em] text-[#6b7280] uppercase"
            >
                Rental value
            </p>
            <h3 class="text-lg font-semibold text-[#0d0d12]">
                Potential monthly income
            </h3>
        </header>

        <div class="flex flex-col gap-3">
            <p class="text-3xl font-semibold text-[#16b1ff]">
                {{ formattedRental }}
            </p>
            <p class="text-sm text-[#6b7280]">
                {{ occupancyCopy }}
            </p>
        </div>

        <p
            class="rounded-[20px] bg-[#f4f5fa] px-4 py-3 text-sm text-[#6b7280] shadow-[inset_8px_8px_20px_rgba(210,212,226,0.55),inset_-8px_-8px_20px_rgba(255,255,255,0.95)]"
        >
            {{ noteCopy }}
        </p>
    </section>
</template>
