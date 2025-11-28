<script setup lang="ts">
import { Loader2, CheckCircle2, Sparkles,RefreshCw } from 'lucide-vue-next';
import { computed } from 'vue';
import StatusBadge from '@/components/properties/workspace/spyhunt/components/StatusBadge.vue';
import { SpyHuntHeaderProps } from '@/components/properties/workspace/spyhunt/types';

const {reportAdded,title, subtitle, status, isRefreshing, isAddToReportBusy} = defineProps<SpyHuntHeaderProps>();
const emit = defineEmits<{
    (e: 'refresh'): void
    (e: 'add-to-report'): void
}>();
const sparklesText = computed(() => {
    return reportAdded ? 'Added to report' : 'Add to report';
});
</script>

<template>
    <header class="rounded-[12px] p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-2xl font-semibold tracking-tight">{{title}}</p>
                <p class="text-sm text-gray-500">{{subtitle}}</p>
                <div class="mt-8 flex flex-wrap items-center gap-3 text-xs">
                    <StatusBadge :status="status ?? 'ready'" />
                </div>
            </div>
            <!-- actions -->
            <div class="flex flex-col gap-4 npo-form-shadow rounded-[12px] p-5">
                <button
                    type="button"
                    class="neu-button flex items-center justify-center gap-2 rounded-[12px] px-4 text-sm !text-muted-foreground hover:!text-slate-600 cursor-pointer !bg-transparent py-4 font-medium"
                    :disabled="isAddToReportBusy"
                    @click="emit('add-to-report')"
                >
                    {{sparklesText}}
                    <Loader2 v-if="isAddToReportBusy" class="size-4 animate-spin"></Loader2>
                    <CheckCircle2 v-else-if="reportAdded" class="size-4"></CheckCircle2>
                    <Sparkles v-else class="size-4 text-primary" />
                </button>
                <button
                    type="button"
                    class="neu-button flex items-center justify-center gap-2 rounded-[12px] px-4 text-sm !text-muted-foreground hover:!text-slate-600 cursor-pointer !bg-transparent py-4 font-medium"
                    :disabled="isRefreshing"
                    @click="emit('refresh')"
                >
                    Refresh Data
                    <Loader2 v-if="isRefreshing" class="size-4 animate-spin text-primary" />
                    <RefreshCw v-else class="size-4 text-primary" />
                </button>
            </div>

        </div>
    </header>
</template>
