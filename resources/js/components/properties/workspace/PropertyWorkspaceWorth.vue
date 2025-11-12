<script setup lang="ts">
import { usePlanUsage } from '@/composables/usePlanUsage';
import propertiesRoutes from '@/routes/properties';
import { useForm, usePage } from '@inertiajs/vue3';
import {
    AlertCircle,
    ArrowRight,
    Gauge,
    LineChart,
    Loader2,
    RefreshCw,
    ShieldCheck,
} from 'lucide-vue-next';
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
import { Button } from '@/components/ui/button';

interface Props {
    property: PropertyWorkspaceProperty;
    meta?: WorkspaceModuleMeta | null;
    moduleId: string;
}

const props = defineProps<Props>();

const propertyId = computed(() => Number(props.property.id));

const endpointBadge = computed(() => {
    if (props.meta?.endpoint) {
        return props.meta.endpoint;
    }

    return Number.isNaN(propertyId.value)
        ? '/properties/:id/worth'
        : `/properties/${propertyId.value}/worth`;
});

const worth = computed(() => props.property.worth ?? null);
const hasWorth = computed(
    () => worth.value !== null && worth.value !== undefined,
);

const comparables = computed<WorthComparable[]>(() => {
    const raw = (worth.value?.comparables ?? []) as Array<
        Partial<WorthComparable> & {
            price?: number | null;
            distance?: string | number | null;
        }
    >;

    return raw.map((item, index) => {
        const salePrice =
            item.sale_price !== undefined && item.sale_price !== null
                ? item.sale_price
                : (item.price ?? null);

        const rawDistance =
            item.distance_miles !== undefined && item.distance_miles !== null
                ? item.distance_miles
                : (item.distance ?? null);

        const parsedDistance =
            typeof rawDistance === 'number'
                ? rawDistance
                : typeof rawDistance === 'string'
                  ? Number.parseFloat(rawDistance)
                  : null;

        return {
            id: item.id ?? `comp-${index}`,
            address: item.address ?? 'Unknown address',
            sale_price:
                typeof salePrice === 'number' && Number.isFinite(salePrice)
                    ? salePrice
                    : null,
            sale_date: item.sale_date ?? null,
            distance_miles: Number.isFinite(parsedDistance)
                ? Number(parsedDistance)
                : null,
            delta: item.delta ?? null,
        };
    });
});

const hasComparables = computed(() =>
    comparables.value.some((comp) => comp.sale_price !== null),
);

const trendPoints = computed<WorthTrendPoint[]>(() => worth.value?.trend ?? []);
const hasTrend = computed(() => trendPoints.value.length > 0);

const fetchForm = useForm({});
const reportForm = useForm({});
const isFetchLoading = computed(() => fetchForm.processing);
const isReportLoading = computed(() => reportForm.processing);
const isBusy = computed(() => isFetchLoading.value || isReportLoading.value);

const {
    usage,
    remaining,
    limitExceeded: isUsageLimitReached,
    percentUsed,
    usageLabel,
    helperCopy,
} = usePlanUsage();

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

const errorDisplayMessage = computed(
    () =>
        errorMessage.value ??
        'We couldn’t retrieve data. Please try again later.',
);

const fetchedAt = computed(
    () => worth.value?.fetched_at ?? worth.value?.cached_at ?? null,
);

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
            return 'Pulling comps and calibrating valuation in real time.';
        case 'cached':
            return 'Refresh to pull the latest market movement and pricing confidence.';
        case 'success':
            return 'Review the latest valuation, comparables, and market trendlines.';
        case 'error':
            return errorDisplayMessage.value;
        default:
            return 'No valuation yet — click “Fetch Valuation” to get started.';
    }
});

const showSuccessBanner = computed(
    () =>
        (state.value === 'success' || state.value === 'cached') &&
        !!successMessage.value,
);

const isFetchDisabled = computed(
    () =>
        isBusy.value ||
        isUsageLimitReached.value ||
        Number.isNaN(propertyId.value),
);

const isReportDisabled = computed(
    () => isBusy.value || !hasWorth.value || Number.isNaN(propertyId.value),
);

const upgradeHref = '/settings/billing';

const confidence = computed(() => worth.value?.confidence ?? null);
const valueLow = computed(() => worth.value?.value_low ?? null);
const valueHigh = computed(() => worth.value?.value_high ?? null);
const rentalValue = computed(() => worth.value?.rental_value ?? null);

const hasRentalValue = computed(
    () =>
        rentalValue.value !== null &&
        rentalValue.value !== undefined &&
        rentalValue.value > 0,
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
    sqft: propertySummary.value?.livingArea ?? null,
    yearBuilt: propertySummary.value?.yearBuilt ?? null,
}));

const comparablesCount = computed(() => comparables.value.length);
const trendCount = computed(() => trendPoints.value.length);

const idleCallout = computed(() =>
    isUsageLimitReached.value
        ? 'Usage limit reached — upgrade your plan to fetch a fresh valuation.'
        : 'No valuation yet — click “Fetch Valuation” to pull the latest data.',
);
</script>

<template>
    <div class="flex flex-col gap-6 text-[#0d0d12]">
        <header
            class="neu-surface flex flex-col gap-5 rounded-[28px] p-6 transition-all duration-200 ease-in-out"
        >
            <div
                class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"
            >
                <div class="space-y-2">
                    <p
                        class="text-xs tracking-[0.28em] text-[#7c4dff] uppercase"
                    >
                        AI Appraisal
                    </p>
                    <h2
                        class="text-2xl font-semibold tracking-tight text-[#0d0d12]"
                    >
                        Instant property valuation and market confidence.
                    </h2>
                    <p class="text-sm text-[#6b7280]">
                        Fetch live AVM data, comps, and confidence scores with a
                        single click.
                    </p>
                </div>

                <div
                    class="flex w-full flex-col items-stretch gap-3 sm:flex-row sm:items-center sm:justify-end lg:max-w-sm"
                >
                    <span
                        class="neu-surface inline-flex items-center gap-2 self-start rounded-full px-4 py-2 text-xs font-semibold tracking-[0.3em] text-[#7c4dff] uppercase "
                    >
                        API
                        <span class="font-medium">{{ endpointBadge }}</span>
                    </span>

                    <Button
                        type="button"
                        :disabled="isFetchDisabled"
                        class="bg-primary-400 "
                        @click="handleFetch"
                    >
                        <component
                            :is="isFetchLoading ? Loader2 : RefreshCw"
                            :class="[
                                'h-4 w-4',
                                { 'animate-spin': isFetchLoading },
                            ]"
                        />
                        {{
                            isUsageLimitReached
                                ? 'Limit reached'
                                : 'Fetch valuation'
                        }}
                    </Button>
                </div>
            </div>

            <transition name="fade">
                <div
                    v-if="showSuccessBanner"
                    class="neu-surface flex items-center gap-3 rounded-[20px] bg-white px-4 py-3 text-sm text-[#0d0d12] shadow-neu-out"
                >
                    <ShieldCheck class="h-5 w-5 text-[#1dbf7a]" />
                    <span>{{ successMessage }}</span>
                </div>
            </transition>

            <div
                class="grid gap-4 md:grid-cols-2 lg:grid-cols-[minmax(0,320px)_1fr]"
            >
                <div
                    class="flex flex-col gap-3 rounded-[22px] bg-[#f4f5fa] p-4 text-xs text-[#6b7280] shadow-[inset_12px_12px_28px_rgba(210,212,226,0.55),inset_-12px_-12px_28px_rgba(255,255,255,0.95)]"
                >
                    <div
                        class="flex items-center justify-between text-xs font-semibold tracking-[0.3em] uppercase"
                    >
                        <span>Plan usage</span>
                        <span class="inline-flex items-center gap-2">
                            <Gauge class="h-4 w-4 text-[#7c4dff]" />
                            {{ usageLabel }}
                        </span>
                    </div>
                    <div
                        class="h-2 rounded-full bg-white shadow-[inset_6px_6px_12px_rgba(204,206,214,0.5),inset_-6px_-6px_12px_rgba(255,255,255,0.95)]"
                    >
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-[#7c4dff] to-[#16b1ff]"
                            :style="usageMeterStyle"
                        />
                    </div>
                    <p class="text-xs text-[#6b7280]">
                        {{ helperCopy }}
                    </p>
                    <a
                        v-if="isUsageLimitReached"
                        :href="upgradeHref"
                        class="inline-flex items-center justify-center gap-2 self-start rounded-[16px] bg-white px-4 py-2 text-xs font-semibold text-[#7c4dff] shadow-[8px_8px_20px_rgba(210,212,226,0.5),-8px_-8px_20px_rgba(255,255,255,0.95)] transition-all duration-200 ease-in-out hover:shadow-[inset_8px_8px_18px_rgba(210,212,226,0.5),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]"
                    >
                        Upgrade plan
                        <ArrowRight class="h-4 w-4" />
                    </a>
                </div>

                <div
                    v-if="lastFetchedLabel"
                    class="rounded-[22px] bg-white px-4 py-3 text-sm text-[#6b7280] shadow-[12px_12px_28px_rgba(210,212,226,0.45),-12px_-12px_28px_rgba(255,255,255,0.95)]"
                >
                    Last fetched on {{ lastFetchedLabel }}
                </div>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.55fr_1fr]">
            <article
                class="neu-surface flex flex-col gap-5 rounded-[28px] p-6 text-sm text-[#0d0d12]"
            >
                <header
                    class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between"
                >
                    <div>
                        <h3 class="text-lg font-semibold text-[#0d0d12]">
                            {{ stateTitle }}
                        </h3>
                        <p class="text-sm text-[#6b7280]">
                            {{ stateSubtitle }}
                        </p>
                        <p
                            v-if="state === 'cached' && lastFetchedLabel"
                            class="mt-2 text-xs font-semibold tracking-[0.28em] text-[#f59e0b] uppercase"
                        >
                            Cached — refresh recommended
                        </p>
                    </div>
                    <LineChart class="h-6 w-6 text-[#7c4dff]" />
                </header>

                <div v-if="state === 'loading'" class="space-y-4">
                    <div
                        class="h-40 animate-pulse rounded-[24px] bg-[#f4f5fa] shadow-[inset_12px_12px_30px_rgba(210,212,226,0.6),inset_-12px_-12px_30px_rgba(255,255,255,0.92)]"
                    />
                    <div
                        class="h-32 animate-pulse rounded-[24px] bg-[#f4f5fa] shadow-[inset_12px_12px_30px_rgba(210,212,226,0.6),inset_-12px_-12px_30px_rgba(255,255,255,0.92)]"
                    />
                </div>

                <div
                    v-else-if="state === 'error'"
                    class="flex flex-col gap-4 rounded-[24px] bg-[#fff5f5] p-6 text-[#9a1b1b] shadow-[12px_12px_28px_rgba(244,200,200,0.55),-12px_-12px_28px_rgba(255,255,255,0.95)]"
                >
                    <div class="flex items-center gap-3 text-sm">
                        <AlertCircle class="h-5 w-5" />
                        <span>{{ errorDisplayMessage }}</span>
                    </div>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 self-start rounded-[16px] bg-white px-4 py-2 text-sm font-semibold text-[#9a1b1b] shadow-[8px_8px_20px_rgba(245,216,216,0.6),-8px_-8px_20px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_8px_8px_18px_rgba(236,187,187,0.55),inset_-8px_-8px_18px_rgba(255,247,247,0.9)]"
                        @click="handleRetry"
                    >
                        Retry
                        <ArrowRight class="h-4 w-4" />
                    </button>
                </div>

                <template v-else-if="state === 'success' || state === 'cached'">
                    <div class="grid gap-4 lg:grid-cols-[1.3fr_1fr]">
                        <CardValuation
                            :value="worth?.value ?? null"
                            :value-low="valueLow"
                            :value-high="valueHigh"
                            :confidence="confidence"
                            :fetched-at="fetchedAt"
                            :is-stale="state === 'cached'"
                        />

                        <RentalValueCard
                            v-if="hasRentalValue"
                            :rental-value="rentalValue"
                        />
                        <div
                            v-else
                            class="neu-surface flex flex-col justify-center gap-2 rounded-[26px] p-6 text-sm text-[#6b7280] shadow-neu-out"
                        >
                            <p
                                class="text-xs font-semibold tracking-[0.3em] text-[#6b7280] uppercase"
                            >
                                Rental value
                            </p>
                            <p class="text-base text-[#0d0d12]">
                                Rental projections are not available yet. Fetch
                                a fresh valuation or add rental data to unlock
                                this card.
                            </p>
                        </div>
                    </div>

                    <AnalyticsChart v-if="hasTrend" :points="trendPoints" />

                    <ComparablesTable
                        :comparables="comparables"
                        :is-loading="false"
                    />

                    <div
                        class="flex flex-col gap-3 rounded-[22px] bg-[#f4f5fa] p-5 text-sm text-[#6b7280] shadow-[inset_10px_10px_24px_rgba(210,212,226,0.55),inset_-10px_-10px_24px_rgba(255,255,255,0.95)]"
                    >
                        <p class="text-sm text-[#5b21b6]">
                            Sync this valuation with PixrSeal to include comps
                            and trendline snapshots in investor reports.
                        </p>

                        <button
                            type="button"
                            :disabled="isReportDisabled"
                            class="neu-btn inline-flex items-center gap-2 self-start px-4 py-2 text-sm font-semibold text-[#7c4dff] transition-all duration-200 ease-in-out disabled:opacity-60"
                            @click="handleAddToReport"
                        >
                            Add to report
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </div>
                </template>

                <div
                    v-else
                    class="flex flex-col gap-3 rounded-[24px] bg-[#f4f5fa] p-6 text-sm text-[#6b7280] shadow-[inset_12px_12px_30px_rgba(210,212,226,0.6),inset_-12px_-12px_30px_rgba(255,255,255,0.92)]"
                >
                    <p class="text-base font-semibold text-[#0d0d12]">
                        No valuation yet
                    </p>
                    <p>
                        {{ idleCallout }}
                    </p>
                    <button
                        type="button"
                        :disabled="isFetchDisabled"
                        class="inline-flex items-center gap-2 self-start rounded-[16px] bg-white px-4 py-2 text-sm font-semibold text-[#7c4dff] shadow-[8px_8px_20px_rgba(210,212,226,0.6),-8px_-8px_20px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_8px_8px_18px_rgba(210,212,226,0.55),inset_-8px_-8px_18px_rgba(255,255,255,0.92)] disabled:cursor-not-allowed disabled:opacity-60"
                        @click="handleFetch"
                    >
                        Fetch valuation
                        <ArrowRight class="h-4 w-4" />
                    </button>
                </div>
            </article>

            <aside class="flex flex-col gap-4">
                <section
                    class="neu-surface flex flex-col gap-4 rounded-[28px] p-6"
                >
                    <header class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-[#0d0d12]">
                            Module signals
                        </h3>
                        <RefreshCw class="h-5 w-5 text-[#7c4dff]" />
                    </header>

                    <ul
                        class="grid gap-3 text-xs font-semibold tracking-[0.3em] text-[#6b7280] uppercase"
                    >
                        <li
                            class="flex items-center justify-between rounded-[18px] bg-[#f4f5fa] px-4 py-3 shadow-[inset_8px_8px_18px_rgba(210,212,226,0.5),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]"
                        >
                            <span>Status</span>
                            <span class="text-[#7c4dff]">{{
                                moduleStatusLabel
                            }}</span>
                        </li>
                        <li
                            class="flex items-center justify-between rounded-[18px] bg-[#f4f5fa] px-4 py-3 shadow-[inset_8px_8px_18px_rgba(210,212,226,0.5),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]"
                        >
                            <span>Local state</span>
                            <span class="text-[#0d0d12]">{{ state }}</span>
                        </li>
                        <li
                            class="flex items-center justify-between rounded-[18px] bg-[#f4f5fa] px-4 py-3 shadow-[inset_8px_8px_18px_rgba(210,212,226,0.5),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]"
                        >
                            <span>Comparables</span>
                            <span class="text-[#0d0d12]">{{
                                comparablesCount
                            }}</span>
                        </li>
                        <li
                            class="flex items-center justify-between rounded-[18px] bg-[#f4f5fa] px-4 py-3 shadow-[inset_8px_8px_18px_rgba(210,212,226,0.5),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]"
                        >
                            <span>Trend points</span>
                            <span class="text-[#0d0d12]">{{ trendCount }}</span>
                        </li>
                        <li
                            class="flex items-center justify-between rounded-[18px] bg-[#f4f5fa] px-4 py-3 shadow-[inset_8px_8px_18px_rgba(210,212,226,0.5),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]"
                        >
                            <span>Plan remaining</span>
                            <span class="text-[#0d0d12]">
                                {{ remaining }} / {{ usage.total }}
                            </span>
                        </li>
                        <li
                            v-if="lastFetchedLabel"
                            class="flex items-center justify-between rounded-[18px] bg-[#f4f5fa] px-4 py-3 shadow-[inset_8px_8px_18px_rgba(210,212,226,0.5),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]"
                        >
                            <span>Last fetched</span>
                            <span class="text-[#0d0d12]">{{
                                lastFetchedLabel
                            }}</span>
                        </li>
                    </ul>
                </section>

                <PropertyDetails
                    :beds="subjectDetails.beds"
                    :baths="subjectDetails.baths"
                    :sqft="subjectDetails.sqft"
                    :year-built="subjectDetails.yearBuilt"
                />

                <div
                    v-if="!hasComparables && state === 'success'"
                    class="rounded-[26px] bg-[#fffdf5] p-5 text-sm text-[#92400e] shadow-[12px_12px_28px_rgba(240,213,166,0.45),-12px_-12px_28px_rgba(255,250,232,0.95)]"
                >
                    Valuation delivered, but comparables are pending. Refresh in
                    a few minutes to load nearby sales data.
                </div>

                <div
                    v-if="errorMessage && state !== 'loading'"
                    class="rounded-[26px] bg-[#fff5f5] p-4 text-sm text-[#9a1b1b] shadow-[8px_8px_22px_rgba(244,200,200,0.55),-8px_-8px_22px_rgba(255,255,255,0.93)]"
                >
                    {{ errorMessage }}
                </div>
            </aside>
        </section>
    </div>
</template>
