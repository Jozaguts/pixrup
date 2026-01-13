<script setup lang="ts">
import { Activity, ClipboardList, Home, MapPin, RefreshCw, ShieldAlert, Waves } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { PropertyWorkspaceProperty, WorkspaceModuleMeta } from './types';
import propertiesRoutes from '@/routes/properties';
import KeySingalCard from '@/components/properties/workspace/overview/KeySingalCard.vue';

interface SignalCard {
    label: string;
    value: string;
    detail: string | null;
    icon: any;
}

interface LocationItem {
    label: string;
    value: string;
    detail?: string | null;
}

interface Props {
    property: PropertyWorkspaceProperty;
    meta?: WorkspaceModuleMeta | null;
    moduleId: string;
}

const props = defineProps<Props>();

const lastRunCopy = computed(() => {
    if (!props.meta?.last_run_at) {
        return 'Awaiting first sync.';
    }

    const date = new Date(props.meta.last_run_at);
    return `Synced on ${date.toLocaleString(undefined, {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })}`;
});
const loading = ref(false);
const overviewError = ref<string | null>(null);
const overview = ref<any>(null);

const snapshot = computed(() => overview.value?.snapshot ?? null);
const payload = computed(() => snapshot.value?.payload ?? {});
const census = computed(() => payload.value?.census ?? {});
const hazards = computed(() => payload.value?.hazards ?? {});
const femaArea = computed(() => hazards.value?.fema_disaster_area ?? {});
const femaDetails = computed(() => (Array.isArray(femaArea.value?.details) ? femaArea.value.details : []));
const crime = computed(() => payload.value?.block_crime ?? {});
const crimeStats = computed(() => crime.value?.property ?? crime.value?.all ?? {});
const salesHistory = computed(() => (Array.isArray(payload.value?.sales_history) ? payload.value.sales_history : []));

const salesCount = computed(() => salesHistory.value.length);
const femaCount = computed(() => femaDetails.value.length);
const crimeDetail = computed(() => {
    const scope = snapshot.value?.crime_compare_scope;
    const incidents = crimeStats.value?.incidents;
    const parts = [];
    if (scope) {
        parts.push(`${scope} scope`);
    }
    if (incidents !== null && incidents !== undefined) {
        parts.push(`${incidents} incidents`);
    }
    return parts.length ? parts.join(' | ') : 'No crime data';
});

const signalCards = computed<SignalCard[]>(() => {
    if (!snapshot.value) {
        return [];
    }

    return [
        {
            label: 'Owner occupied',
            value: formatBool(snapshot.value.owner_occupied),
            detail: snapshot.value.owner_occupied === null ? 'Status not reported' : null,
            icon: Home,
        },
        {
            label: 'FEMA disaster area',
            value: formatBool(snapshot.value.fema_disaster_area),
            detail: femaCount.value > 0 ? `${femaCount.value} declarations` : 'No declarations',
            icon: ShieldAlert,
        },
        {
            label: 'Flood zone',
            value: snapshot.value.flood_zone ?? '-',
            detail: snapshot.value.flood_risk ? `Risk: ${formatTitle(snapshot.value.flood_risk)}` : 'Risk: -',
            icon: Waves,
        },
        {
            label: 'Crime percentile',
            value:
                snapshot.value.crime_percentile !== null && snapshot.value.crime_percentile !== undefined
                    ? `${snapshot.value.crime_percentile}th`
                    : '-',
            detail: crimeDetail.value,
            icon: Activity,
        },
    ];
});

const signalByLabel = computed(() => {
    const map = new Map<string, SignalCard>();
    for (const signal of signalCards.value) {
        map.set(signal.label, signal);
    }
    return map;
});

const locationItems = computed<LocationItem[]>(() => {
    if (!snapshot.value) {
        return [];
    }

    return [
        {
            label: 'MSA',
            value: snapshot.value.msa ?? '-',
            detail: snapshot.value.msa_name ?? null,
        },
        {
            label: 'County',
            value: census.value?.county_name ?? '-',
            detail: census.value?.fips ? `FIPS ${census.value.fips}` : null,
        },
        {
            label: 'Census tract',
            value: snapshot.value.census_tract ?? '-',
        },
        {
            label: 'Block group',
            value: snapshot.value.block_group ?? '-',
        },
    ];
});

const locationByLabel = computed(() => {
    const map = new Map<string, LocationItem>();
    for (const item of locationItems.value) {
        map.set(item.label, item);
    }
    return map;
});

const recentSales = computed(() => {
    const sorted = [...salesHistory.value]
        .filter(Boolean)
        .sort((a, b) => toTimestamp(b?.record_date) - toTimestamp(a?.record_date))
        .slice(0, 2);

    return sorted.map((sale, index) => ({
        key: sale?.record_doc ?? sale?.record_date ?? `sale-${index}`,
        dateLabel: formatDate(sale?.record_date),
        typeLabel: formatTitle(sale?.event_type) ?? 'Sale',
        parties: formatParties(sale),
    }));
});

const latestFema = computed(() => {
    if (!femaDetails.value.length) {
        return null;
    }

    const sorted = [...femaDetails.value].sort(
        (a, b) =>
            toTimestamp(b?.declared_date ?? b?.end_date ?? b?.start_date) -
            toTimestamp(a?.declared_date ?? a?.end_date ?? a?.start_date),
    );
    const event = sorted[0];

    return {
        title: event?.title ?? 'Declaration on file',
        type: event?.type ? formatTitle(event.type) : null,
        dateLabel: formatDate(event?.declared_date ?? event?.end_date ?? event?.start_date),
    };
});

const latestFemaTitle = computed(() => latestFema.value?.title ?? 'No FEMA declarations available.');
const latestFemaBadge = computed(() => latestFema.value?.type ?? 'FEMA');
const latestFemaDate = computed(() => latestFema.value?.dateLabel ?? '-');
async function loadOverView() {
    loading.value = true;
    overviewError.value = null;
    try {
        const res = await fetch(propertiesRoutes.overview.get(props.property.id as number).url, {
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
        });
        const json = await res.json();
        overview.value = json.data ?? json;
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
    } catch (error) {
        overviewError.value = 'Unable to load overview right now.';
    } finally {
        loading.value = false;
    }
}
loadOverView();

function formatBool(value: unknown): string {
    if (value === true) {
        return 'Yes';
    }
    if (value === false) {
        return 'No';
    }
    return 'Unknown';
}

function formatDate(value?: string): string {
    if (!value) {
        return '-';
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function formatTitle(value?: string): string | null {
    if (!value) {
        return null;
    }
    return value
        .toString()
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (letter) => letter.toUpperCase());
}

function formatParties(sale: any): string {
    const grantee = [sale?.grantee_1_forenames, sale?.grantee_1].filter(Boolean).join(' ') || sale?.grantee_1;
    const grantor = [sale?.grantor_1_forenames, sale?.grantor_1].filter(Boolean).join(' ') || sale?.grantor_1;
    if (grantee && grantor) {
        return `${grantee} from ${grantor}`;
    }
    return grantee || grantor || 'Parties not listed';
}

function toTimestamp(value?: string): number {
    if (!value) {
        return 0;
    }
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? 0 : date.getTime();
}
</script>

<template>
    <div class="pt-6 flex flex-col gap-6 text-accent">
        <section class="grid h-100 gap-6">
            <article class="npo-form-shadow flex flex-col gap-6 rounded-[12px] p-6 text-accent">
                <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-1">
                        <h3 class="text-lg font-semibold">Key Investment Signals</h3>
                        <p class="text-sm text-accent/50">
                            Blend of valuation, renovation and market sentiment across modules.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-end">
                        <button
                            type="button"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 rounded-[12px] bg-primary/50 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#5f2fe0] disabled:cursor-not-allowed disabled:opacity-70"
                            @click="loadOverView"
                        >
                            <RefreshCw :class="['h-4 w-4', { 'animate-spin': loading }]" />
                            Refresh
                        </button>
                    </div>
                </header>

                <div v-if="loading" class="grid gap-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="h-24 animate-pulse rounded-[16px] bg-surface shadow-neu-in" />
                        <div class="h-24 animate-pulse rounded-[16px] bg-surface shadow-neu-in" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div class="h-20 animate-pulse rounded-[16px] bg-surface shadow-neu-in" />
                        <div class="h-20 animate-pulse rounded-[16px] bg-surface shadow-neu-in" />
                        <div class="hidden h-20 animate-pulse rounded-[16px] bg-surface shadow-neu-in lg:block" />
                    </div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="h-40 animate-pulse rounded-[16px] bg-surface shadow-neu-in" />
                        <div class="h-40 animate-pulse rounded-[16px] bg-surface shadow-neu-in" />
                    </div>
                </div>

                <div
                    v-else-if="overviewError"
                    class="rounded-[16px] bg-surface p-4 text-sm text-accent/50 shadow-neu-in"
                >
                    {{ overviewError }}
                </div>

                <div v-else-if="!snapshot" class="rounded-[16px] bg-surface p-4 text-sm text-accent/50 shadow-neu-in">
                    Overview data is not available yet. Fetch a snapshot to unlock signals.
                </div>

                <div v-else class="flex flex-col gap-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <KeySingalCard
                            :icon="signalByLabel.get('Owner occupied')?.icon ?? Home"
                            :value="signalByLabel.get('Owner occupied')?.value ?? '-'"
                            :details="signalByLabel.get('Owner occupied')?.detail ?? ''"
                            title="Owner occupied"
                        />
                        <KeySingalCard
                            :icon="signalByLabel.get('FEMA disaster area')?.icon ?? Home"
                            :value="signalByLabel.get('FEMA disaster area')?.value ?? '-'"
                            :details="signalByLabel.get('FEMA disaster area')?.detail ?? ''"
                            title="FEMA disaster area"
                        />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <KeySingalCard
                            :icon="signalByLabel.get('Flood zone')?.icon ?? Home"
                            :value="signalByLabel.get('Flood zone')?.value ?? '-'"
                            :details="signalByLabel.get('Flood zone')?.detail ?? ''"
                            title=" Flood zone"
                        />
                        <KeySingalCard
                            :icon="signalByLabel.get('Crime percentile')?.icon ?? Home"
                            :value="signalByLabel.get('Crime percentile')?.value ?? '-'"
                            :details="signalByLabel.get('Crime percentile')?.detail ?? ''"
                            title="Crime percentile"
                        />
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <KeySingalCard :icon="MapPin" title="Location context">
                            <template #expand>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <div class="flex flex-col gap-4">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-[11px] font-semibold tracking-[0.2em] text-accent/50 uppercase"
                                            >
                                                {{ locationByLabel.get('MSA')?.label ?? 'MSA' }}
                                            </span>
                                            <span class="text-sm font-semibold break-words text-accent">
                                                {{ locationByLabel.get('MSA')?.value ?? '-' }}
                                            </span>
                                            <span
                                                v-if="locationByLabel.get('MSA')?.detail"
                                                class="text-xs break-words text-accent/50"
                                            >
                                                {{ locationByLabel.get('MSA')?.detail }}
                                            </span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-[11px] font-semibold tracking-[0.2em] text-accent/50 uppercase"
                                            >
                                                {{ locationByLabel.get('Census tract')?.label ?? 'Census tract' }}
                                            </span>
                                            <span class="text-sm font-semibold break-words text-accent">
                                                {{ locationByLabel.get('Census tract')?.value ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-4">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-[11px] font-semibold tracking-[0.2em] text-accent/50 uppercase"
                                            >
                                                {{ locationByLabel.get('County')?.label ?? 'County' }}
                                            </span>
                                            <span class="text-sm font-semibold break-words text-accent">
                                                {{ locationByLabel.get('County')?.value ?? '-' }}
                                            </span>
                                            <span
                                                v-if="locationByLabel.get('County')?.detail"
                                                class="text-xs break-words text-accent/50"
                                            >
                                                {{ locationByLabel.get('County')?.detail }}
                                            </span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-[11px] font-semibold tracking-[0.2em] text-accent/50 uppercase"
                                            >
                                                {{ locationByLabel.get('Block group')?.label ?? 'Block group' }}
                                            </span>
                                            <span class="text-sm font-semibold break-words text-accent">
                                                {{ locationByLabel.get('Block group')?.value ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </KeySingalCard>
                        <KeySingalCard
                            :icon="ClipboardList"
                            title="Activity snapshot"
                            value="Recent sales and FEMA signals."
                            :details="'Sales history | ' + salesCount + ' events'"
                        >
                            <template #expand>
                                <div class="flex flex-col gap-4 rounded-[16px]">
                                    <div class="flex flex-col gap-3">
                                        <div v-if="recentSales.length" class="grid gap-3">
                                            <div
                                                v-for="sale in recentSales"
                                                :key="sale.key"
                                                class="border-b-1 border-accent/20 bg-background p-3"
                                            >
                                                <div
                                                    class="flex w-full flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                                                >
                                                    <div class="min-w-0">
                                                        <p class="text-xs text-accent/50">{{ sale.dateLabel }}</p>
                                                        <p class="text-sm break-words text-accent sm:truncate">
                                                            {{ sale.parties }}
                                                        </p>
                                                    </div>
                                                    <span
                                                        class="self-start px-3 py-1 text-[10px] font-semibold tracking-[0.2em] text-accent uppercase sm:self-center"
                                                    >
                                                        {{ sale.typeLabel }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <p v-else class="text-xs text-accent/50">No sales history recorded.</p>
                                    </div>

                                    <div class="h-px w-full"></div>

                                    <div class="flex flex-col gap-3">
                                        <div class="flex flex-wrap items-center justify-between gap-2 p-3 text-xs">
                                            <span class="min-w-0 break-words text-accent uppercase"
                                                >FEMA declarations</span
                                            >
                                            <span class="text-xs font-semibold tracking-[0.2em] text-accent">
                                                {{ femaCount }} events
                                            </span>
                                        </div>
                                        <div v-if="latestFema" class="rounded-[12px] bg-background p-3">
                                            <div
                                                class="flex flex-wrap items-center justify-between gap-2 sm:flex-nowrap sm:gap-4"
                                            >
                                                <div class="min-w-0">
                                                    <p class="text-xs text-accent/50">{{ latestFemaDate }}</p>
                                                    <p class="truncate text-sm text-accent">{{ latestFemaTitle }}</p>
                                                </div>
                                                <span
                                                    class="px-3 py-1 text-[10px] font-semibold tracking-[0.2em] text-accent uppercase"
                                                >
                                                    {{ latestFemaBadge }}
                                                </span>
                                            </div>
                                        </div>
                                        <p v-else class="text-xs text-accent/50">No FEMA declarations available.</p>
                                    </div>
                                </div>
                            </template>
                        </KeySingalCard>
                    </div>
                </div>

                <footer class="flex flex-wrap items-center gap-2 text-xs text-accent/40">
                    <span>Sources: FEMA, county records, and partner datasets.</span>
                    <button type="button" class="font-semibold text-[#6e33ff] transition hover:text-[#7c4dff]">
                        Use discretion
                    </button>
                </footer>
            </article>
        </section>
    </div>
</template>
