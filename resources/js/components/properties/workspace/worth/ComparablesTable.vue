<script setup lang="ts">
import { computed, ref } from 'vue';
import type { WorthComparable } from '../types';

interface Props {
    comparables?: WorthComparable[];
    isLoading?: boolean;
}
import useSpyHunt from '@/composables/useSpyHunt';
const props = withDefaults(defineProps<Props>(), {
    comparables: () => [],
    isLoading: false,
});

const hasComparables = computed(() => props.comparables.length > 0 && !props.isLoading);
const comparables = computed(() => {
    return props.comparables.map((comp) => {
        return {
            address: comp.address?.address_full,
            beds: comp.info?.beds,
            baths: comp.info?.baths,
            sqft: useSpyHunt().numberFormat(comp.info?.sqft ?? 0),
            distance: useSpyHunt().numberFormat(comp.info?.distance_from_subject ?? 0),
        };
    });
});
const headers = [
    {
        text: 'Address',
        value: 'address',
        fixed: true,
    },
    {
        text: 'Beds',
        value: 'beds',
        align: 'center',
        width: 50,
    },
    {
        text: 'Baths',
        value: 'baths',
        align: 'center',
        width: 50,
    },
    {
        text: 'Sqft',
        value: 'sqft',
        width: 50,
    },
    {
        text: 'Distance',
        value: 'distance',
        align: 'right',
        width: 50,
    },
    // {
    //     text: 'Delta',
    //     value: 'distance',
    //     align: 'right',
    //     width: 20,
    // },
];
const searchString = ref('');
</script>

<template>
    <section class="npo-form-shadow flex flex-col gap-4 rounded-[12px] bg-surface p-6">
        <header class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div
                    class="mb-2 flex items-center justify-between md:flex-col md:gap-1 lg:flex-col lg:items-start lg:gap-1"
                >
                    <p class="text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase">Comparables</p>
                    <span
                        class="active inline-flex items-center rounded-sm px-2 py-1 text-xs font-medium text-accent ring shadow-neu-in ring-white"
                    >
                        {{ comparables.length }} homes
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-accent">Nearby sales in the last 90 days</h3>
            </div>
        </header>

        <div v-if="props.isLoading" class="space-y-3">
            <div
                v-for="index in 3"
                :key="`skeleton-${index}`"
                class="h-16 animate-pulse rounded-[22px] bg-surface shadow-neu-in"
            />
        </div>
        <div v-else-if="hasComparables" class="overflow-hidden rounded-[12px]">
            <data-table
                table-class-name="soft-table"
                :headers="headers"
                :items="comparables"
                hide-footer
                search-field="title"
                :search-value="searchString"
            ></data-table>
        </div>
        <p v-else class="rounded-[22px] bg-surface px-4 py-6 text-sm text-accent/50">
            No comparables yet — fetch a valuation to populate nearby sale activity.
        </p>
    </section>
</template>
<style scoped>
.soft-table {
    --easy-table-header-background-color: var(--color-surface);
    --easy-table-header-font-color: var(--foreground-color);
    --easy-table-body-row-background-color: var(--color-surface);
    --easy-table-body-row-hover-background-color: var(--color-surface);
    --easy-table-row-border: 1px solid var(--color-background); /* light gray border */
    --easy-table-body-row-height: 72px; /* tall row to fit pdf thumbnail */
    --easy-table-header-item-padding: 1rem;
    --easy-table-border: none;
    --easy-table-body-row-hover-font-color: var(--on-surface-muted);
    --easy-table-body-row-font-color: var(--on-surface);
    .easy-data-table__body-cell, .easy-data-table__header-cell {text-align: center !important;};
}
</style>