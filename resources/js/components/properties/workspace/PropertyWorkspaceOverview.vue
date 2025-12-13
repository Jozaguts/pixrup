<script setup lang="ts">
import {
    CalendarClock,
    ClipboardList,
} from 'lucide-vue-next';
import { computed } from 'vue';
import type { PropertyWorkspaceProperty, WorkspaceModuleMeta } from './types';
import PropertyOverviewLogs from "@/components/properties/workspace/PropertyOverviewLogs.vue";

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
</script>

<template>
    <div class="flex flex-col gap-6 text-[#1f2937]">
        <header class="flex flex-col gap-4 rounded-[28px] p-6">
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-lg font-semibold tracking-tight">
                        Workspace Overview
                    </h2>
                    <p class="text-sm text-gray-500">
                        Centralize property intelligence before diving into
                        specific tools.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <CalendarClock class="h-4 w-4" />
                    <span>{{ lastRunCopy }}</span>
                </div>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.35fr_1fr]">
            <article
                class="flex flex-col gap-5 rounded-[12px] p-6 neo-shadow"
            >
                <header class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold">
                            Key Investment Signals
                        </h3>
                        <p class="text-sm text-gray-500">
                            Blend of valuation, renovation and market sentiment
                            across modules.
                        </p>
                    </div>
                    <ClipboardList class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <ul class="space-y-4 text-sm text-gray-600 shadow-neu-in p-4 px-6 rounded-[12px] bg-gray-200">
                    <li class="shadow-md rounded-[12px] bg-background p-4">
                        PixrWorth signals potential 4.9% appreciation in the
                        next 90 days compared to the ZIP median.
                    </li>
                    <li class="shadow-md rounded-[12px] bg-background p-4">
                        Glow-Up scenario #2 increases ARV by $68K with minimal
                        structural changes and quick cosmetic upgrades.
                    </li>
                    <li class="shadow-md rounded-[12px] bg-background p-4">
                        SpyHunt flagged two competing listings going under
                        contract within 7 days — move fast on staging.
                    </li>
                </ul>
            </article>

            <aside class="p-3">
                <PropertyOverviewLogs />
            </aside>
        </section>
    </div>
</template>
<style scoped>
.neo-shadow{
    box-shadow: -5px -5px 15px rgba(255, 255, 255, 0.8),
    5px 5px 15px rgba(0, 0, 0, 0.1);
}
</style>
