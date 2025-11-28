<script setup lang="ts">
import SpyHuntComparableList from '@/components/properties/workspace/spyhunt/SpyHuntComparableList.vue';
import SpyHuntEmptyState from '@/components/properties/workspace/spyhunt/SpyHuntEmptyState.vue';
import SpyHuntMap from '@/components/properties/workspace/spyhunt/spyHuntMap/index.vue';
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
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import SpyHuntWorkSpaceSkeleton from '@/components/skeleton/SpyHuntWorkSpaceSkeleton.vue';
import SpyhuntHeader from '@/components/properties/workspace/spyhunt/SpyhuntHeader.vue';
import useSpyHunt from '@/composables/useSpyHunt';
import spyHuntRoutes from '@/routes/properties/spyhunt';

interface Props {
    property: PropertyWorkspaceProperty;
    meta: WorkspaceModuleMeta;
    moduleId: string;
}
const props = defineProps<Props>();
const { formatAddress } = useSpyHunt();
const propertyHeading = computed(() => formatAddress(props.property));

const lastUpdatedCopy = computed(() => {
    const iso = props.meta?.last_run_at ?? props.property.last_updated; // todo esto tieen que llgar formateado desde el backeend
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
        squareFootage: 1450,
        status: 'sold',
        propertyType: 'House',
        distanceMiles: 0.6,
        lastEvent: 'Sold • Apr 12, 2025',
        daysOnMarket: 9,
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
        squareFootage: 1720,
        status: 'active',
        propertyType: 'House',
        distanceMiles: 1.1,
        lastEvent: 'Listed • 3 days ago',
        daysOnMarket: 3,
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
        squareFootage: 1380,
        status: 'rental',
        propertyType: 'House',
        distanceMiles: 0.9,
        lastEvent: 'For rent • $3,150/mo',
        daysOnMarket: 14,
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
        squareFootage: 1880,
        status: 'sold',
        propertyType: 'House',
        distanceMiles: 2.4,
        lastEvent: 'Sold • Mar 26, 2025',
        daysOnMarket: 11,
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
        squareFootage: 1280,
        status: 'active',
        propertyType: 'Condo',
        distanceMiles: 1.8,
        lastEvent: 'Price drop • −1%',
        daysOnMarket: 17,
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
        squareFootage: 1365,
        status: 'rental',
        propertyType: 'Multi-family',
        distanceMiles: 1.3,
        lastEvent: 'Leased • Mar 18, 2025',
        daysOnMarket: 21,
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
    const nextRange = (partial.priceRange ??
        current.priceRange) as SpyHuntFilters['priceRange'];
    filters.value = {
        ...current,
        ...partial,
        priceRange: nextRange,
    };
    queueFilterAnimation();
};
const changeEventHandler = (event: {type: string; value: any}) => {
    console.log(event);
    if (event.type === 'radius'){
        spyhunt.value.filters.defaults.radius = event.value
    }
}

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
type SpyHunt = {
    filters:{
        radius: number[],
        defaults:{
            radius: number
        }
    }
}
const spyhunt = ref<SpyHunt>({} as SpyHunt);

async function loadSpyHunt() {
    const res = await fetch(spyHuntRoutes.fetch.get(props.property.id).url,{
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
    const json = await res.json();
    spyhunt.value = json.data;
}
onMounted(() => {
    loadSpyHunt()
});
</script>

<template>
    <div class="flex flex-col gap-6 text-[#111827]" >
        <SpyhuntHeader
            :title="propertyHeading"
            :subtitle="lastUpdatedCopy"
            :status="meta.status"
            :isRefreshing="isRefreshing"
            :isAddToReportBusy="isAddToReportBusy"
            :reportAdded="reportAdded"
            @refresh="handleRefresh"
            @add-to-report="handleAddToReport"
        />
        <SpyHuntWorkSpaceSkeleton v-if="moduleState === 'loading'" />

        <section v-else-if="moduleState === 'ready'" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-10">
                <div class="space-y-4 lg:col-span-7">
                    <SpyHuntMap
                        v-if="spyhunt"
                        :comparables="filteredComparables"
                        :filters="spyhunt.filters"
                        :selected-comparable-id="selectedComparableId"
                        :is-updating="isUpdatingFilters"
                        @select="handleComparableSelect"
                        @clear-selection="selectedComparableId = null"
                        @changed="changeEventHandler"
                    />

                    <div class="grid gap-3 md:grid-cols-3">
                        <!--                        <div-->
                        <!--                            v-for="insight in heatmapInsights"-->
                        <!--                            :key="insight.id"-->
                        <!--                            class="rounded-[22px] border border-white/40 bg-white/80 px-4 py-3 shadow-sm backdrop-blur"-->
                        <!--                        >-->
                        <!--                            <p class="text-xs font-semibold text-gray-400">-->
                        <!--                                {{ insight.label }}-->
                        <!--                            </p>-->
                        <!--                            <p class="text-lg font-semibold text-[#111827]">-->
                        <!--                                {{ insight.value }}-->
                        <!--                            </p>-->
                        <!--                            <p class="text-xs text-gray-500">-->
                        <!--                                {{ insight.helper }}-->
                        <!--                            </p>-->
                        <!--                        </div>-->
                    </div>
                </div>

                <div class="flex flex-col gap-4 lg:col-span-3">
                    <!--                    <div class="rounded-[28px] bg-white p-5 shadow-lg">-->
                    <!--                        <header class="flex items-center justify-between">-->
                    <!--                            <div>-->
                    <!--                                <p-->
                    <!--                                    class="text-xs font-semibold uppercase tracking-[0.3em] text-gray-400"-->
                    <!--                                >-->
                    <!--                                    Market snapshot-->
                    <!--                                </p>-->
                    <!--                                <p class="text-sm text-gray-500">-->
                    <!--                                    Updated live from MLS + Pixr signals-->
                    <!--                                </p>-->
                    <!--                            </div>-->
                    <!--                            <TrendingUp class="size-5 text-[#7c4dff]" />-->
                    <!--                        </header>-->
                    <!--                        <div class="mt-4 grid gap-4">-->
                    <!--                            <div-->
                    <!--                                v-for="metric in marketMetrics"-->
                    <!--                                :key="metric.id"-->
                    <!--                                class="flex items-start gap-3 rounded-2xl bg-[#f8f7ff] p-4"-->
                    <!--                            >-->
                    <!--                                <component-->
                    <!--                                    :is="metric.icon"-->
                    <!--                                    class="mt-1 size-4 text-[#7c4dff]"-->
                    <!--                                />-->
                    <!--                                <div>-->
                    <!--                                    <p class="text-xs uppercase tracking-[0.3em] text-gray-400">-->
                    <!--                                        {{ metric.label }}-->
                    <!--                                    </p>-->
                    <!--                                    <p-->
                    <!--                                        class="text-lg font-semibold"-->
                    <!--                                        :class="{-->
                    <!--                                            'text-[#15803d]':-->
                    <!--                                                metric.id === 'trend' &&-->
                    <!--                                                metric.trendDirection === 'up',-->
                    <!--                                            'text-[#b91c1c]':-->
                    <!--                                                metric.id === 'trend' &&-->
                    <!--                                                metric.trendDirection === 'down',-->
                    <!--                                            'text-[#111827]':-->
                    <!--                                                metric.id !== 'trend',-->
                    <!--                                        }"-->
                    <!--                                    >-->
                    <!--                                        {{ metric.value }}-->
                    <!--                                    </p>-->
                    <!--                                    <p class="text-xs text-gray-500">-->
                    <!--                                        {{ metric.helper }}-->
                    <!--                                    </p>-->
                    <!--                                </div>-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                    <SpyHuntComparableList-->
                    <!--                        :items="comparableListItems"-->
                    <!--                        :selected-id="selectedComparableId"-->
                    <!--                        @select="handleComparableSelect"-->
                    <!--                    />-->

                    <!--                    <SpyHuntValueEstimateCard-->
                    <!--                        :value="valueEstimate.value"-->
                    <!--                        :min="valueEstimate.min"-->
                    <!--                        :max="valueEstimate.max"-->
                    <!--                        :confidence="valueEstimate.confidence"-->
                    <!--                        :sample-count="valueEstimate.sampleCount"-->
                    <!--                        :radius-label="valueEstimate.radiusLabel"-->
                    <!--                        :source-note="valueEstimate.sourceNote"-->
                    <!--                    />-->

                    <div
                        class="rounded-[28px] bg-gradient-to-br from-[#f5f3ff] via-white to-white p-5 shadow-lg"
                    >
                        <!--                        <div class="flex items-start gap-3">-->
                        <!--                            <Sparkles class="size-5 text-[#7c4dff]" />-->
                        <!--                            <div>-->
                        <!--                                <p class="text-base font-semibold">-->
                        <!--                                    Call-to-action-->
                        <!--                                </p>-->
                        <!--                                <p class="text-sm text-gray-500">-->
                        <!--                                    Send this market snapshot to PixrSeal or-->
                        <!--                                    append it to the investor workspace.-->
                        <!--                                </p>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                        <!--                        <button-->
                        <!--                            type="button"-->
                        <!--                            class="mt-4 inline-flex w-full items-center justify-between rounded-2xl bg-[#7c4dff] px-4 py-3 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(124,77,255,0.35)] transition hover:bg-[#6b3ce6]"-->
                        <!--                            title="Available in PixrSeal Report"-->
                        <!--                            @click="handleAddToReport"-->
                        <!--                        >-->
                        <!--                            Add Market Data to Report-->
                        <!--                            <ArrowUpRight class="size-4" />-->
                        <!--                        </button>-->
                        <!--                        <p class="mt-2 text-xs text-gray-500">-->
                        <!--                            Available in PixrSeal Report-->
                        <!--                        </p>-->
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
                class="fixed right-6 bottom-6 z-40 max-w-sm rounded-3xl bg-white px-5 py-4 text-sm font-semibold text-[#065f46] shadow-[0_18px_40px_rgba(5,150,105,0.25)]"
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
