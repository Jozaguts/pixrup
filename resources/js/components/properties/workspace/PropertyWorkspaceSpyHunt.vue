<script setup lang="ts">
import { Compass, Radar, Target, TrendingUp } from 'lucide-vue-next';
import { computed } from 'vue';
import type { PropertyWorkspaceProperty, WorkspaceModuleMeta } from './types';

interface Props {
    property: PropertyWorkspaceProperty;
    meta?: WorkspaceModuleMeta | null;
    moduleId: string;
}

const props = defineProps<Props>();

const endpointBadge = computed(
    () => props.meta?.endpoint ?? '/api/properties/:id/spyhunt',
);

const marketSignals = computed(() => [
    {
        id: 'absorption',
        label: 'Absorption rate',
        value: '21 days',
        delta: '+4 days slower vs. last month',
    },
    {
        id: 'inventory',
        label: 'Active inventory',
        value: '18 listings',
        delta: '-12% MoM',
    },
    {
        id: 'medianPrice',
        label: 'Median list price',
        value: '$845k',
        delta: '+3.1% MoM',
    },
]);
</script>

<template>
    <div class="flex flex-col gap-6 text-[#1f2937]">
        <header class="flex flex-col gap-4 rounded-[28px] p-6">
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-lg font-semibold">PixrSpyHunt Intel</h2>
                    <p class="text-sm text-gray-500">
                        MLS bridge, Zillow, and proprietary buyer signals —
                        refreshed every hour.
                    </p>
                </div>
                <span
                    class="hidden items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold tracking-[0.3em] text-[#7c4dff] uppercase sm:inline-flex"
                >
                    API
                    <span class="font-medium">{{ endpointBadge }}</span>
                </span>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <article class="space-y-6 rounded-[28px] p-6">
                <div class="neu-surface p-6 shadow-neu-out">
                    <header class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold">
                                Heatmap radius
                            </h3>
                            <p class="text-sm text-gray-500">
                                Hover to preview competitor listings and buyer
                                interest.
                            </p>
                        </div>
                        <Radar class="h-5 w-5 text-[#7c4dff]" />
                    </header>

                    <div class="neu-surface mt-4 h-64 shadow-neu-in" />

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div
                            class="rounded-[18px] px-4 py-3 text-sm font-semibold text-[#1f2937]"
                        >
                            0.5 mi radius — 6 actives
                        </div>
                        <div
                            class="rounded-[18px] px-4 py-3 text-sm font-semibold text-[#1f2937]"
                        >
                            Buyer demand score: 8.3 / 10
                        </div>
                        <div
                            class="rounded-[18px] px-4 py-3 text-sm font-semibold text-[#1f2937]"
                        >
                            Avg DOM: 13 days
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div
                        v-for="signal in marketSignals"
                        :key="signal.id"
                        class="rounded-[22px] p-5 text-sm text-gray-600"
                    >
                        <p
                            class="text-xs tracking-[0.3em] text-gray-400 uppercase"
                        >
                            {{ signal.label }}
                        </p>
                        <p class="mt-2 text-xl font-semibold text-[#1f2937]">
                            {{ signal.value }}
                        </p>
                        <p class="mt-1 text-xs text-[#0f766e]">
                            {{ signal.delta }}
                        </p>
                    </div>
                </div>
            </article>

            <aside class="flex flex-col gap-5 rounded-[28px] p-6">
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Buyer Persona Radar</h3>
                    <div class="neu-surface shadow-neu-out">
                        <Target class="h-5 w-5 text-[#7c4dff]" />
                    </div>
                </header>
                <p class="text-sm text-gray-500">
                    Curated from portal engagement, social listening, and
                    mortgage pre-approval feeds.
                </p>

                <div class="space-y-4">
                    <div class="neu-surface p-4 shadow-neu-out">
                        <p class="font-semibold text-[#1f2937]">
                            Tech Relocator
                        </p>
                        <p class="text-sm text-gray-500">
                            Hybrid remote, budget $950K, wants turnkey + office
                            space.
                        </p>
                    </div>

                    <div class="neu-surface p-4 shadow-neu-out">
                        <p class="font-semibold text-[#1f2937]">Investor Duo</p>
                        <p class="text-sm text-gray-500">
                            Seeks furnished rental, focuses on East Austin
                            arrivals.
                        </p>
                    </div>
                </div>

                <div class="neu-surface p-4 shadow-neu-out">
                    <div class="flex items-center gap-2">
                        <Compass class="h-4 w-4" />
                        Sync with PixrCollab to notify buyer agents instantly.
                    </div>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 self-start rounded-[18px] bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-[8px_8px_18px_rgba(108,72,219,0.45),-6px_-6px_18px_rgba(212,199,255,0.4)] transition hover:shadow-[inset_6px_6px_16px_rgba(86,55,176,0.6),inset_-6px_-6px_16px_rgba(158,132,255,0.6)] focus:ring-2 focus:ring-[#7c4dff]/60 focus:outline-none"
                >
                    <TrendingUp class="h-4 w-4" />
                    Export market brief
                </button>
            </aside>
        </section>
    </div>
</template>
