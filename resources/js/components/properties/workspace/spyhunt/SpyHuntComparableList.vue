<script setup lang="ts">
import { MapPin, MoveRight } from 'lucide-vue-next';
import { computed } from 'vue';
import type { SpyHuntComparable } from './types';

interface Props {
    items: SpyHuntComparable[];
    selectedId?: string | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'select', comparable: SpyHuntComparable): void;
}>();

const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
});

const formatPrice = (comparable: SpyHuntComparable) => {
    if (comparable.status === 'rental' && comparable.rentPerMonth) {
        return `${formatter.format(comparable.rentPerMonth)} / mo`;
    }

    return formatter.format(comparable.price);
};

const statusClasses = (status: SpyHuntComparable['status']) => {
    switch (status) {
        case 'sold':
            return 'bg-[#dcfce7] text-[#15803d]';
        case 'active':
            return 'bg-[#dbeafe] text-[#1d4ed8]';
        case 'rental':
            return 'bg-[#ede9fe] text-[#7c4dff]';
        default:
            return 'bg-gray-100 text-gray-600';
    }
};

const title = computed(() => {
    const rentalCount = props.items.filter(
        (item) => item.status === 'rental',
    ).length;
    const saleCount = props.items.length - rentalCount;
    return `${saleCount} sale comps • ${rentalCount} rent comps`;
});
</script>

<template>
    <div class="space-y-4 rounded-[24px] bg-white p-4 shadow-md">
        <header class="flex items-center justify-between">
            <div>
                <p
                    class="text-xs font-semibold uppercase tracking-[0.3em] text-gray-400"
                >
                    Comparable Summary
                </p>
                <p class="text-sm text-gray-500">
                    {{ title }}
                </p>
            </div>
            <MapPin class="size-5 text-[#7c4dff]" aria-hidden="true" />
        </header>
        <ul class="space-y-3">
            <li
                v-for="item in items"
                :key="item.id"
                class="flex gap-3 rounded-2xl border border-gray-100 p-3 transition hover:border-[#c4b5fd] hover:bg-[#f8f7ff]"
                :class="{
                    'ring-2 ring-[#7c4dff]/40':
                        selectedId && selectedId === item.id,
                }"
            >
                <img
                    :src="item.thumbnail"
                    :alt="item.address"
                    class="size-16 rounded-xl object-cover"
                    loading="lazy"
                />
                <div class="flex flex-1 flex-col">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-[#111827]">
                            {{ item.address }}
                        </p>
                        <span
                            class="rounded-full px-2 py-1 text-xs font-semibold"
                            :class="statusClasses(item.status)"
                        >
                            {{ item.status === 'rental' ? 'Rent' : item.status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">
                        {{ formatPrice(item) }} •
                        {{ item.beds ?? '—' }} bd •
                        {{ item.baths ?? '—' }} ba •
                        {{ item.sqft?.toLocaleString() ?? '—' }} ft²
                    </p>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                        <span>{{ item.propertyType }}</span>
                        <span>• {{ item.distanceMiles.toFixed(1) }} mi</span>
                        <span v-if="item.dom">• {{ item.dom }} DOM</span>
                    </div>
                    <button
                        type="button"
                        class="mt-3 inline-flex items-center gap-2 self-start text-sm font-semibold text-[#7c4dff] transition hover:text-[#5b36c5]"
                        @click="emit('select', item)"
                    >
                        View on Map
                        <MoveRight class="size-4" />
                    </button>
                </div>
            </li>
        </ul>
    </div>
</template>
