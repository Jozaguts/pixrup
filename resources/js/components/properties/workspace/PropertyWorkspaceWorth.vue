<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { usePlanUsage } from '@/composables/usePlanUsage';
import propertiesRoutes from '@/routes/properties';
import { useForm, usePage } from '@inertiajs/vue3';
import { AlertCircle, ArrowRight, Gauge, LineChart, Loader2, RefreshCw, ShieldCheck } from 'lucide-vue-next';
import { computed } from 'vue';
import type {
    PropertyWorkspaceProperty,
    WorkspaceModuleMeta,
    WorthComparable,
    WorthStatusState,
    WorthTrendPoint,
} from './types';
import AnalyticsChart from './worth/AnalyticsChart.vue';
import CardValuation from './worth/CardValuation.vue';
import ComparablesTable from './worth/ComparablesTable.vue';
import PropertyDetails from './worth/PropertyDetails.vue';
import RentalValueCard from './worth/RentalValueCard.vue';

interface Props {
    property: PropertyWorkspaceProperty;
    meta?: WorkspaceModuleMeta | null;
    moduleId: string;
}

const props = defineProps<Props>();

const propertyId = computed(() => Number(props.property.id));

const worth = computed(() => props.property.worth ?? null);
const hasWorth = computed(() => worth.value !== null && worth.value !== undefined);
const hasComparables = computed(() => worth.value?.comparables.some((comp) => comp.sale_price !== null));

const trendPoints = computed<WorthTrendPoint[]>(() => worth.value?.trend ?? []);
const hasTrend = computed(() => trendPoints.value.length > 0);

const fetchForm = useForm({});
const reportForm = useForm({});
const isFetchLoading = computed(() => fetchForm.processing);
const isReportLoading = computed(() => reportForm.processing);
const isBusy = computed(() => isFetchLoading.value || isReportLoading.value);

const { usage, remaining, limitExceeded: isUsageLimitReached, percentUsed, usageLabel, helperCopy } = usePlanUsage();

const usageMeterStyle = computed(() => ({
    width: `${Math.min(100, Math.max(0, percentUsed.value))}%`,
}));

const page = usePage();
const flashStatus = computed(() => page.props.flash?.status ?? null);

const successMessage = computed(() => {
    switch (flashStatus.value) {
        case 'worth-ready':
            return 'Appraisal completed successfully 🎯';
        case 'worth-report':
            return 'Appraisal added to the report queue.';
        default:
            return null;
    }
});

const errors = computed(() => page.props.errors ?? {});
const errorMessage = computed(() => {
    const error = errors.value?.worth;
    if (typeof error === 'string' && error.trim().length > 0) {
        return error;
    }

    return null;
});

const errorDisplayMessage = computed(() => errorMessage.value ?? 'We couldn’t retrieve data. Please try again later.');

const fetchedAt = computed(() => worth.value?.fetched_at ?? worth.value?.cached_at ?? null);

const lastFetchedLabel = computed(() => {
    if (!fetchedAt.value) {
        return null;
    }

    const date = new Date(fetchedAt.value);

    if (Number.isNaN(date.getTime())) {
        return null;
    }

    return date.toLocaleString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
});

const STALE_THRESHOLD_HOURS = 12;
const isStale = computed(() => {
    if (!fetchedAt.value) {
        return false;
    }

    const time = new Date(fetchedAt.value).getTime();

    if (Number.isNaN(time)) {
        return false;
    }

    const diffHours = (Date.now() - time) / (1000 * 60 * 60);

    return diffHours >= STALE_THRESHOLD_HOURS;
});

const state = computed<WorthStatusState>(() => {
    if (isBusy.value) {
        return 'loading';
    }

    if (!hasWorth.value && errorMessage.value) {
        return 'error';
    }

    if (hasWorth.value && isStale.value) {
        return 'cached';
    }

    if (hasWorth.value) {
        return 'success';
    }

    return 'idle';
});

const stateTitle = computed(() => {
    switch (state.value) {
        case 'loading':
            return 'Fetching valuation…';
        case 'cached':
            return 'Cached appraisal';
        case 'success':
            return 'Appraisal ready';
        case 'error':
            return 'Something went wrong';
        default:
            return 'No valuation yet';
    }
});

const stateSubtitle = computed(() => {
    switch (state.value) {
        case 'loading':
            return 'Pulling comps and calibrating valuation.';
        case 'cached':
            return 'Refresh to pull the latest market movement.';
        case 'success':
            return 'Review the latest valuation, comparables and market.';
        case 'error':
            return errorDisplayMessage.value;
        default:
            return 'No valuation yet — click “Fetch Valuation” .';
    }
});

const showSuccessBanner = computed(
    () => (state.value === 'success' || state.value === 'cached') && !!successMessage.value,
);

const isFetchDisabled = computed(() => isBusy.value || isUsageLimitReached.value || Number.isNaN(propertyId.value));

const isReportDisabled = computed(() => isBusy.value || !hasWorth.value || Number.isNaN(propertyId.value));

const upgradeHref = '/settings/billing';

const confidence = computed(() => worth.value?.confidence ?? null);
const valueLow = computed(() => worth.value?.value_low ?? null);
const valueHigh = computed(() => worth.value?.value_high ?? null);
const rentalValue = computed(() => worth.value?.rental_value ?? null);

const hasRentalValue = computed(
    () => rentalValue.value !== null && rentalValue.value !== undefined && rentalValue.value > 0,
);

const handleFetch = () => {
    if (isFetchDisabled.value) {
        return;
    }

    const route = propertiesRoutes.worth.fetch({
        property: propertyId.value,
    });
    fetchForm.submit(route.method, route.url, {
        preserveScroll: true,
    });
};

const handleRetry = () => {
    handleFetch();
};

const handleAddToReport = () => {
    if (isReportDisabled.value) {
        return;
    }

    const route = propertiesRoutes.worth.report.post({
        property: propertyId.value,
    });

    reportForm.submit(route.method, route.url, {
        preserveScroll: true,
    });
};

const moduleStatus = computed(() => props.meta?.status ?? state.value);

const moduleStatusLabel = computed(() => {
    if (!moduleStatus.value) {
        return 'Unknown';
    }

    return moduleStatus.value
        .toString()
        .replace(/[-_]/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
});

const propertySummary = computed(() => props.property.summary ?? {});

const subjectDetails = computed(() => ({
    beds: propertySummary.value?.bedrooms ?? null,
    baths: propertySummary.value?.bathrooms ?? null,
    squareFootage: propertySummary.value?.squareFootage ?? null,
    propertyType: propertySummary.value?.propertyType ?? null,
}));

const trendCount = computed(() => trendPoints.value.length);

const idleCallout = computed(() =>
    isUsageLimitReached.value
        ? 'Usage limit reached — upgrade your plan to fetch a fresh valuation.'
        : 'No valuation yet — click “Fetch Valuation” to pull the latest data.',
);
</script>

<template>
    <div class="flex flex-col gap-6 text-[#0d0d12] mt-4">
        <header class="flex flex-col gap-5 rounded-[12px] lg:p-6 md:p-6 transition-all duration-200 ease-in-out">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-2">
                    <p class="text-xs tracking-[0.28em] text-[#7c4dff] uppercase">AI Appraisal</p>
                    <h2 class="text-2xl font-semibold tracking-tight text-[#0d0d12]">
                        Instant property valuation and market confidence.
                    </h2>
                    <p class="text-sm text-[#6b7280]">
                        Fetch live AVM data, comps, and confidence scores with a single click.
                    </p>
                </div>

                <div class="flex w-full flex-col lg:max-w-sm">
                    <button
                        type="button"
                        class="neu-button flex transform cursor-pointer items-center justify-center gap-2 rounded-[12px] !bg-transparent px-4 py-4 text-sm font-medium !text-black transition-transform duration-200 ease-out hover:scale-110"
                        @click="handleFetch"
                    >
                        <component
                            :is="isFetchLoading ? Loader2 : RefreshCw"
                            :class="['h-4 w-4', { 'animate-spin': isFetchLoading }]"
                        />
                        {{ isUsageLimitReached ? 'Limit reached' : 'Fetch valuation' }}
                    </button>
                    <div v-if="lastFetchedLabel" class="mt-2 text-right text-sm text-[#6b7280]">
                        Last fetched on {{ lastFetchedLabel }}
                    </div>
                </div>
            </div>

            <transition name="fade">
                <div
                    v-if="showSuccessBanner"
                    class="flex items-center gap-3 rounded-[12px] bg-white px-4 py-3 text-sm text-[#0d0d12]"
                >
                    <ShieldCheck class="h-5 w-5 text-[#1dbf7a]" />
                    <span>{{ successMessage }}</span>
                </div>
            </transition>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-[minmax(0,320px)_1fr]">
                <div class="npo-form-shadow flex flex-col gap-3 rounded-[12px] p-4 text-xs text-[#6b7280]">
                    <div class="flex items-center justify-between text-xs font-semibold tracking-[0.3em] uppercase">
                        <span>Plan usage</span>
                        <span class="inline-flex items-center gap-2">
                            <Gauge class="h-4 w-4 text-[#7c4dff]" />
                            {{ usageLabel }}
                        </span>
                    </div>

                    <div class="relative h-3 w-full overflow-hidden rounded-full !bg-[#e4e6ee] shadow-inner">
                        <div
                            class="h-full rounded-[12px] bg-gradient-to-r from-[#7c4dff] to-[#16b1ff]"
                            :style="usageMeterStyle"
                        />
                    </div>
                    <p class="text-xs text-[#6b7280]">
                        {{ helperCopy }}
                    </p>
                    <a
                        v-if="isUsageLimitReached"
                        :href="upgradeHref"
                        class="inline-flex items-center justify-center gap-2 self-start rounded-[12px] bg-white px-4 py-2 text-xs font-semibold text-[#7c4dff] shadow-[8px_8px_20px_rgba(210,212,226,0.5),-8px_-8px_20px_rgba(255,255,255,0.95)] transition-all duration-200 ease-in-out hover:shadow-[inset_8px_8px_18px_rgba(210,212,226,0.5),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]"
                    >
                        Upgrade plan
                        <ArrowRight class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="npo-form-shadow flex flex-col gap-5 rounded-[12px] p-6 text-sm text-[#0d0d12]">
                <header class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                    <div class="flex flex-col w-full">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-[#0d0d12]">
                                    {{ stateTitle }}
                                </h3>
                                <p class="text-sm text-[#6b7280]">
                                    {{ stateSubtitle }}
                                </p>
                            </div>
                            <div
                                class="pointer-events-none flex size-12 items-center justify-center rounded-full text-black shadow-neu-in"
                            >
                                <LineChart class="h-6 w-6" />
                            </div>
                        </div>
                    </div>
                </header>

                <div v-if="state === 'loading'" class="space-y-4">
                    <div
                        class="h-40 animate-pulse rounded-[12px] bg-[#f4f5fa] shadow-[inset_12px_12px_30px_rgba(210,212,226,0.6),inset_-12px_-12px_30px_rgba(255,255,255,0.92)]"
                    />
                    <div
                        class="h-32 animate-pulse rounded-[12px] bg-[#f4f5fa] shadow-[inset_12px_12px_30px_rgba(210,212,226,0.6),inset_-12px_-12px_30px_rgba(255,255,255,0.92)]"
                    />
                </div>

                <div
                    v-else-if="state === 'error'"
                    class="flex flex-col gap-4 rounded-[12px] bg-[#fff5f5] p-6 text-[#9a1b1b] shadow-[12px_12px_28px_rgba(244,200,200,0.55),-12px_-12px_28px_rgba(255,255,255,0.95)]"
                >
                    <div class="flex items-center gap-3 text-sm">
                        <AlertCircle class="h-5 w-5" />
                        <span>{{ errorDisplayMessage }}</span>
                    </div>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 self-start rounded-[12px] bg-white px-4 py-2 text-sm font-semibold text-[#9a1b1b] shadow-[8px_8px_20px_rgba(245,216,216,0.6),-8px_-8px_20px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_8px_8px_18px_rgba(236,187,187,0.55),inset_-8px_-8px_18px_rgba(255,247,247,0.9)]"
                        @click="handleRetry"
                    >
                        Retry
                        <ArrowRight class="h-4 w-4" />
                    </button>
                </div>
                <template v-else-if="state === 'success' || state === 'cached'">
                    <div class="grid gap-4 lg:grid-cols-[1.3fr_1fr] rounded-[12px]">
                        <CardValuation
                            :value="worth?.value ?? null"
                            :value-low="valueLow"
                            :value-high="valueHigh"
                            :confidence="confidence"
                            :fetched-at="fetchedAt"
                            :is-stale="state === 'cached'"
                        />

                        <RentalValueCard v-if="hasRentalValue" :rental-value="rentalValue" />
                        <div
                            v-else
                            class="flex flex-col  gap-2 rounded-[12px]  text-sm text-[#6b7280]"
                        >
                            <p class="text-base font-semibold tracking-[0.3em] text-[#0d0d12] uppercase">Rental value</p>
                            <p class="text-base text-[#0d0d12]">
                                Rental projections are not available yet. Fetch a fresh valuation or add rental data to
                                unlock this card.
                            </p>
                        </div>
                    </div>

                    <AnalyticsChart v-if="hasTrend" :points="trendPoints" />

                    <ComparablesTable :comparables="worth?.comparables ?? []" :is-loading="!worth?.comparables" />

                    <div
                        class="flex flex-col gap-3 rounded-[12px] p-5 text-sm text-[#6b7280]"
                    >
                        <p class="text-sm ">
                            Sync this valuation with PixrSeal to include comps and trendline snapshots in investor
                            reports.
                        </p>

                        <button
                            type="button"
                            :disabled="isReportDisabled"
                            class="neu-button flex items-center justify-center gap-2 rounded-[12px] px-4 text-sm !text-black cursor-pointer !bg-transparent py-4 font-medium active"
                            @click="handleAddToReport"
                        >
                            Add to report
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </div>
                </template>

                <div
                    v-else
                    class="flex flex-col gap-3 rounded-[12px] bg-[#f4f5fa] p-6 text-sm text-[#6b7280] shadow-[inset_12px_12px_30px_rgba(210,212,226,0.6),inset_-12px_-12px_30px_rgba(255,255,255,0.92)]"
                >
                    <p class="text-base font-semibold text-[#0d0d12]">No valuation yet</p>
                    <p>
                        {{ idleCallout }}
                    </p>
                    <button
                        type="button"
                        :disabled="isFetchDisabled"
                        class="inline-flex items-center gap-2 self-start rounded-[12px] bg-white px-4 py-2 text-sm font-semibold text-[#7c4dff] shadow-[8px_8px_20px_rgba(210,212,226,0.6),-8px_-8px_20px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_8px_8px_18px_rgba(210,212,226,0.55),inset_-8px_-8px_18px_rgba(255,255,255,0.92)] disabled:cursor-not-allowed disabled:opacity-60"
                        @click="handleFetch"
                    >
                        Fetch valuation
                        <ArrowRight class="h-4 w-4" />
                    </button>
                </div>
            </article>

            <aside class="flex flex-col gap-4 npo-form-shadow rounded-[12px] ">
                <section class="flex flex-col gap-4  p-6">
                    <header class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-[#0d0d12]">Module signals</h3>

                        <div
                            class="pointer-events-none flex size-12 items-center justify-center rounded-full text-black shadow-neu-in"
                        >
                            <RefreshCw class="h-5 w-5" />
                        </div>
                    </header>

                    <ul class="grid gap-3 text-xs font-semibold tracking-[0.3em] text-[#6b7280] uppercase">
                        <li
                            class="flex rounded-[12px] shadow-neu-in p-4 justify-between"
                        >
                            <span>Status</span>
                            <span class="text-[#7c4dff]">{{ moduleStatusLabel }}</span>
                        </li>
                        <li
                            class="flex rounded-[12px] shadow-neu-in p-4 justify-between"
                        >
                            <span>Local state</span>
                            <span class="text-[#0d0d12]">{{ state }}</span>
                        </li>
                        <li
                            class="flex rounded-[12px] shadow-neu-in p-4 justify-between"
                        >
                            <span>Comparables</span>
                            <span class="text-[#0d0d12]">{{ worth?.comparables?.length }}</span>
                        </li>
                        <li
                            class="flex rounded-[12px] shadow-neu-in p-4 justify-between"
                        >
                            <span>Trend points</span>
                            <span class="text-[#0d0d12]">{{ trendCount }}</span>
                        </li>
                        <li
                            class="flex rounded-[12px] shadow-neu-in p-4 justify-between"
                        >
                            <span>Plan remaining</span>
                            <span class="text-[#0d0d12]"> {{ remaining }} / {{ usage.total }} </span>
                        </li>
                        <li
                            v-if="lastFetchedLabel"
                            class="flex rounded-[12px] shadow-neu-in p-4 justify-between"
                        >
                            <span>Last fetched</span>
                            <span class="text-[#0d0d12]">{{ lastFetchedLabel }}</span>
                        </li>
                    </ul>
                </section>
                <PropertyDetails
                    :beds="subjectDetails.beds"
                    :baths="subjectDetails.baths"
                    :squareFootage="subjectDetails.squareFootage"
                    :propertyType="subjectDetails.propertyType"
                />

                <div
                    v-if="!hasComparables && state === 'success'"
                    class="rounded-[12px] bg-[#fffdf5] p-5 text-sm text-[#92400e] shadow-[12px_12px_28px_rgba(240,213,166,0.45),-12px_-12px_28px_rgba(255,250,232,0.95)]"
                >
                    Valuation delivered, but comparables are pending. Refresh in a few minutes to load nearby sales
                    data.
                </div>

                <div
                    v-if="errorMessage && state !== 'loading'"
                    class="rounded-[12px] bg-[#fff5f5] p-4 text-sm text-[#9a1b1b] shadow-[8px_8px_22px_rgba(244,200,200,0.55),-8px_-8px_22px_rgba(255,255,255,0.93)]"
                >
                    {{ errorMessage }}
                </div>
            </aside>
        </section>
    </div>
</template>
