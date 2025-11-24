<script setup lang="ts">
import {
    Filter,
    Loader2,
    MapPin,
    MoveRight,
    RefreshCw,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Legends from '@/components/properties/workspace/spyhunt/spyHuntMap/Leyends.vue';
import type { SpyHuntComparable, SpyHuntFilters } from './types';

interface Props {
    comparables: SpyHuntComparable[];
    filters: SpyHuntFilters;
    selectedComparableId?: string | null;
    isUpdating?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    selectedComparableId: null,
    isUpdating: false,
});

const emit = defineEmits<{
    (e: 'select', comparable: SpyHuntComparable): void;
    (e: 'update:filters', payload: Partial<SpyHuntFilters>): void;
    (e: 'clear-selection'): void;
}>();

const hoveredComparableId = ref<string | null>(null);

const selectedComparable = computed(() =>
    props.comparables.find(
        (item) => item.id === props.selectedComparableId,
    ),
);

const hoveredComparable = computed(() =>
    props.comparables.find(
        (item) => item.id === hoveredComparableId.value,
    ),
);

const highlightComparable = computed(
    () => hoveredComparable.value ?? selectedComparable.value ?? null,
);

const floatingCardStyle = computed(() => {
    if (!highlightComparable.value) {
        return null;
    }
    const { x, y } = highlightComparable.value.position;
    const safeX = Math.min(Math.max(x, 10), 80);
    const safeY = Math.min(Math.max(y, 15), 85);
    return {
        left: `calc(${safeX}% + 16px)`,
        top: `calc(${safeY}% - 16px)`,
    };
});

const radiusOptions = [1, 3, 5];
const propertyTypes: SpyHuntFilters['propertyType'][] = [
    'Any',
    'House',
    'Condo',
    'Multi-family',
    'Townhome',
];

const priceBounds: [number, number] = [100_000, 1_100_000];

const handlePriceInput = (key: 'min' | 'max', value: number) => {
    const [min, max] = props.filters.priceRange;
    if (key === 'min') {
        const nextMin = Math.min(value, max - 10_000);
        emit('update:filters', { priceRange: [nextMin, max] });
        return;
    }
    const nextMax = Math.max(value, min + 10_000);
    emit('update:filters', { priceRange: [min, nextMax] });
};

const currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
});

const formatCurrency = (value: number) => currencyFormatter.format(value);

const displayPrice = (item: SpyHuntComparable) => {
    if (item.status === 'rental' && item.rentPerMonth) {
        return `${formatCurrency(item.rentPerMonth)} / mo`;
    }
    return formatCurrency(item.price);
};

const markerClasses = (status: SpyHuntComparable['status']) => {
    switch (status) {
        case 'sold':
            return 'bg-white text-[#16a34a] shadow-[0_10px_30px_rgba(22,163,74,0.35)]';
        case 'active':
            return 'bg-white text-[#2563eb] shadow-[0_10px_30px_rgba(37,99,235,0.35)]';
        case 'rental':
            return 'bg-white text-[#7c4dff] shadow-[0_10px_30px_rgba(124,77,255,0.35)]';
        default:
            return 'bg-white text-gray-600';
    }
};


</script>

<template>
    <div
        class="relative h-[520px] w-full overflow-hidden rounded-[32px] bg-gradient-to-tr from-[#dfe3f8] via-[#eef1ff] to-white shadow-[0_25px_65px_rgba(82,90,186,0.2)]"
    >
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.6),transparent_40%),radial-gradient(circle_at_80%_0,rgba(124,77,255,0.2),transparent_35%)]"
        />

        <div class="absolute inset-0">
            <div
                v-for="item in comparables"
                :key="item.id"
                class="absolute"
                :style="{
                    left: `calc(${item.position.x}% - 18px)`,
                    top: `calc(${item.position.y}% - 18px)`,
                }"
            >
                <button
                    type="button"
                    class="group flex size-9 items-center justify-center rounded-full border border-white transition hover:-translate-y-1 focus-visible:ring-2 focus-visible:ring-offset-2"
                    :class="[
                        markerClasses(item.status),
                        props.selectedComparableId === item.id
                            ? 'scale-110 ring-2 ring-white'
                            : '',
                    ]"
                    @mouseenter="hoveredComparableId = item.id"
                    @mouseleave="hoveredComparableId = null"
                    @click="emit('select', item)"
                >
                    <MapPin class="size-4" />
                </button>
            </div>
        </div>

        <Transition name="fade">
            <div
                v-if="isUpdating"
                class="absolute inset-0 z-20 flex flex-col items-center justify-center gap-2 bg-white/70 text-sm font-semibold text-gray-600 backdrop-blur-sm"
            >
                <Loader2 class="size-5 animate-spin text-[#7c4dff]" />
                Updating market data…
            </div>
        </Transition>

        <Transition name="fade">
            <div
                v-if="highlightComparable && floatingCardStyle"
                class="absolute z-20 w-56 rounded-2xl bg-white/95 p-4 text-xs text-gray-600 shadow-xl backdrop-blur"
                :style="floatingCardStyle"
            >
                <p class="font-semibold text-[#111827]">
                    {{ highlightComparable.address }}
                </p>
                <p class="text-sm text-gray-500">
                    {{ highlightComparable.beds ?? '—' }} bd •
                    {{ highlightComparable.baths ?? '—' }} ba •
                    {{ highlightComparable.sqft?.toLocaleString() ?? '—' }}
                    ft²
                </p>
                <p class="mt-2 text-base font-semibold text-[#7c4dff]">
                    {{ displayPrice(highlightComparable) }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ highlightComparable.lastEvent }}
                </p>
            </div>
        </Transition>

        <Transition name="slide-left">
            <aside
                v-if="selectedComparable"
                class="absolute right-0 top-0 z-30 h-full w-72 bg-white/95 p-5 backdrop-blur-md"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-[#111827]">
                            {{ selectedComparable.address }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ selectedComparable.distanceMiles.toFixed(1) }}
                            mi • {{ selectedComparable.propertyType }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="text-xs text-[#7c4dff]"
                        @click="emit('clear-selection')"
                    >
                        Close
                    </button>
                </div>
                <img
                    :src="selectedComparable.thumbnail"
                    :alt="selectedComparable.address"
                    class="mt-4 h-32 w-full rounded-2xl object-cover"
                />
                <p class="mt-4 text-2xl font-semibold text-[#111827]">
                    {{ displayPrice(selectedComparable) }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ selectedComparable.lastEvent }}
                </p>
                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div class="rounded-2xl bg-[#f3f4f6] p-3">
                        <p class="text-gray-500">Beds / Baths</p>
                        <p class="text-sm font-semibold text-[#111827]">
                            {{ selectedComparable.beds ?? '—' }} bd •
                            {{ selectedComparable.baths ?? '—' }} ba
                        </p>
                    </div>
                    <div class="rounded-2xl bg-[#f3f4f6] p-3">
                        <p class="text-gray-500">Sq Ft</p>
                        <p class="text-sm font-semibold text-[#111827]">
                            {{
                                selectedComparable.sqft?.toLocaleString() ??
                                '—'
                            }}
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-lg transition hover:bg-[#6b3ce6]"
                >
                    View detail
                    <MoveRight class="size-4" />
                </button>
            </aside>
        </Transition>

        <div
            class="pointer-events-none absolute inset-x-6 top-6 flex flex-wrap gap-3"
        >
            <div
                class="pointer-events-auto flex flex-wrap gap-3 rounded-2xl bg-white/90 px-5 py-4 text-sm font-semibold shadow-lg"
            >
                <p class="text-xs uppercase tracking-[0.3em] text-gray-400">
                    Radius
                </p>
                <div class="flex gap-2">
                    <button
                        v-for="radius in radiusOptions"
                        :key="radius"
                        type="button"
                        class="rounded-full px-3 py-1 text-xs font-semibold transition"
                        :class="
                            radius === filters.radius
                                ? 'bg-[#7c4dff] text-white shadow'
                                : 'bg-gray-100 text-gray-600'
                        "
                        @click="emit('update:filters', { radius })"
                    >
                        {{ radius }} mi
                    </button>
                </div>
            </div>

            <div
                class="pointer-events-auto flex flex-1 flex-col gap-2 rounded-2xl bg-white/90 px-5 py-4 shadow-lg md:max-w-md"
            >
                <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.3em] text-gray-400">
                    Price range
                    <span class="text-[10px] text-gray-400">
                        {{ formatCurrency(filters.priceRange[0]) }} –
                        {{ formatCurrency(filters.priceRange[1]) }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <input
                        type="range"
                        class="h-1 flex-1 appearance-none rounded-full bg-gray-200 accent-[#7c4dff]"
                        :min="priceBounds[0]"
                        :max="priceBounds[1]"
                        step="10000"
                        :value="filters.priceRange[0]"
                        @input="
                            handlePriceInput(
                                'min',
                                Number(($event.target as HTMLInputElement).value),
                            )
                        "
                    />
                    <input
                        type="range"
                        class="h-1 flex-1 appearance-none rounded-full bg-gray-200 accent-[#7c4dff]"
                        :min="priceBounds[0]"
                        :max="priceBounds[1]"
                        step="10000"
                        :value="filters.priceRange[1]"
                        @input="
                            handlePriceInput(
                                'max',
                                Number(($event.target as HTMLInputElement).value),
                            )
                        "
                    />
                </div>
            </div>

            <div
                class="pointer-events-auto flex flex-wrap gap-3 rounded-2xl bg-white/90 px-4 py-4 text-sm font-semibold shadow-lg"
            >
                <label class="text-xs uppercase tracking-[0.3em] text-gray-400">
                    Type
                </label>
                <select
                    class="rounded-2xl border-0 bg-[#f3f4f6] px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-[#7c4dff]"
                    :value="filters.propertyType"
                    @change="
                        emit('update:filters', {
                            propertyType: ($event.target as HTMLSelectElement)
                                .value as SpyHuntFilters['propertyType'],
                        })
                    "
                >
                    <option
                        v-for="type in propertyTypes"
                        :key="type"
                        :value="type"
                    >
                        {{ type }}
                    </option>
                </select>
                <div class="ml-2 flex items-center gap-2 rounded-full bg-[#f3e8ff] px-3 py-1 text-xs text-[#7c4dff]">
                    <Filter class="size-3.5" />
                    Smart filters live
                </div>
            </div>

            <div
                class="pointer-events-auto flex items-center gap-2 rounded-2xl bg-white/90 px-4 py-4 shadow-lg"
            >
                <span class="text-xs font-semibold uppercase tracking-[0.3em] text-gray-400">
                    Mode
                </span>
                <div class="flex gap-2 rounded-full bg-[#f3f4f6] p-1">
                    <button
                        type="button"
                        class="rounded-full px-3 py-1 text-xs font-semibold transition"
                        :class="
                            filters.mode === 'sale'
                                ? 'bg-white text-[#1f2937] shadow'
                                : 'text-gray-500'
                        "
                        @click="emit('update:filters', { mode: 'sale' })"
                    >
                        Sale
                    </button>
                    <button
                        type="button"
                        class="rounded-full px-3 py-1 text-xs font-semibold transition"
                        :class="
                            filters.mode === 'rent'
                                ? 'bg-white text-[#1f2937] shadow'
                                : 'text-gray-500'
                        "
                        @click="emit('update:filters', { mode: 'rent' })"
                    >
                        Rent
                    </button>
                </div>
            </div>
        </div>
        <Legends  />


        <div class="pointer-events-none absolute bottom-6 right-6">
            <div
                class="pointer-events-auto inline-flex items-center gap-2 rounded-full bg-white/90 px-4 py-2 text-xs font-semibold text-gray-600 shadow-lg"
            >
                <RefreshCw class="size-3.5 text-[#7c4dff]" />
                Live MLS feed
            </div>
        </div>
    </div>
</template>
