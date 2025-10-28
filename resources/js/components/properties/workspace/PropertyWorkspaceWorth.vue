<script setup lang="ts">
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import {
    AlertCircle,
    ArrowRight,
    DollarSign,
    LineChart,
    Loader2,
    RefreshCw,
    ShieldCheck,
    TrendingUp,
} from 'lucide-vue-next';
import propertiesRoutes from '@/routes/properties';
import type {
    PropertyWorkspaceProperty,
    WorkspaceModuleMeta,
    WorthComparable,
    WorthStatusState,
} from './types';

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
const hasWorth = computed(() => worth.value !== null);
const comparables = computed<WorthComparable[]>(() => worth.value?.comparables ?? []);
const trendPoints = computed(() => worth.value?.trend ?? []);

const fetchForm = useForm({});
const reportForm = useForm({});
const isFetchLoading = computed(() => fetchForm.processing);
const isReportLoading = computed(() => reportForm.processing);
const isActionDisabled = computed(
    () => isFetchLoading.value || isReportLoading.value,
);

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

const errorMessage = computed(() => {
    const errors = page.props.errors ?? {};
    return typeof errors.worth === 'string' ? errors.worth : null;
});

const state = computed<WorthStatusState>(() => {
    if (isFetchLoading.value || isReportLoading.value) {
        return 'loading';
    }

    if (!hasWorth.value && errorMessage.value) {
        return 'error';
    }

    if (hasWorth.value) {
        return 'success';
    }

    return 'idle';
});

const formattedValue = computed(() =>
    worth.value?.value
        ? `$${Intl.NumberFormat('en-US').format(worth.value.value)}`
        : '—',
);

const confidenceLabel = computed(() =>
    worth.value?.confidence !== undefined && worth.value?.confidence !== null
        ? `${worth.value.confidence}%`
        : '—',
);

const deltaAfterGlow = computed(() => {
    const currentEstimate = worth.value?.value ?? null;
    const projected = currentEstimate ? currentEstimate * 1.06 : null;

    if (!currentEstimate || !projected) {
        return {
            label: '—',
            projected: '—',
        };
    }

    const diff = ((projected - currentEstimate) / currentEstimate) * 100;

    return {
        label: `${diff.toFixed(1)}%`,
        projected: `$${Intl.NumberFormat('en-US').format(Math.round(projected))}`,
    };
});

const stateTitle = computed(() => {
    switch (state.value) {
        case 'loading':
            return 'Fetching valuation…';
        case 'error':
            return 'Could not retrieve data.';
        case 'success':
            return 'Appraisal ready';
        default:
            return 'No appraisal yet';
    }
});

const showSuccessBanner = computed(
    () => state.value === 'success' && !!successMessage.value,
);

const handleRetry = () => {
    handleFetch();
};

const handleFetch = () => {
    if (isActionDisabled.value || Number.isNaN(propertyId.value)) {
        return;
    }

    const route = propertiesRoutes.worth.fetch.post({ property: propertyId.value });

    fetchForm.submit(route.method, route.url, {
        preserveScroll: true,
    });
};

const handleAddToReport = () => {
    if (isActionDisabled.value || Number.isNaN(propertyId.value) || !hasWorth.value) {
        return;
    }

    const route = propertiesRoutes.worth.report.post({ property: propertyId.value });

    reportForm.submit(route.method, route.url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="flex flex-col gap-6 text-[#0d0d12]">
        <header
            class="flex flex-col gap-4 rounded-[28px] bg-white p-6 shadow-[20px_20px_48px_rgba(209,210,225,0.55),-20px_-20px_48px_rgba(255,255,255,0.95)]"
        >
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.28em] text-[#7C4DFF]">
                        AI Appraisal
                    </p>
                    <h2 class="text-2xl font-semibold tracking-tight text-[#0d0d12]">
                        Instant property valuation and market confidence.
                    </h2>
                    <p class="text-sm text-[#6b7280]">
                        Fetch live AVM data, comps, and confidence scores with a single click.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                    <span
                        class="inline-flex items-center gap-2 self-start rounded-full bg-[#f2ecff] px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#7c4dff] shadow-[6px_6px_18px_rgba(186,162,255,0.35),-6px_-6px_18px_rgba(255,255,255,0.92)]"
                    >
                        API
                        <span class="font-medium">{{ endpointBadge }}</span>
                    </span>

                    <button
                        type="button"
                        :disabled="isActionDisabled"
                        class="inline-flex items-center gap-2 rounded-[18px] bg-[#7c4dff] px-5 py-3 text-sm font-semibold text-white shadow-[12px_12px_30px_rgba(108,72,219,0.5),-10px_-10px_28px_rgba(212,199,255,0.45)] transition hover:shadow-[inset_12px_12px_24px_rgba(86,55,176,0.55),inset_-10px_-10px_24px_rgba(158,132,255,0.55)] disabled:cursor-not-allowed disabled:opacity-60"
                        @click="handleFetch"
                    >
                        <component
                            :is="isFetchLoading ? Loader2 : RefreshCw"
                            :class="['h-4 w-4', { 'animate-spin': isFetchLoading }]"
                        />
                        Fetch Valuation
                    </button>
                </div>
            </div>

            <transition name="fade">
                <div
                    v-if="showSuccessBanner"
                    class="flex items-center gap-3 rounded-[20px] bg-[#f4f5fa] px-4 py-3 text-sm text-[#0d0d12] shadow-[inset_10px_10px_24px_rgba(210,212,226,0.6),inset_-10px_-10px_24px_rgba(255,255,255,0.92)]"
                >
                    <ShieldCheck class="h-5 w-5 text-[#1dbf7a]" />
                    <span>{{ successMessage }}</span>
                </div>
            </transition>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.55fr_1fr]">
            <article
                class="flex flex-col gap-4 rounded-[28px] bg-white p-6 shadow-[18px_18px_44px_rgba(209,210,225,0.52),-18px_-18px_44px_rgba(255,255,255,0.95)]"
            >
                <header class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-[#0d0d12]">{{ stateTitle }}</h3>
                        <p class="text-sm text-[#6b7280]">
                            {{ hasWorth ? 'Review the latest market signals for this property.' : 'No appraisal yet — click “Fetch Valuation”.' }}
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
                        <span>{{ errorMessage ?? 'Could not retrieve data.' }}</span>
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

                <div v-else-if="hasWorth" class="space-y-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div
                            class="rounded-[24px] bg-[#f4f5fa] p-6 shadow-[inset_14px_14px_34px_rgba(210,212,226,0.6),inset_-14px_-14px_34px_rgba(255,255,255,0.92)]"
                        >
                            <div class="flex items-center gap-2 text-xs uppercase tracking-[0.3em] text-[#6b7280]">
                                <DollarSign class="h-4 w-4 text-[#7c4dff]" />
                                Estimated Value
                            </div>
                            <p class="mt-3 text-4xl font-semibold text-[#7c4dff]">
                                {{ formattedValue }}
                            </p>
                            <p class="mt-2 text-sm text-[#6b7280]">
                                Last updated
                                {{
                                    worth?.fetched_at
                                        ? new Date(worth.fetched_at).toLocaleString()
                                        : 'just now'
                                }}
                            </p>
                        </div>

                        <div
                            class="rounded-[24px] bg-[#f4f5fa] p-6 shadow-[inset_14px_14px_34px_rgba(210,212,226,0.6),inset_-14px_-14px_34px_rgba(255,255,255,0.92)]"
                        >
                            <div class="flex items-center gap-2 text-xs uppercase tracking-[0.3em] text-[#6b7280]">
                                <ShieldCheck class="h-4 w-4 text-[#1dbf7a]" />
                                Confidence
                            </div>
                            <p class="mt-3 text-4xl font-semibold text-[#0d0d12]">
                                {{ confidenceLabel }}
                            </p>
                            <p class="mt-2 text-sm text-[#6b7280]">
                                Based on {{ comparables.length }} nearby comparables in the last 90 days.
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid gap-4 rounded-[24px] bg-[#f7f8fe] p-6 shadow-[inset_16px_16px_36px_rgba(208,210,228,0.55),inset_-16px_-16px_36px_rgba(255,255,255,0.92)] md:grid-cols-[1.1fr_1fr]"
                    >
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 text-sm font-semibold text-[#0d0d12]">
                                <TrendingUp class="h-5 w-5 text-[#7c4dff]" />
                                Market trend (90 days)
                            </div>

                            <div class="grid h-32 grid-cols-6 items-end gap-2">
                                <div
                                    v-for="point in trendPoints"
                                    :key="point.label"
                                    class="flex flex-col items-center justify-end gap-2 text-xs text-[#6b7280]"
                                >
                                    <div
                                        class="w-full rounded-full bg-gradient-to-b from-[#7c4dff] to-[#b298ff] shadow-[4px_8px_16px_rgba(124,77,255,0.35)]"
                                        :style="{
                                            height: `${Math.max(14, (point.value / (trendPoints[0]?.value ?? 1)) * 36)}px`,
                                        }"
                                    />
                                    <span class="uppercase tracking-wide">
                                        {{ point.label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col justify-between gap-4 rounded-[20px] bg-white p-5 text-sm text-[#6b7280] shadow-[10px_10px_24px_rgba(209,210,225,0.45),-10px_-10px_24px_rgba(255,255,255,0.93)]"
                        >
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-[#6b7280]">
                                    Post Glow-Up
                                </p>
                                <p class="mt-2 text-2xl font-semibold text-[#0d0d12]">
                                    {{ deltaAfterGlow.projected }}
                                </p>
                                <p class="mt-1 text-sm text-[#1dbf7a]">
                                    {{ deltaAfterGlow.label }} potential uplift
                                </p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-[#6b7280]">
                                    Provider
                                </p>
                                <p class="mt-1 font-semibold capitalize text-[#0d0d12]">
                                    {{ worth?.provider }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-base font-semibold text-[#0d0d12]">
                                Top Comparables
                            </h4>
                            <span class="text-xs uppercase tracking-[0.3em] text-[#6b7280]">
                                Last 90 days
                            </span>
                        </div>

                        <ul class="grid gap-3 md:grid-cols-3">
                            <li
                                v-for="comp in comparables"
                                :key="comp.id"
                                class="rounded-[20px] bg-[#f4f5fa] p-4 text-sm text-[#0d0d12] shadow-[inset_10px_10px_26px_rgba(210,212,226,0.6),inset_-10px_-10px_26px_rgba(255,255,255,0.92)]"
                            >
                                <p class="font-semibold text-[#0d0d12]">
                                    {{ comp.address }}
                                </p>
                                <p class="mt-1 text-xs uppercase tracking-[0.3em] text-[#6b7280]">
                                    {{ comp.distance }}
                                </p>
                                <p class="mt-1 text-sm text-[#7c4dff]">
                                    ${{ Intl.NumberFormat('en-US').format(comp.price) }}
                                </p>
                                <p class="mt-1 text-xs text-[#1dbf7a]">
                                    {{ comp.delta }} vs. subject
                                </p>
                            </li>
                        </ul>
                    </div>

                    <div class="flex flex-col gap-3 rounded-[24px] bg-[#f7f4ff] p-5 shadow-[12px_12px_26px_rgba(197,189,237,0.55),-12px_-12px_26px_rgba(255,255,255,0.95)]">
                        <p class="text-sm text-[#5b21b6]">
                            Sync this valuation with PixrSeal to include comps and trendline snapshots in investor reports.
                        </p>

                        <button
                            type="button"
                            :disabled="isActionDisabled || !hasWorth"
                            class="inline-flex items-center gap-2 self-start rounded-[18px] bg-white px-4 py-2 text-sm font-semibold text-[#7c4dff] shadow-[10px_10px_24px_rgba(186,162,255,0.45),-10px_-10px_24px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_10px_10px_20px_rgba(171,147,248,0.55),inset_-10px_-10px_20px_rgba(247,242,255,0.92)] disabled:opacity-60"
                            @click="handleAddToReport"
                        >
                            Add to Report
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </article>

            <aside
                class="flex flex-col gap-4 rounded-[28px] bg-white p-6 shadow-[18px_18px_44px_rgba(209,210,225,0.52),-18px_-18px_44px_rgba(255,255,255,0.95)]"
            >
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-[#0d0d12]">
                        Module Signals
                    </h3>
                    <RefreshCw class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <div
                    class="rounded-[22px] bg-[#f4f5fa] p-4 text-sm text-[#6b7280] shadow-[inset_12px_12px_28px_rgba(210,212,226,0.58),inset_-12px_-12px_28px_rgba(255,255,255,0.92)]"
                >
                    <p>
                        The appraisal updates dashboards, investor summaries, and downstream PixrSeal sections once ready.
                    </p>
                </div>

                <div
                    class="grid gap-3 rounded-[22px] bg-[#f7f8fe] p-5 shadow-[inset_12px_12px_30px_rgba(208,210,228,0.58),inset_-12px_-12px_30px_rgba(255,255,255,0.92)]"
                >
                    <div class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-[#6b7280]">
                        <span>Status</span>
                        <span class="font-semibold text-[#7c4dff]">
                            {{ state }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-[#6b7280]">
                        <span>Comparables</span>
                        <span class="font-semibold text-[#0d0d12]">
                            {{ comparables.length }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-[#6b7280]">
                        <span>Trend points</span>
                        <span class="font-semibold text-[#0d0d12]">
                            {{ trendPoints.length }}
                        </span>
                    </div>
                </div>

                <div
                    v-if="errorMessage && state !== 'loading'"
                    class="rounded-[20px] bg-[#fff5f5] p-4 text-sm text-[#9a1b1b] shadow-[8px_8px_22px_rgba(244,200,200,0.55),-8px_-8px_22px_rgba(255,255,255,0.93)]"
                >
                    {{ errorMessage }}
                </div>
            </aside>
        </section>
    </div>
</template>
