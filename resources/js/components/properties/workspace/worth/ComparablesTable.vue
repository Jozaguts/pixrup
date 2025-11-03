<script setup lang="ts">
import { computed } from 'vue';
import type { WorthComparable } from '../types';

interface Props {
    comparables?: WorthComparable[];
    isLoading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    comparables: () => [],
    isLoading: false,
});

const hasComparables = computed(
    () => props.comparables.length > 0 && !props.isLoading,
);

const rows = computed(() =>
    props.comparables.map((item, index) => ({
        id: item.id ?? `comp-${index}`,
        address: item.address ?? 'Unknown',
        salePrice:
            item.sale_price !== null && item.sale_price !== undefined
                ? `$${Intl.NumberFormat('en-US', {
                      maximumFractionDigits: 0,
                  }).format(item.sale_price)}`
                : '—',
        saleDate: item.sale_date
            ? new Date(item.sale_date).toLocaleDateString(undefined, {
                  month: 'short',
                  day: 'numeric',
                  year: 'numeric',
              })
            : '—',
        distance:
            item.distance_miles !== null && item.distance_miles !== undefined
                ? `${item.distance_miles.toFixed(2)} mi`
                : '—',
        delta: item.delta ?? null,
    })),
);
</script>

<template>
    <section
        class="neu-surface shadow-neu-out flex flex-col gap-4 rounded-[26px] p-6 transition-all duration-200 ease-in-out"
    >
        <header class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#6b7280]">
                    Comparables
                </p>
                <h3 class="text-lg font-semibold text-[#0d0d12]">
                    Nearby sales in the last 90 days
                </h3>
            </div>
            <span
                class="inline-flex items-center gap-2 rounded-full bg-[#f4f5fa] px-3 py-1 text-xs font-medium text-[#6b7280] shadow-[inset_4px_4px_12px_rgba(204,206,214,0.6),inset_-4px_-4px_12px_rgba(255,255,255,0.9)]"
            >
                {{ rows.length }} homes
            </span>
        </header>

        <div v-if="props.isLoading" class="space-y-3">
            <div
                v-for="index in 3"
                :key="`skeleton-${index}`"
                class="h-16 animate-pulse rounded-[22px] bg-[#f4f5fa] shadow-[inset_12px_12px_28px_rgba(210,212,226,0.6),inset_-12px_-12px_28px_rgba(255,255,255,0.92)]"
            />
        </div>

        <div v-else-if="hasComparables" class="overflow-hidden rounded-[22px]">
            <table class="min-w-full divide-y divide-[#e5e7eb] text-sm text-[#0d0d12]">
                <thead class="bg-[#f4f5fa] text-xs font-semibold uppercase tracking-[0.25em] text-[#6b7280]">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left">Address</th>
                        <th scope="col" class="px-4 py-3 text-left">Sale price</th>
                        <th scope="col" class="px-4 py-3 text-left">Sale date</th>
                        <th scope="col" class="px-4 py-3 text-left">Distance</th>
                        <th scope="col" class="px-4 py-3 text-left">Delta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f1f5f9] bg-white">
                    <tr
                        v-for="row in rows"
                        :key="row.id"
                        class="transition-colors duration-200 hover:bg-[#f8f9fd]"
                    >
                        <td class="px-4 py-4">
                            <p class="font-semibold text-[#0d0d12]">{{ row.address }}</p>
                        </td>
                        <td class="px-4 py-4 text-[#7c4dff]">
                            {{ row.salePrice }}
                        </td>
                        <td class="px-4 py-4 text-[#6b7280]">
                            {{ row.saleDate }}
                        </td>
                        <td class="px-4 py-4 text-[#6b7280]">
                            {{ row.distance }}
                        </td>
                        <td class="px-4 py-4">
                            <span
                                v-if="row.delta"
                                class="rounded-full bg-[#f4f5fa] px-3 py-1 text-xs font-semibold text-[#1dbf7a] shadow-[inset_4px_4px_12px_rgba(204,206,214,0.6),inset_-4px_-4px_12px_rgba(255,255,255,0.9)]"
                            >
                                {{ row.delta }}
                            </span>
                            <span v-else class="text-[#d1d5db]">—</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p
            v-else
            class="rounded-[22px] bg-[#f4f5fa] px-4 py-6 text-sm text-[#6b7280] shadow-[inset_10px_10px_24px_rgba(210,212,226,0.55),inset_-10px_-10px_24px_rgba(255,255,255,0.95)]"
        >
            No comparables yet — fetch a valuation to populate nearby sale activity.
        </p>
    </section>
</template>
