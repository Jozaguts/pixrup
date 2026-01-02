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
    <section
        class="flex flex-col gap-4 rounded-[12px] bg-gray-200 p-6 shadow-neu-in transition-all duration-200 ease-in-out"
    >
        <header class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div
                    class="mb-2 flex items-center justify-between md:flex-col md:gap-1 lg:flex-col lg:items-start lg:gap-1"
                >
                    <p class="text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase">Comparables</p>
                    <span
                        class="active inline-flex items-center rounded-sm px-2 py-1 text-xs font-medium text-black ring shadow-neu-in ring-white"
                    >
                        {{ comparables.length }} homes
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-[#0d0d12]">Nearby sales in the last 90 days</h3>
            </div>
        </header>

        <div v-if="props.isLoading" class="space-y-3">
            <div
                v-for="index in 3"
                :key="`skeleton-${index}`"
                class="h-16 animate-pulse rounded-[22px] bg-[#f4f5fa] shadow-[inset_12px_12px_28px_rgba(210,212,226,0.6),inset_-12px_-12px_28px_rgba(255,255,255,0.92)]"
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
        <p
            v-else
            class="rounded-[22px] bg-[#f4f5fa] px-4 py-6 text-sm text-accent/50 shadow-[inset_10px_10px_24px_rgba(210,212,226,0.55),inset_-10px_-10px_24px_rgba(255,255,255,0.95)]"
        >
            No comparables yet — fetch a valuation to populate nearby sale activity.
        </p>
    </section>
</template>
<style scoped>
.soft-table {
    --easy-table-header-background-color: #eee;  /* light bg */
    --easy-table-header-font-color: #374151;/* dark gray */
    --easy-table-body-row-background-color: #eee;
    --easy-table-body-row-hover-background-color:var(--color-gray-200);
    --easy-table-row-border: 1px solid #e5e7eb; /* light gray border */
    --easy-table-body-row-height: 72px; /* tall row to fit pdf thumbnail */
    --easy-table-header-item-padding: 1rem;
    --easy-table-border: none;
    --easy-table-body-row-font-color:rgba(0, 0, 0, 0.60);
    .easy-data-table__body-cell,
    .easy-data-table__header-cell {
        text-align: center !important;
    }

}
</style>