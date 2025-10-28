<script setup lang="ts">
import { computed } from 'vue';
import { BarChart3, Building2, Calculator, ExternalLink } from 'lucide-vue-next';
import type {
    PropertyWorkspaceProperty,
    WorkspaceModuleMeta,
} from './types';

interface Props {
    property: PropertyWorkspaceProperty;
    meta?: WorkspaceModuleMeta | null;
    moduleId: string;
}

const props = defineProps<Props>();

const valuation = computed(() => props.property.pricing ?? {});

const endpointBadge = computed(() => props.meta?.endpoint ?? '/api/properties/:id/worth');

const comparables = computed(() => [
    {
        id: 'comp-1',
        address: '118 Willow St, Austin, TX',
        distance: '0.3 mi',
        price: 892000,
        delta: '+3.2%',
    },
    {
        id: 'comp-2',
        address: '501 Research Blvd, Austin, TX',
        distance: '0.6 mi',
        price: 905500,
        delta: '+4.1%',
    },
    {
        id: 'comp-3',
        address: '9802 Walnut Grove, Austin, TX',
        distance: '0.8 mi',
        price: 867400,
        delta: '+1.8%',
    },
]);
</script>

<template>
    <div class="flex flex-col gap-6 text-[#1f2937]">
        <header class="flex flex-col gap-4 rounded-[28px] bg-white p-6 shadow-[10px_10px_30px_rgba(205,207,222,0.45),-10px_-10px_30px_rgba(255,255,255,0.95)]">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight">PixrWorth Valuation</h2>
                    <p class="text-sm text-gray-500">
                        Live AVM blending HouseCanary data, comps, and Pixrup heuristics.
                    </p>
                </div>
                <span
                    class="hidden items-center gap-2 rounded-full bg-[#f7f4ff] px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#7c4dff] shadow-[4px_4px_12px_rgba(186,162,255,0.3),-4px_-4px_12px_rgba(255,255,255,0.9)] sm:inline-flex"
                >
                    API
                    <span class="font-medium">{{ endpointBadge }}</span>
                </span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-[22px] bg-[#f4f5fa] p-5 shadow-[inset_10px_10px_24px_rgba(197,200,218,0.6),inset_-10px_-10px_24px_rgba(255,255,255,0.92)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Current AVM</p>
                    <p class="mt-2 text-3xl font-semibold text-[#7c4dff]">
                        {{
                            valuation.currentEstimate
                                ? `$${Intl.NumberFormat('en-US').format(valuation.currentEstimate)}`
                                : '—'
                        }}
                    </p>
                    <p class="mt-2 text-sm text-gray-500">
                        Last run: {{ meta?.last_run_at ? new Date(meta.last_run_at).toLocaleString() : 'Pending' }}
                    </p>
                </div>

                <div class="rounded-[22px] bg-[#f4f5fa] p-5 shadow-[inset_10px_10px_24px_rgba(197,200,218,0.6),inset_-10px_-10px_24px_rgba(255,255,255,0.92)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Post Glow-Up</p>
                    <p class="mt-2 text-3xl font-semibold text-[#1f2937]">
                        {{
                            valuation.potentialAfterGlow
                                ? `$${Intl.NumberFormat('en-US').format(valuation.potentialAfterGlow)}`
                                : '—'
                        }}
                    </p>
                    <p class="mt-2 text-sm text-gray-500">
                        Delta vs. current: {{
                            valuation.potentialAfterGlow && valuation.currentEstimate
                                ? `${(((valuation.potentialAfterGlow - valuation.currentEstimate) / valuation.currentEstimate) * 100).toFixed(1)}%`
                                : '—'
                        }}
                    </p>
                </div>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.3fr_1fr]">
            <article class="flex flex-col gap-5 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(205,207,222,0.45),-10px_-10px_28px_rgba(255,255,255,0.95)]">
                <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-base font-semibold">Run Valuation</h3>
                        <p class="text-sm text-gray-500">
                            Trigger a fresh appraisal pulling the latest MLS snapshots.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-[18px] bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-[8px_8px_20px_rgba(108,72,219,0.45),-6px_-6px_18px_rgba(212,199,255,0.4)] transition hover:shadow-[inset_8px_8px_18px_rgba(86,55,176,0.6),inset_-6px_-6px_18px_rgba(158,132,255,0.6)] focus:outline-none focus:ring-2 focus:ring-[#7c4dff]/60"
                    >
                        <Calculator class="h-4 w-4" />
                        Run PixrWorth
                    </button>
                </header>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-[20px] bg-[#f7f8fe] p-5 shadow-[inset_8px_8px_18px_rgba(201,203,217,0.6),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Data Sources</p>
                        <ul class="mt-3 space-y-2 text-sm text-gray-600">
                            <li>HouseCanary Valuation Suite</li>
                            <li>Zillow Bridge MLS refresh (hourly)</li>
                            <li>Pixrup Renovation Heuristics</li>
                        </ul>
                    </div>

                    <div class="rounded-[20px] bg-[#f7f8fe] p-5 shadow-[inset_8px_8px_18px_rgba(201,203,217,0.6),inset_-8px_-8px_18px_rgba(255,255,255,0.9)]">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Confidence</p>
                        <p class="mt-3 text-2xl font-semibold text-[#1f2937]">High (87%)</p>
                        <p class="mt-1 text-sm text-gray-500">
                            Based on 14 active comps and 3 closed in last 30 days within 0.8 miles.
                        </p>
                    </div>
                </div>
            </article>

            <aside class="flex flex-col gap-4 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(205,207,222,0.45),-10px_-10px_28px_rgba(255,255,255,0.95)]">
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Top Comparables</h3>
                    <BarChart3 class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <ul class="space-y-4 text-sm text-gray-600">
                    <li
                        v-for="comp in comparables"
                        :key="comp.id"
                        class="rounded-[20px] bg-[#f4f5fa] p-4 shadow-[inset_8px_8px_18px_rgba(201,203,217,0.6),inset_-8px_-8px_18px_rgba(255,255,255,0.92)]"
                    >
                        <p class="font-semibold text-[#1f2937]">{{ comp.address }}</p>
                        <p class="mt-1 text-xs uppercase tracking-[0.25em] text-gray-400">
                            {{ comp.distance }} • ${{ Intl.NumberFormat('en-US').format(comp.price) }}
                        </p>
                        <p class="mt-1 text-sm text-[#0f766e]">Change vs. subject: {{ comp.delta }}</p>
                    </li>
                </ul>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 self-start rounded-[18px] bg-[#f7f4ff] px-4 py-2 text-sm font-semibold text-[#7c4dff] shadow-[6px_6px_16px_rgba(186,162,255,0.4),-6px_-6px_16px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_6px_6px_16px_rgba(161,133,240,0.55),inset_-6px_-6px_16px_rgba(243,236,255,0.9)] focus:outline-none focus:ring-2 focus:ring-[#7c4dff]/50"
                >
                    Export comps
                    <ExternalLink class="h-4 w-4" />
                </button>

                <div class="flex items-center gap-2 rounded-[18px] bg-[#f4f5fa] px-4 py-3 text-xs uppercase tracking-[0.3em] text-gray-400 shadow-[inset_6px_6px_14px_rgba(201,203,217,0.6),inset_-6px_-6px_14px_rgba(255,255,255,0.92)]">
                    <Building2 class="h-4 w-4 text-[#7c4dff]" />
                    MLS Sync hourly
                </div>
            </aside>
        </section>
    </div>
</template>
