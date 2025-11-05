<script setup lang="ts">
import { computed } from 'vue';
import {
    Activity,
    CalendarClock,
    ClipboardList,
    Home,
    Ruler,
    VenetianMask,
} from 'lucide-vue-next';
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

const cards = computed(() => {
    const summary = props.property.summary ?? {};
    const pricing = props.property.pricing ?? {};

    return [
        {
            id: 'propertyType',
            label: 'Property Type',
            value: summary.propertyType ?? 'Not specified',
            icon: Home,
        },
        {
            id: 'bedBath',
            label: 'Beds / Baths',
            value:
                summary.bedrooms || summary.bathrooms
                    ? `${summary.bedrooms ?? '—'} bd • ${summary.bathrooms ?? '—'} ba`
                    : '—',
            icon: VenetianMask,
        },
        {
            id: 'livingArea',
            label: 'Living Area',
            value: summary.livingArea
                ? `${Intl.NumberFormat('en-US').format(summary.livingArea)} sq ft`
                : '—',
            icon: Ruler,
        },
        {
            id: 'valuation',
            label: 'Current Estimate',
            value: pricing.currentEstimate
                ? `$${Intl.NumberFormat('en-US', {
                      maximumFractionDigits: 0,
                  }).format(pricing.currentEstimate)}`
                : '—',
            icon: Activity,
        },
    ];
});

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

const eventItems = computed(() => [
    {
        id: 'appraisal',
        title: 'Appraisal refreshed',
        timestamp: '2 days ago',
        body: 'PixrWorth pulled latest comps from HouseCanary and recalibrated the AVM.',
    },
    {
        id: 'glow',
        title: 'Glow-Up render ready',
        timestamp: '12 hours ago',
        body: 'AI renovation preview approved and staged for investor deck.',
    },
    {
        id: 'collab',
        title: 'Collab thread active',
        timestamp: '5 minutes ago',
        body: 'Sofia dropped notes on staging priorities for the walkthrough.',
    },
]);
</script>

<template>
    <div class="flex flex-col gap-6 text-[#1f2937]">
        <header class="flex flex-col gap-4 rounded-[28px]  p-6 ">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight">Workspace Overview</h2>
                    <p class="text-sm text-gray-500">
                        Centralize property intelligence before diving into specific tools.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <CalendarClock class="h-4 w-4" />
                    <span>{{ lastRunCopy }}</span>
                </div>
            </div>

            <dl class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="card in cards"
                    :key="card.id"
                    class="flex items-center gap-3  px-4 py-5 "
                >
                    <component :is="card.icon" class="h-5 w-5 text-[#7c4dff]" />
                    <div>
                        <dt class="text-xs uppercase tracking-[0.3em] text-gray-400">{{ card.label }}</dt>
                        <dd class="text-sm font-semibold text-[#1f2937]">{{ card.value }}</dd>
                    </div>
                </div>
            </dl>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.35fr_1fr]">
            <article class="flex flex-col gap-5 rounded-[28px]  p-6 neu-surface neu-shadow-center">
                <header class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold">Key Investment Signals</h3>
                        <p class="text-sm text-gray-500">
                            Blend of valuation, renovation and market sentiment across modules.
                        </p>
                    </div>
                    <ClipboardList class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <ul class="space-y-4 text-sm text-gray-600">
                    <li class="rounded-[18px]  p-4  neu-surface">
                        PixrWorth signals potential 4.9% appreciation in the next 90 days compared to the ZIP median.
                    </li>
                    <li class="rounded-[18px]  p-4  neu-surface">
                        Glow-Up scenario #2 increases ARV by $68K with minimal structural changes and quick cosmetic upgrades.
                    </li>
                    <li class="rounded-[18px] p-4 neu-surface ">
                        SpyHunt flagged two competing listings going under contract within 7 days — move fast on staging.
                    </li>
                </ul>
            </article>

            <aside class="flex flex-col gap-4 rounded-[28px] neu-surface shadow-neu-out  p-6">
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Recent Activity</h3>
                    <Activity class="h-5 w-5 text-[#7c4dff]" />
                </header>
                <ol class="space-y-4 text-sm text-gray-600">
                    <li
                        v-for="event in eventItems"
                        :key="event.id"
                        class=" p-4 neu-surface shadow-neu-in"
                    >
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">{{ event.timestamp }}</p>
                        <p class="mt-1 font-semibold text-[#1f2937]">{{ event.title }}</p>
                        <p class="mt-1 text-sm text-gray-600">{{ event.body }}</p>
                    </li>
                </ol>
            </aside>
        </section>
    </div>
</template>
