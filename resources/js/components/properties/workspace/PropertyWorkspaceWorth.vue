<script setup lang="ts">
import ToastAlert from '@/components/shared/ToastAlert.vue';
import { usePlanUsage } from '@/composables/usePlanUsage';
import http from '@/lib/http';
import propertiesRoutes from '@/routes/properties';
import { useForm, usePage } from '@inertiajs/vue3';
import { AlertCircle, ArrowRight, Gauge, LineChart, Loader2, RefreshCw } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';
import type {
    PropertyWorkspaceProperty,
    WorkspaceModuleMeta,
    WorthResult,
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

const worthState = ref<WorthResult | null>(props.property.worth ?? null);
const worth = computed(() => worthState.value);
const propertyWorthValue = computed(() => {
    if (!worth.value || worth.value.value === null || worth.value.value === undefined) {
        return 'N/A';
    }

    return `$${worth.value.value.toLocaleString()}`;
});

const hasWorth = computed(() => worth.value !== null && worth.value !== undefined);
const hasComparables = computed(() => worth.value?.comparables.some((comp) => comp.sale_price !== null));
const comparablesCount = computed(() => worth.value?.comparables.length ?? 0);

const trendPoints = computed<WorthTrendPoint[]>(() => worth.value?.trend ?? []);
const hasTrend = computed(() => trendPoints.value.length > 0);

const reportForm = useForm({});
const isFetchLoading = ref(false);
const isReportLoading = computed(() => reportForm.processing);
const isBusy = computed(() => isFetchLoading.value || isReportLoading.value);
const isWorthLoading = computed(() => isFetchLoading.value);

watch(
    () => props.property.worth,
    (value) => {
        if (!isFetchLoading.value) {
            worthState.value = value ?? null;
        }
    },
);

const { usage, remaining, limitExceeded: isUsageLimitReached, percentUsed, usageLabel, helperCopy } = usePlanUsage();

const usageMeterStyle = computed(() => ({
    width: `${Math.min(100, Math.max(0, percentUsed.value))}%`,
}));

const page = usePage();
const flashStatus = computed(() => page.props.flash?.status ?? null);
const fetchErrorMessage = ref<string | null>(null);
const fetchErrorCode = ref<string | null>(null);
const fetchSuccessMessage = ref<string | null>(null);
const showFetchErrorToast = ref(false);
const showSuccessToast = ref(false);

const successMessage = computed(() => {
    if (fetchSuccessMessage.value) {
        return fetchSuccessMessage.value;
    }

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
    if (fetchErrorMessage.value) {
        return fetchErrorMessage.value;
    }

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
    if (isWorthLoading.value) {
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

const isFetchDisabled = computed(
    () => isWorthLoading.value || isUsageLimitReached.value || Number.isNaN(propertyId.value),
);

const isReportDisabled = computed(() => isBusy.value || !hasWorth.value || Number.isNaN(propertyId.value));

const upgradeHref = '/settings/billing';

const confidence = computed(() => worth.value?.confidence ?? null);
const valueLow = computed(() => worth.value?.value_low ?? null);
const valueHigh = computed(() => worth.value?.value_high ?? null);
const rentalValue = computed(() => worth.value?.rental_value ?? null);

const hasRentalValue = computed(
    () => rentalValue.value !== null && rentalValue.value !== undefined && rentalValue.value > 0,
);

const normalizeWorthPayload = (payload: Partial<WorthResult>): WorthResult => ({
    id: payload.id ?? worthState.value?.id ?? 0,
    value: payload.value ?? null,
    value_low: payload.value_low ?? null,
    value_high: payload.value_high ?? null,
    confidence: payload.confidence ?? null,
    comparables: payload.comparables ?? [],
    trend: payload.trend ?? worthState.value?.trend ?? [],
    provider: payload.provider ?? null,
    fetched_at: payload.fetched_at ?? null,
    cached_at: payload.cached_at ?? null,
    rental_value: payload.rental_value ?? worthState.value?.rental_value ?? null,
});

const triggerErrorToast = (message: string, code: string | null = null): void => {
    fetchErrorMessage.value = message;
    fetchErrorCode.value = code;
    showFetchErrorToast.value = false;

    nextTick(() => {
        showFetchErrorToast.value = true;
        window.setTimeout(() => {
            showFetchErrorToast.value = false;
        }, 5200);
    });
};

const handleFetch = async () => {
    if (isFetchDisabled.value) {
        return;
    }

    const route = propertiesRoutes.worth.fetch({
        property: propertyId.value,
    });

    isFetchLoading.value = true;
    fetchErrorMessage.value = null;
    fetchErrorCode.value = null;
    fetchSuccessMessage.value = null;
    showFetchErrorToast.value = false;

    try {
        const response = await http.post(route.url);
        const payload = response?.data ?? {};
        if (!payload?.worth) {
            throw new Error('Worth payload missing');
        }

        worthState.value = normalizeWorthPayload(payload.worth as Partial<WorthResult>);
        fetchSuccessMessage.value = 'Appraisal completed successfully 🎯';
    } catch (error: unknown) {
        const response = (error as { response?: { data?: { message?: string; code?: string } } })?.response;
        const message =
            typeof response?.data?.message === 'string' && response.data.message.trim().length > 0
                ? response.data.message
                : 'We couldn’t retrieve data. Please try again later.';
        const code = typeof response?.data?.code === 'string' ? response.data.code : null;

        triggerErrorToast(message, code);
    } finally {
        isFetchLoading.value = false;
    }
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

watch(successMessage, (value, previous) => {
    if (!value || value === previous) {
        return;
    }

    showSuccessToast.value = false;

    nextTick(() => {
        showSuccessToast.value = true;
        window.setTimeout(() => {
            showSuccessToast.value = false;
        }, 4200);
    });
});
</script>

<template>
    <div class="flex flex-col gap-6 pt-6 text-accent">
        <ToastAlert
            v-if="showFetchErrorToast"
            :visible="showFetchErrorToast"
            type="error"
            :title="fetchErrorCode === 'limit' ? 'Plan limit reached' : 'Unable to fetch valuation'"
            :msg="errorDisplayMessage"
            :timer="5200"
            :show-confirm-button="false"
            :show-close-button="true"
            :redirect-url="fetchErrorCode === 'limit' ? upgradeHref : undefined"
        />
        <ToastAlert
            v-if="showSuccessToast"
            :visible="showSuccessToast"
            type="success"
            title="Appraisal ready"
            :msg="successMessage ?? ''"
            :timer="4200"
            :show-confirm-button="false"
            :show-close-button="true"
        />
        <section class="grid h-100 grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2">
            <div class="flex gap-6">
                <article class="npo-form-shadow flex w-full flex-col gap-4 rounded-[12px] p-6 text-sm text-accent">
                    <header class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                        <div class="space-y-1">
                            <h2 class="text-2xl font-semibold tracking-tight text-accent">Estimate property value</h2>
                            <h2 class="text-2xl font-semibold tracking-tight text-accent">
                                {{ propertyWorthValue }}
                            </h2>
                            <div class="flex items-center gap-2">
                                <p class="text-sm text-accent">Confidence: {{ confidence }}%</p>
                                <div class="flex gap-x-1">
                                    <span
                                        class="h-2 w-2 rounded-full bg-primary text-white"
                                        v-for="item in 5"
                                        :key="item"
                                        :class="item * 20 < confidence ? 'bg-primary' : 'bg-primary/50'"
                                    />
                                </div>
                            </div>
                            <small class="text-accent/50"
                                >Based on {{ comparablesCount }} nearby comparable sales.</small
                            >
                        </div>
                        <div class="flex flex-col flex-wrap items-end">
                            <button
                                type="button"
                                :disabled="isFetchDisabled"
                                class="inline-flex items-center gap-2 rounded-[12px] bg-[#6e33ff] px-4 py-2 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(110,51,255,0.35)] transition hover:bg-[#5f2fe0] disabled:cursor-not-allowed disabled:opacity-70"
                                @click="handleFetch"
                            >
                                <component
                                    :is="isFetchLoading ? Loader2 : RefreshCw"
                                    :class="['h-4 w-4', { 'animate-spin': isFetchLoading }]"
                                />
                                {{ isUsageLimitReached ? 'Limit reached' : 'Fetch valuation' }}
                            </button>
                            <div v-if="lastFetchedLabel" class="mt-2 text-right text-sm text-accent/50">
                                Last fetched on {{ lastFetchedLabel }}
                            </div>
                        </div>
                    </header>
                    <div class="flex flex-col gap-5 rounded-[12px] transition-all duration-200 ease-in-out">
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-[minmax(0,420px)_1fr]">
                            <div v-if="isWorthLoading" class="flex flex-col rounded-[12px] text-xs text-accent">
                                <div class="h-4 w-28 animate-pulse rounded-full bg-surface shadow-neu-in" />
                                <div class="h-3 w-full animate-pulse rounded-full bg-surface shadow-neu-in" />
                                <div class="h-4 w-48 animate-pulse rounded-full bg-surface shadow-neu-in" />
                                <div class="h-8 w-24 animate-pulse rounded-[12px] bg-surface shadow-neu-in" />
                            </div>
                            <div
                                v-else
                                class="flex flex-col gap-3 rounded-[12px] border-1  p-4 text-xs text-accent shadow-neu-in"
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
                                <div class="relative h-3 w-full overflow-hidden rounded-full bg-surface shadow-neu-in">
                                    <div
                                        class="h-full rounded-[12px] bg-gradient-to-r from-[#7c4dff] to-[#16b1ff]"
                                        :style="usageMeterStyle"
                                    />
                                </div>
                                <p class="text-xs text-accent/50">
                                    {{ helperCopy }}
                                </p>

                                <a
                                    v-if="isUsageLimitReached"
                                    :href="upgradeHref"
                                    class="inline-flex items-center justify-center gap-2 self-start rounded-[12px] bg-background px-4 py-2 text-xs font-semibold text-[#7c4dff] shadow-[8px_8px_20px_rgba(210,212,226,0.5),-8px_-8px_20px_rgba(255,255,255,0.95)] transition-all duration-200 ease-in-out hover:shadow-[inset_8px_8px_18px_rgba(210,212,226,0.5),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]"
                                >
                                    Upgrade plan
                                    <ArrowRight class="h-4 w-4" />
                                </a>
                            </div>
                            <div class="">
                                <CardValuation
                                    v-if="state === 'success' || state === 'cached'"
                                    :fetched-at="fetchedAt ?? ''"
                                    :is-stale="isStale"
                                    :value="worth?.value"
                                    :value-low="valueLow"
                                    :value-high="valueHigh"
                                    :confidence="confidence"
                                />
                            </div>
                        </div>
                    </div>
                    <div v-if="isWorthLoading" class="space-y-4">
                        <div class="grid gap-4 rounded-[12px] lg:grid-cols-[1.3fr_1fr]">
                            <div class="h-40 animate-pulse rounded-[12px] bg-surface shadow-neu-in" />
                            <div class="h-40 animate-pulse rounded-[12px] bg-surface shadow-neu-in" />
                        </div>
                        <div class="h-36 animate-pulse rounded-[12px] bg-surface shadow-neu-in" />
                        <div class="h-44 animate-pulse rounded-[12px] bg-surface shadow-neu-in" />
                        <div class="h-20 animate-pulse rounded-[12px] bg-surface shadow-neu-in" />
                    </div>
                    <div
                        v-else-if="state === 'error'"
                        class="flex flex-col gap-4 rounded-[12px] bg-surface p-6 shadow-neu-in"
                    >
                        <div class="flex items-center gap-3 text-sm">
                            <AlertCircle class="h-5 w-5" />
                            <span>{{ errorDisplayMessage }}</span>
                        </div>

                        <button
                            type="button"
                            class="bg-sruface inline-flex items-center gap-2 self-start rounded-[12px] px-4 py-2 text-sm font-semibold text-[#9a1b1b]"
                            @click="handleRetry"
                        >
                            Retry
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </div>
                    <template v-if="state === 'success' || state === 'cached'">
                        <div class="mt-auto flex flex-col gap-3 rounded-[12px] p-5 text-sm text-accent/50">
                            <button
                                type="button"
                                :disabled="isReportDisabled"
                                class="neu-button active flex cursor-pointer items-center justify-center gap-2 rounded-[12px] !bg-transparent px-4 py-4 text-sm font-medium text-accent"
                                @click="handleAddToReport"
                            >
                                Add to report
                                <ArrowRight class="h-4 w-4" />
                            </button>
                        </div>
                    </template>
                </article>
            </div>
            <aside class="flex flex-col gap-4 rounded-[12px]">
                <ComparablesTable :comparables="worth?.comparables ?? []" :is-loading="!worth?.comparables" />
            </aside>
        </section>
    </div>
</template>
