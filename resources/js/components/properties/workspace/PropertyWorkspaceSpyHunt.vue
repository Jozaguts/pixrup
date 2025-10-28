<script setup lang="ts">
import { computed } from 'vue';
import { Compass, Radar, Target, TrendingUp } from 'lucide-vue-next';
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
        <header class="flex flex-col gap-4 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(205,207,222,0.45),-10px_-10px_28px_rgba(255,255,255,0.94)]">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">PixrSpyHunt Intel</h2>
                    <p class="text-sm text-gray-500">
                        MLS bridge, Zillow, and proprietary buyer signals — refreshed every hour.
                    </p>
                </div>
                <span
                    class="hidden items-center gap-2 rounded-full bg-[#f7f4ff] px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#7c4dff] shadow-[4px_4px_12px_rgba(186,162,255,0.3),-4px_-4px_12px_rgba(255,255,255,0.95)] sm:inline-flex"
                >
                    API
                    <span class="font-medium">{{ endpointBadge }}</span>
                </span>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <article class="space-y-6 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(205,207,222,0.45),-10px_-10px_28px_rgba(255,255,255,0.94)]">
                <div class="rounded-[24px] bg-gradient-to-br from-[#fefefe] via-[#f4f5fa] to-[#dde2f7] p-6 shadow-[inset_16px_16px_36px_rgba(199,202,221,0.6),inset_-16px_-16px_36px_rgba(255,255,255,0.9)]">
                    <header class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold">Heatmap radius</h3>
                            <p class="text-sm text-gray-500">Hover to preview competitor listings and buyer interest.</p>
                        </div>
                        <Radar class="h-5 w-5 text-[#7c4dff]" />
                    </header>

                    <div
                        class="mt-4 h-64 rounded-[20px] bg-gradient-to-br from-[#7c4dff]/15 via-[#f4f5fa] to-transparent shadow-[inset_12px_12px_30px_rgba(195,197,216,0.55),inset_-12px_-12px_30px_rgba(255,255,255,0.9)]"
                    />

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-[18px] bg-white px-4 py-3 text-sm font-semibold text-[#1f2937] shadow-[6px_6px_16px_rgba(200,200,216,0.45),-6px_-6px_16px_rgba(255,255,255,0.95)]">
                            0.5 mi radius — 6 actives
                        </div>
                        <div class="rounded-[18px] bg-white px-4 py-3 text-sm font-semibold text-[#1f2937] shadow-[6px_6px_16px_rgba(200,200,216,0.45),-6px_-6px_16px_rgba(255,255,255,0.95)]">
                            Buyer demand score: 8.3 / 10
                        </div>
                        <div class="rounded-[18px] bg-white px-4 py-3 text-sm font-semibold text-[#1f2937] shadow-[6px_6px_16px_rgba(200,200,216,0.45),-6px_-6px_16px_rgba(255,255,255,0.95)]">
                            Avg DOM: 13 days
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div
                        v-for="signal in marketSignals"
                        :key="signal.id"
                        class="rounded-[22px] bg-[#f4f5fa] p-5 text-sm text-gray-600 shadow-[inset_10px_10px_24px_rgba(199,202,220,0.6),inset_-10px_-10px_24px_rgba(255,255,255,0.92)]"
                    >
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">{{ signal.label }}</p>
                        <p class="mt-2 text-xl font-semibold text-[#1f2937]">{{ signal.value }}</p>
                        <p class="mt-1 text-xs text-[#0f766e]">{{ signal.delta }}</p>
                    </div>
                </div>
            </article>

            <aside class="flex flex-col gap-5 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(205,207,222,0.45),-10px_-10px_28px_rgba(255,255,255,0.94)]">
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Buyer Persona Radar</h3>
                    <Target class="h-5 w-5 text-[#7c4dff]" />
                </header>
                <p class="text-sm text-gray-500">
                    Curated from portal engagement, social listening, and mortgage pre-approval feeds.
                </p>

                <div class="space-y-4">
                    <div class="rounded-[20px] bg-[#f4f5fa] p-4 shadow-[inset_8px_8px_18px_rgba(199,202,220,0.6),inset_-8px_-8px_18px_rgba(255,255,255,0.92)]">
                        <p class="font-semibold text-[#1f2937]">Tech Relocator</p>
                        <p class="text-sm text-gray-500">Hybrid remote, budget $950K, wants turnkey + office space.</p>
                    </div>

                    <div class="rounded-[20px] bg-[#f4f5fa] p-4 shadow-[inset_8px_8px_18px_rgba(199,202,220,0.6),inset_-8px_-8px_18px_rgba(255,255,255,0.92)]">
                        <p class="font-semibold text-[#1f2937]">Investor Duo</p>
                        <p class="text-sm text-gray-500">Seeks furnished rental, focuses on East Austin arrivals.</p>
                    </div>
                </div>

                <div class="rounded-[20px] bg-[#f7f4ff] p-4 text-sm text-[#7c4dff] shadow-[6px_6px_16px_rgba(186,162,255,0.4),-6px_-6px_16px_rgba(255,255,255,0.95)]">
                    <div class="flex items-center gap-2">
                        <Compass class="h-4 w-4" />
                        Sync with PixrCollab to notify buyer agents instantly.
                    </div>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 self-start rounded-[18px] bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-[8px_8px_18px_rgba(108,72,219,0.45),-6px_-6px_18px_rgba(212,199,255,0.4)] transition hover:shadow-[inset_6px_6px_16px_rgba(86,55,176,0.6),inset_-6px_-6px_16px_rgba(158,132,255,0.6)] focus:outline-none focus:ring-2 focus:ring-[#7c4dff]/60"
                >
                    <TrendingUp class="h-4 w-4" />
                    Export market brief
                </button>
            </aside>
        </section>
    </div>
</template>
