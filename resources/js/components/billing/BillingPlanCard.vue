<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import type { BillingPlanOption } from '@/types';

const props = defineProps<{
    plan: BillingPlanOption;
    priceLabel: string;
    intervalLabel: string;
    actionLabel: string;
    canSubmit: boolean;
    isProcessing: boolean;
    isBusy: boolean;
}>();

const emit = defineEmits<{
    (event: 'action', plan: BillingPlanOption): void;
}>();

const isDisabled = computed(
    () => props.plan.is_current || !props.canSubmit || props.isBusy,
);
const buttonClassName = computed(() =>
    cn(
        'flex items-center justify-center rounded-[12px] px-4 py-2 text-xs font-semibold text-primary shadow-neu-out hover:opacity-90 focus-visible:ring-2 focus-visible:ring-primary/40 disabled:cursor-not-allowed disabled:opacity-50',
        props.isProcessing && 'animate-pulse',
    ),
);

const handleAction = () => {
    emit('action', props.plan);
};
</script>

<template>
    <div
        data-slot="billing-plan-card"
        class="flex flex-col gap-3 rounded-[12px] bg-background p-4 shadow-neu-in"
    >
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h3 class="text-sm font-semibold text-accent">{{ plan.name }}</h3>
                <p v-if="plan.description" class="text-xs text-accent/50">
                    {{ plan.description }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-primary">
                    {{ priceLabel }}
                    <span class="text-xs text-accent/50">{{ intervalLabel }}</span>
                </p>
            </div>
        </div>
        <button
            type="button"
            data-slot="billing-plan-action"
            :disabled="isDisabled"
            :class="buttonClassName"
            @click="handleAction"
        >
            {{ actionLabel }}
        </button>
    </div>
</template>
