<script setup lang="ts">
import billingRoutes from '@/routes/billing';
import { useForm } from '@inertiajs/vue3';

interface BillingPaymentMethod {
    id: string;
    brand?: string | null;
    last4?: string | null;
    exp_month?: number | null;
    exp_year?: number | null;
}

const props = defineProps<{
    method: BillingPaymentMethod;
    isDefault: boolean;
}>();

const defaultForm = useForm({
    payment_method: '',
});
const removeForm = useForm({
    payment_method: '',
});

const handleMakeDefault = () => {
    if (props.isDefault || defaultForm.processing) {
        return;
    }

    defaultForm.payment_method = props.method.id;
    defaultForm.post('/billing/payment-method/default', {
        preserveScroll: true,
        onFinish: () => {
            defaultForm.reset('payment_method');
        },
    });
};

const handleRemove = () => {
    if (removeForm.processing) {
        return;
    }

    removeForm.payment_method = props.method.id;
    removeForm.delete(billingRoutes.paymentMethod.destroy().url, {
        preserveScroll: true,
        onFinish: () => {
            removeForm.reset('payment_method');
        },
    });
};
</script>

<template>
    <div class="flex flex-col gap-3 rounded-[12px] bg-background p-4 shadow-neu-in sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-10 w-16 items-center justify-center rounded-[10px] bg-white text-sm font-semibold text-[#1a1f36] shadow-neu-in">
                {{ method.brand ? method.brand.toUpperCase() : 'CARD' }}
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-accent">
                    {{ method.brand ?? 'Card' }} ending in {{ method.last4 ?? '----' }}
                </span>
                <span class="text-xs text-accent/50">
                    {{ method.exp_month && method.exp_year ? `Exp ${method.exp_month}/${method.exp_year}` : 'Card on file' }}
                </span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span
                v-if="isDefault"
                class="rounded-full bg-surface px-3 py-1 text-[10px] font-semibold tracking-[0.3em] text-accent/70 uppercase shadow-neu-in"
            >
                Default
            </span>
            <button
                v-else
                type="button"
                :disabled="defaultForm.processing"
                class="neu-button active inline-flex items-center justify-center rounded-[10px] !bg-transparent px-3 py-2 text-[10px] font-semibold tracking-[0.3em] text-accent uppercase shadow-neu-in disabled:cursor-not-allowed disabled:text-accent/50"
                @click="handleMakeDefault"
            >
                {{ defaultForm.processing ? 'Saving...' : 'Make default' }}
            </button>
            <button
                type="button"
                :disabled="removeForm.processing"
                class="neu-button active inline-flex items-center justify-center rounded-[10px] !bg-transparent px-3 py-2 text-[10px] font-semibold tracking-[0.3em] text-accent uppercase shadow-neu-in disabled:cursor-not-allowed disabled:text-accent/50"
                @click="handleRemove"
            >
                {{ removeForm.processing ? 'Removing...' : 'Remove' }}
            </button>
        </div>
    </div>
</template>
