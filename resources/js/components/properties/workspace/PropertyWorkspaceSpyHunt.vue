<script setup lang="ts">
import SpyHuntComparableList from '@/components/properties/workspace/spyhunt/SpyHuntComparableList.vue';
import SpyHuntEmptyState from '@/components/properties/workspace/spyhunt/SpyHuntEmptyState.vue';
import SpyHuntMap from '@/components/properties/workspace/spyhunt/SpyHuntMap.vue';
import SpyHuntValueEstimateCard from '@/components/properties/workspace/spyhunt/SpyHuntValueEstimateCard.vue';
import type {
    SpyHuntComparable,
    SpyHuntFilters,
    SpyHuntState,
} from '@/components/properties/workspace/spyhunt/types';
import type {
    PropertyWorkspaceProperty,
    WorkspaceModuleMeta,
} from '@/components/properties/workspace/types';
import {
    ArrowDownRight,
    ArrowUpRight,
    Binoculars,
    Building2,
    CheckCircle2,
    DollarSign,
    Loader2,
    MapPin,
    RefreshCw,
    Sparkles,
    Timer,
    TrendingUp,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

interface Props {
    property: PropertyWorkspaceProperty;
    meta?: WorkspaceModuleMeta | null;
    moduleId: string;
}

const props = defineProps<Props>();

const endpointBadge = computed(
    () => props.meta?.endpoint ?? '/api/properties/:id/spyhunt',
);

const formatAddress = (property: PropertyWorkspaceProperty) => {
    const primary = property.address?.line1 ?? property.title ?? null;
    const locality = [property.address?.city, property.address?.state]
        .filter(Boolean)
        .join(', ');
    const sections = [primary, locality].filter(
        (section): section is string => Boolean(section),
    );
    if (sections.length) {
        return sections.join(' • ');
    }
    return property.id ? `Property #${property.id}` : 'PixrSpyHunt';
};

const propertyHeading = computed(() => formatAddress(props.property));

const lastUpdatedCopy = computed(() => {
    const iso = props.meta?.last_run_at ?? props.property.last_updated;
    if (!iso) {
        return 'Awaiting first sync';
    }
    const date = new Date(iso);
    return `Updated ${date.toLocaleString(undefined, {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })}`;
});

const comparables = ref<SpyHuntComparable[]>([
    {
        id: 'cmp-01',
        address: '123 Main St',
        price: 485_000,
        rentPerMonth: null,
        beds: 3,
        baths: 2,
        sqft: 1450,
        status: 'sold',
        propertyType: 'House',
        distanceMiles: 0.6,
        lastEvent: 'Sold • Apr 12, 2025',
        dom: 9,
        thumbnail:
            'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=200&q=60',
        position: { x: 54, y: 46 },
    },
    {
        id: 'cmp-02',
        address: '1412 Juniper Ln',
        price: 512_000,
        rentPerMonth: null,
        beds: 4,
        baths: 3,
        sqft: 1720,
        status: 'active',
        propertyType: 'House',
        distanceMiles: 1.1,
        lastEvent: 'Listed • 3 days ago',
        dom: 3,
        thumbnail:
            'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=200&q=60',
        position: { x: 34, y: 42 },
    },
    {
        id: 'cmp-03',
        address: '88 Waverly Way',
        price: 468_000,
        rentPerMonth: 3_150,
        beds: 3,
        baths: 2,
        sqft: 1380,
        status: 'rental',
        propertyType: 'House',
        distanceMiles: 0.9,
        lastEvent: 'For rent • $3,150/mo',
        dom: 14,
        thumbnail:
            'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=200&q=60',
        position: { x: 61, y: 32 },
    },
    {
        id: 'cmp-04',
        address: '2108 Creek Bend',
        price: 559_000,
        rentPerMonth: null,
        beds: 4,
        baths: 3,
        sqft: 1880,
        status: 'sold',
        propertyType: 'House',
        distanceMiles: 2.4,
        lastEvent: 'Sold • Mar 26, 2025',
        dom: 11,
        thumbnail:
            'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=200&q=60',
        position: { x: 23, y: 60 },
    },
    {
        id: 'cmp-05',
        address: '701 Marlowe Dr',
        price: 472_000,
        rentPerMonth: null,
        beds: 2,
        baths: 2,
        sqft: 1280,
        status: 'active',
        propertyType: 'Condo',
        distanceMiles: 1.8,
        lastEvent: 'Price drop • −1%',
        dom: 17,
        thumbnail:
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=200&q=60',
        position: { x: 75, y: 58 },
    },
    {
        id: 'cmp-06',
        address: '55 Willow Plaza',
        price: 446_000,
        rentPerMonth: 3_050,
        beds: 2,
        baths: 2,
        sqft: 1365,
        status: 'rental',
        propertyType: 'Multi-family',
        distanceMiles: 1.3,
        lastEvent: 'Leased • Mar 18, 2025',
        dom: 21,
        thumbnail:
            'https://images.unsplash.com/photo-1430285561322-7808604715df?auto=format&fit=crop&w=200&q=60',
        position: { x: 44, y: 70 },
    },
]);

const priceLimits: [number, number] = [100_000, 1_100_000];

const filters = ref<SpyHuntFilters>({
    radius: 3,
    priceRange: [350_000, 900_000],
    propertyType: 'House',
    mode: 'sale',
});

const selectedComparableId = ref<string | null>(null);

const filteredComparables = computed(() => {
    const current = filters.value;
    return comparables.value.filter((item) => {
        const matchesMode =
            current.mode === 'rent'
                ? item.status === 'rental'
                : item.status !== 'rental';
        const matchesPrice =
            item.price >= current.priceRange[0] &&
            item.price <= current.priceRange[1];
        const matchesType =
            current.propertyType === 'Any' ||
            item.propertyType === current.propertyType;
        const matchesRadius = item.distanceMiles <= current.radius;
        return matchesMode && matchesPrice && matchesType && matchesRadius;
    });
});

const saleComparables = computed(() =>
    filteredComparables.value.filter((item) => item.status !== 'rental'),
);

const rentalComparables = computed(() =>
    filteredComparables.value.filter((item) => item.status === 'rental'),
);

const average = (
    items: SpyHuntComparable[],
    extractor: (item: SpyHuntComparable) => number | null,
) => {
    if (!items.length) {
        return null;
    }
    const values = items
        .map(extractor)
        .filter((value): value is number => value !== null);
    if (!values.length) {
        return null;
    }
    return values.reduce((sum, value) => sum + value, 0) / values.length;
};

const avgPricePerSqft = computed(() => {
    const value = average(
        saleComparables.value,
        (item) =>
            item.sqft && item.sqft > 0 ? item.price / item.sqft : null,
    );
    return value ? Math.round(value) : null;
});

const avgRentPerSqft = computed(() => {
    const value = average(
        rentalComparables.value,
        (item) =>
            item.sqft && item.sqft > 0 && item.rentPerMonth
                ? item.rentPerMonth / item.sqft
                : null,
    );
    return value ? Number(value.toFixed(2)) : null;
});

const avgDom = computed(() => {
    const value = average(
        saleComparables.value,
        (item) => (item.dom ? item.dom : null),
    );
    return value ? Math.round(value) : null;
});

const trendMeta = computed(() => {
    const base = 410;
    const current = avgPricePerSqft.value ?? base;
    const delta = current - base;
    const direction = delta >= 0 ? 'up' : 'down';
    const percent = Math.abs(delta / base) * 100;
    return {
        direction,
        label: `${delta >= 0 ? '+' : '-'}${percent.toFixed(1)}% vs last 30d`,
    };
});

const marketMetrics = computed(() => [
    {
        id: 'price',
        label: 'Avg price / ft²',
        value: avgPricePerSqft.value
            ? `$${avgPricePerSqft.value.toLocaleString()}`
            : '—',
        helper: `${saleComparables.value.length || 0} sale comps`,
        icon: DollarSign,
    },
    {
        id: 'rent',
        label: 'Avg rent / ft²',
        value: avgRentPerSqft.value
            ? `$${avgRentPerSqft.value.toFixed(2)}`
            : '—',
        helper: `${rentalComparables.value.length || 0} rent comps`,
        icon: Building2,
    },
    {
        id: 'dom',
        label: 'Days on market',
        value: avgDom.value ? `${avgDom.value} days` : '—',
        helper: 'Rolling 30-day window',
        icon: Timer,
    },
    {
        id: 'trend',
        label: 'Trend',
        value: trendMeta.value.label,
        helper:
            trendMeta.value.direction === 'up'
                ? 'Momentum ↑'
                : 'Cooling ↓',
        icon: trendMeta.value.direction === 'up' ? ArrowUpRight : ArrowDownRight,
        trendDirection: trendMeta.value.direction,
    },
]);

const heatmapInsights = computed(() => [
    {
        id: 'radius',
        label: `${filters.value.radius} mi radius`,
        value: `${filteredComparables.value.length} matches`,
        helper: 'Live MLS + portals',
    },
    {
        id: 'demand',
        label: 'Buyer demand',
        value: '8.6 / 10',
        helper: '+0.4 this week',
    },
    {
        id: 'dom',
        label: 'Avg DOM',
        value: avgDom.value ? `${avgDom.value} days` : '—',
        helper: '-3 vs ZIP median',
    },
]);

const comparableListItems = computed(() => {
    const source = filteredComparables.value.length
        ? filteredComparables.value
        : comparables.value;
    return [...source]
        .sort((a, b) => a.distanceMiles - b.distanceMiles)
        .slice(0, 4);
});

const worthValue = computed(() => props.property.worth?.value ?? 486_000);

const valueEstimate = computed(() => {
    const value = worthValue.value;
    const min = Math.round(value * 0.94);
    const max = Math.round(value * 1.08);
    const score = props.property.worth?.confidence ?? 0.78;
    const confidence =
        score >= 0.85 ? 'High' : score >= 0.65 ? 'Medium' : 'Low';

    return {
        value,
        min,
        max,
        confidence,
        sampleCount: Math.max(3, saleComparables.value.length || 0),
        radiusLabel: `${filters.value.radius} mi`,
        sourceNote: `Based on ${
            Math.max(3, saleComparables.value.length || 0) || 3
        } recent sales within ${filters.value.radius} mi.`,
    };
});

const statusBadges: Record<
    string,
    { label: string; classes: string }
> = {
    ready: {
        label: 'Ready',
        classes: 'bg-[#E4F9F0] text-[#0B6B4F]',
    },
    processing: {
        label: 'Processing',
        classes: 'bg-[#EEF2FF] text-[#3730A3]',
    },
    'in-progress': {
        label: 'In progress',
        classes: 'bg-[#FFF4DA] text-[#9A6B00]',
    },
    'needs-action': {
        label: 'Needs attention',
        classes: 'bg-[#FFE4E6] text-[#9F1239]',
    },
    loading: {
        label: 'Syncing',
        classes: 'bg-[#DBEAFE] text-[#1D4ED8]',
    },
};

const explicitState = computed<SpyHuntState>(() => {
    const key = (props.meta?.status ?? 'ready').toString().toLowerCase();
    if (key === 'error') {
        return 'error';
    }
    if (key === 'empty' || key === 'needs-action') {
        return 'empty';
    }
    if (key === 'loading' || key === 'refreshing') {
        return 'loading';
    }
    return 'ready';
});

const moduleState = computed<SpyHuntState>(() => {
    if (explicitState.value !== 'ready') {
        return explicitState.value;
    }
    return filteredComparables.value.length ? 'ready' : 'empty';
});

const moduleStatusBadge = computed(() => {
    const key = (props.meta?.status ?? 'ready').toString().toLowerCase();
    return statusBadges[key] ?? statusBadges.ready;
});

const isUpdatingFilters = ref(false);
const isRefreshing = ref(false);
const isAddToReportBusy = ref(false);
const reportAdded = ref(false);
const reportToast = ref('');

let filterTimer: ReturnType<typeof setTimeout> | null = null;
let refreshTimer: ReturnType<typeof setTimeout> | null = null;
let reportTimer: ReturnType<typeof setTimeout> | null = null;
let toastTimer: ReturnType<typeof setTimeout> | null = null;

const queueFilterAnimation = () => {
    isUpdatingFilters.value = true;
    if (filterTimer) {
        clearTimeout(filterTimer);
    }
    filterTimer = setTimeout(() => {
        isUpdatingFilters.value = false;
    }, 800);
};

const handleFiltersUpdate = (partial: Partial<SpyHuntFilters>) => {
    const current = filters.value;
    const nextRange =
        (partial.priceRange ?? current.priceRange) as SpyHuntFilters['priceRange'];
    filters.value = {
        ...current,
        ...partial,
        priceRange: nextRange,
    };
    queueFilterAnimation();
};

const handleComparableSelect = (comparable: SpyHuntComparable) => {
    selectedComparableId.value = comparable.id;
};

const handleTryAnotherRadius = () => {
    const [min, max] = filters.value.priceRange;
    handleFiltersUpdate({
        radius: Math.min(5, filters.value.radius + 2),
        propertyType: 'Any',
        mode: 'sale',
        priceRange: [
            Math.max(priceLimits[0], min - 50_000),
            Math.min(priceLimits[1], max + 50_000),
        ],
    });
};

const handleRefresh = () => {
    if (isRefreshing.value) {
        return;
    }
    isRefreshing.value = true;
    queueFilterAnimation();
    if (refreshTimer) {
        clearTimeout(refreshTimer);
    }
    refreshTimer = setTimeout(() => {
        isRefreshing.value = false;
    }, 1_300);
};

const showToast = (message: string) => {
    reportToast.value = message;
    if (toastTimer) {
        clearTimeout(toastTimer);
    }
    toastTimer = setTimeout(() => {
        reportToast.value = '';
    }, 4_500);
};

const handleAddToReport = () => {
    if (isAddToReportBusy.value) {
        return;
    }
    isAddToReportBusy.value = true;
    reportAdded.value = false;
    if (reportTimer) {
        clearTimeout(reportTimer);
    }
    reportTimer = setTimeout(() => {
        isAddToReportBusy.value = false;
        reportAdded.value = true;
        showToast('Market data added to your report successfully ✅');
    }, 1_500);
};

const dismissToast = () => {
    reportToast.value = '';
    if (toastTimer) {
        clearTimeout(toastTimer);
    }
};

watch(filteredComparables, (items) => {
    if (
        selectedComparableId.value &&
        !items.some((item) => item.id === selectedComparableId.value)
    ) {
        selectedComparableId.value = null;
    }
});

onBeforeUnmount(() => {
    [filterTimer, refreshTimer, reportTimer, toastTimer]
        .filter(Boolean)
        .forEach((timer) => clearTimeout(timer!));
});
</script>

<template>
    <div class="flex flex-col gap-6 text-[#111827]">
        <header
            class="rounded-[32px] bg-gradient-to-r from-[#E6E1FF] via-white to-white p-6 shadow-[0_18px_55px_rgba(124,77,255,0.12)]"
        >
            <div
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <div>
                    <p class="text-2xl font-semibold tracking-tight">
                        {{ propertyHeading }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ lastUpdatedCopy }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-2xl bg-[#7c4dff] px-5 py-3 text-sm font-semibold text-white shadow-[0_18px_32px_rgba(124,77,255,0.35)] transition hover:bg-[#6b3ce6] focus-visible:ring-2 focus-visible:ring-[#7c4dff]"
                        :disabled="isAddToReportBusy"
                        @click="handleAddToReport"
                    >
                        <Loader2
                            v-if="isAddToReportBusy"
                            class="size-4 animate-spin"
                        />
                        <CheckCircle2
                            v-else-if="reportAdded"
                            class="size-4"
                        />
                        <Sparkles v-else class="size-4" />
                        {{ reportAdded ? 'Added to Report' : 'Add to Report' }}
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-2xl bg-white/80 px-5 py-3 text-sm font-semibold text-gray-600 shadow-inner transition hover:text-[#111827] focus-visible:ring-2 focus-visible:ring-[#D1D5DB]"
                        :disabled="isRefreshing"
                        @click="handleRefresh"
                    >
                        <Loader2
                            v-if="isRefreshing"
                            class="size-4 animate-spin text-[#7c4dff]"
                        />
                        <RefreshCw
                            v-else
                            class="size-4 text-[#7c4dff]"
                        />
                        Refresh Data
                    </button>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3 text-xs">
                <span
                    class="inline-flex items-center gap-2 rounded-full px-3 py-1 font-semibold"
                    :class="moduleStatusBadge.classes"
                >
                    {{ moduleStatusBadge.label }}
                </span>
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 font-semibold text-[#7c4dff] shadow-sm"
                >
                    <MapPin class="size-3.5" />
                    {{ endpointBadge }}
                </span>
            </div>
        </header>

        <section v-if="moduleState === 'loading'" class="space-y-4">
            <div
                class="h-6 animate-pulse rounded-full bg-gradient-to-r from-gray-100 via-gray-200 to-gray-100"
            />
            <div class="grid gap-4 lg:grid-cols-10">
                <div
                    class="h-[520px] animate-pulse rounded-[32px] bg-gradient-to-br from-gray-100 via-gray-200 to-gray-100 lg:col-span-7"
                />
                <div class="space-y-4 lg:col-span-3">
                    <div class="h-40 animate-pulse rounded-[28px] bg-gray-100" />
                    <div class="h-48 animate-pulse rounded-[28px] bg-gray-100" />
                    <div class="h-48 animate-pulse rounded-[28px] bg-gray-100" />
                </div>
            </div>
        </section>

        <section v-else-if="moduleState === 'ready'" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-10">
                <div class="space-y-4 lg:col-span-7">
                    <SpyHuntMap
                        :comparables="filteredComparables"
                        :filters="filters"
                        :selected-comparable-id="selectedComparableId"
                        :is-updating="isUpdatingFilters"
                        @select="handleComparableSelect"
                        @clear-selection="selectedComparableId = null"
                        @update:filters="handleFiltersUpdate"
                    />

                    <div class="grid gap-3 md:grid-cols-3">
                        <div
                            v-for="insight in heatmapInsights"
                            :key="insight.id"
                            class="rounded-[22px] border border-white/40 bg-white/80 px-4 py-3 shadow-sm backdrop-blur"
                        >
                            <p class="text-xs font-semibold text-gray-400">
                                {{ insight.label }}
                            </p>
                            <p class="text-lg font-semibold text-[#111827]">
                                {{ insight.value }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ insight.helper }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 lg:col-span-3">
                    <div class="rounded-[28px] bg-white p-5 shadow-lg">
                        <header class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.3em] text-gray-400"
                                >
                                    Market snapshot
                                </p>
                                <p class="text-sm text-gray-500">
                                    Updated live from MLS + Pixr signals
                                </p>
                            </div>
                            <TrendingUp class="size-5 text-[#7c4dff]" />
                        </header>
                        <div class="mt-4 grid gap-4">
                            <div
                                v-for="metric in marketMetrics"
                                :key="metric.id"
                                class="flex items-start gap-3 rounded-2xl bg-[#f8f7ff] p-4"
                            >
                                <component
                                    :is="metric.icon"
                                    class="mt-1 size-4 text-[#7c4dff]"
                                />
                                <div>
                                    <p class="text-xs uppercase tracking-[0.3em] text-gray-400">
                                        {{ metric.label }}
                                    </p>
                                    <p
                                        class="text-lg font-semibold"
                                        :class="{
                                            'text-[#15803d]':
                                                metric.id === 'trend' &&
                                                metric.trendDirection === 'up',
                                            'text-[#b91c1c]':
                                                metric.id === 'trend' &&
                                                metric.trendDirection === 'down',
                                            'text-[#111827]':
                                                metric.id !== 'trend',
                                        }"
                                    >
                                        {{ metric.value }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ metric.helper }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <SpyHuntComparableList
                        :items="comparableListItems"
                        :selected-id="selectedComparableId"
                        @select="handleComparableSelect"
                    />

                    <SpyHuntValueEstimateCard
                        :value="valueEstimate.value"
                        :min="valueEstimate.min"
                        :max="valueEstimate.max"
                        :confidence="valueEstimate.confidence"
                        :sample-count="valueEstimate.sampleCount"
                        :radius-label="valueEstimate.radiusLabel"
                        :source-note="valueEstimate.sourceNote"
                    />

                    <div
                        class="rounded-[28px] bg-gradient-to-br from-[#f5f3ff] via-white to-white p-5 shadow-lg"
                    >
                        <div class="flex items-start gap-3">
                            <Sparkles class="size-5 text-[#7c4dff]" />
                            <div>
                                <p class="text-base font-semibold">
                                    Call-to-action
                                </p>
                                <p class="text-sm text-gray-500">
                                    Send this market snapshot to PixrSeal or
                                    append it to the investor workspace.
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="mt-4 inline-flex w-full items-center justify-between rounded-2xl bg-[#7c4dff] px-4 py-3 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(124,77,255,0.35)] transition hover:bg-[#6b3ce6]"
                            title="Available in PixrSeal Report"
                            @click="handleAddToReport"
                        >
                            Add Market Data to Report
                            <ArrowUpRight class="size-4" />
                        </button>
                        <p class="mt-2 text-xs text-gray-500">
                            Available in PixrSeal Report
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <SpyHuntEmptyState
            v-else-if="moduleState === 'empty'"
            :icon="Binoculars"
            title="Data not available for this radius"
            message="SpyHunt could not find comps that match your filters. Expand the radius or widen the price band to pull more activity."
            cta-label="Try another radius"
            hint="Tip: 5 mi radius unlocks surrounding ZIPs"
            @action="handleTryAnotherRadius"
        />

        <SpyHuntEmptyState
            v-else
            :icon="Binoculars"
            variant="error"
            title="Unable to fetch market data"
            message="The MLS bridge did not respond. Refresh SpyHunt or try again in a few minutes."
            cta-label="Refresh SpyHunt"
            @action="handleRefresh"
        />

        <Transition name="fade-slide">
            <div
                v-if="reportToast"
                class="fixed bottom-6 right-6 z-40 max-w-sm rounded-3xl bg-white px-5 py-4 text-sm font-semibold text-[#065f46] shadow-[0_18px_40px_rgba(5,150,105,0.25)]"
                role="status"
                aria-live="polite"
            >
                <div class="flex items-start gap-3">
                    <CheckCircle2 class="size-5 text-[#10b981]" />
                    <div class="flex-1">
                        <p>{{ reportToast }}</p>
                        <button
                            type="button"
                            class="mt-2 text-xs font-semibold text-[#0f172a]/60 underline"
                            @click="dismissToast"
                        >
                            Dismiss
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
