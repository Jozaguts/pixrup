<script setup lang="ts">
import { CalendarClock, ClipboardList } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { PropertyWorkspaceProperty, SpyHunt, WorkspaceModuleMeta } from './types';
import PropertyOverviewLogs from '@/components/properties/workspace/PropertyOverviewLogs.vue';
import propertiesRoutes from '@/routes/properties';

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
const overview = ref<any>();
async function loadOverView() {
    loading.value = true;
    const res = await fetch(propertiesRoutes.overview.get(props.property.id as number).url, {
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
    });
    const json = await res.json();
    overview.value = json.data as SpyHunt;

    loading.value = false;
}
loadOverView();
</script>

<template>
    <div class="flex flex-col gap-6 text-[#1f2937]">
        <header class="flex flex-col gap-4 rounded-[28px] p-6">
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
        </header>

        <section class="grid gap-6 h-100">
            <article class="npo-form-shadow flex flex-col gap-5 rounded-[12px] p-6 text-accent">
                <header class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold">Key Investment Signals</h3>
                        <p class="text-sm text-gray-500">
                            Blend of valuation, renovation and market sentiment across modules.
                        </p>
                    </div>
                    <ClipboardList class="h-5 w-5 text-[#7c4dff]" />
                </header>


            </article>
        </section>
    </div>
</template>
