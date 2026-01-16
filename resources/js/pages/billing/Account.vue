<script setup lang="ts">
import type { BillingPlan, BillingPlanOption, BreadcrumbItem } from '@/types';
import billingRoutes from '@/routes/billing';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { CreditCard, FolderOpen, Sparkles, X } from 'lucide-vue-next';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import PaymentMethodCard from '@/components/billing/PaymentMethodCard.vue';
import BillingPlanCard from '@/components/billing/BillingPlanCard.vue';
import ToastAlert from '@/components/shared/ToastAlert.vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface BillingOrder {
    id: string;
    date: string;
    type: string;
    receipt_url?: string | null;
}

interface BillingOrderHistory {
    data: BillingOrder[];
    next_cursor?: string | null;
    has_more?: boolean;
}

interface BillingPaymentMethod {
    id: string;
    brand?: string | null;
    last4?: string | null;
    exp_month?: number | null;
    exp_year?: number | null;
}

interface SetupIntentPayload {
    client_secret: string;
}

interface Props {
    stripeKey?: string | null;
    paymentMethods?: BillingPaymentMethod[];
    defaultPaymentMethodId?: string | null;
    setupIntent?: SetupIntentPayload | null;
    orderHistory?: BillingOrderHistory | null;
    activePlan?: BillingPlan | null;
    plans?: BillingPlanOption[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billing',
        href: billingRoutes.account().url,
    },
];

const orderHistory = ref<BillingOrder[]>([]);
const orderHistoryCursors = ref<Record<number, string | null>>({});
const orderHistoryServerItemsLength = ref(0);
const activePlan = computed(() => props.activePlan ?? null);
const plans = computed(() => props.plans ?? []);
const paymentMethods = computed(() => props.paymentMethods ?? []);
const defaultPaymentMethodId = computed(() => props.defaultPaymentMethodId ?? null);

const hasOrderHistory = computed(() => orderHistory.value.length > 0);
const hasActivePlan = computed(() => Boolean(activePlan.value));
const hasPlans = computed(() => plans.value.length > 0);
const hasPaymentMethod = computed(() => paymentMethods.value.length > 0);

const cardHolderName = ref('');
const stripeError = ref<string | null>(null);
const stripeReady = ref(false);
const cardElementRef = ref<HTMLDivElement | null>(null);

const paymentForm = useForm({
    payment_method: '',
});
const paymentMethodError = computed(() => paymentForm.errors.payment_method ?? null);

const orderHistoryHeaders = [
    {
        text: 'Date',
        value: 'date',
    },
    {
        text: 'Plan',
        value: 'type',
    },
    {
        text: 'Receipt',
        value: 'receipt',
        align: 'right',
        width: 120,
    },
];
const orderHistoryTableHeight = 396;
const orderHistoryServerOptions = ref({
    page: 1,
    rowsPerPage: 5,
});

const planForm = useForm({
    price_id: '',
});
const planError = computed(() => planForm.errors.price_id ?? null);
const cancelForm = useForm({});
const isLoadingOrderHistory = ref(false);
const orderHistoryError = ref<string | null>(null);
const pendingPlanPriceId = ref<string | null>(null);
const selectedPlan = ref<BillingPlanOption | null>(null);
const isPlanConfirmOpen = ref(false);
const showPlanSuccessToast = ref(false);
const planSuccessMessage = ref<string | null>(null);

const canCollectPayment = computed(() => Boolean(props.stripeKey) && Boolean(props.setupIntent?.client_secret));
const showPaymentForm = ref(false);
const shouldMountStripe = computed(() => showPaymentForm.value && canCollectPayment.value);

let stripeInstance: any = null;
let stripeElements: any = null;
let stripeCardElement: any = null;
let stripeScriptPromise: Promise<void> | null = null;

const loadStripeScript = async (): Promise<void> => {
    if (typeof window === 'undefined') {
        return;
    }

    if ((window as any).Stripe) {
        return;
    }

    if (!stripeScriptPromise) {
        stripeScriptPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://js.stripe.com/v3/';
            script.async = true;
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Stripe failed to load.'));
            document.head.appendChild(script);
        });
    }

    await stripeScriptPromise;
};

const mountStripeCard = async () => {
    if (!shouldMountStripe.value || stripeReady.value) {
        return;
    }

    stripeError.value = null;

    try {
        await loadStripeScript();
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
    } catch (error) {
        stripeError.value = 'Stripe failed to load. Please try again later.';
        return;
    }

    if (!(window as any).Stripe || !props.stripeKey) {
        stripeError.value = 'Stripe is not available right now.';
        return;
    }

    await nextTick();
    if (!cardElementRef.value) {
        return;
    }

    stripeInstance = (window as any).Stripe(props.stripeKey);
    stripeElements = stripeInstance.elements();
    stripeCardElement = stripeElements.create('card', {
        hidePostalCode: true,
    });
    stripeCardElement.mount(cardElementRef.value);
    stripeReady.value = true;
};

const destroyStripeCard = () => {
    if (stripeCardElement) {
        stripeCardElement.unmount();
        if (typeof stripeCardElement.destroy === 'function') {
            stripeCardElement.destroy();
        }
    }

    stripeCardElement = null;
    stripeElements = null;
    stripeInstance = null;
    stripeReady.value = false;
};

const handleAddPaymentMethod = async () => {
    if (!shouldMountStripe.value || !stripeInstance || !stripeCardElement) {
        return;
    }

    if (paymentForm.processing) {
        return;
    }

    stripeError.value = null;

    const clientSecret = props.setupIntent?.client_secret;
    if (!clientSecret) {
        stripeError.value = 'Payment setup is not ready yet.';
        return;
    }

    const result = await stripeInstance.confirmCardSetup(clientSecret, {
        payment_method: {
            card: stripeCardElement,
            billing_details: {
                name: cardHolderName.value || undefined,
            },
        },
    });

    if (result.error) {
        stripeError.value = result.error.message ?? 'Unable to verify card.';
        return;
    }

    const paymentMethodId = result.setupIntent?.payment_method;
    if (!paymentMethodId) {
        stripeError.value = 'Unable to verify card.';
        return;
    }

    paymentForm.payment_method = paymentMethodId;
    paymentForm.post(billingRoutes.paymentMethod.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            showPaymentForm.value = false;
        },
        onFinish: () => {
            paymentForm.reset('payment_method');
        },
    });
};

onMounted(() => {
    if (shouldMountStripe.value) {
        mountStripeCard();
    }
});

watch(
    hasPaymentMethod,
    (value) => {
        showPaymentForm.value = !value;
    },
    { immediate: true },
);

watch(shouldMountStripe, (value) => {
    if (value) {
        mountStripeCard();
        return;
    }

    destroyStripeCard();
});

onBeforeUnmount(() => {
    destroyStripeCard();
});

const handleShowPaymentForm = () => {
    showPaymentForm.value = true;
};

const isPlanDrawerOpen = ref(false);

const openPlansDrawer = () => {
    isPlanDrawerOpen.value = true;
};

const closePlansDrawer = () => {
    isPlanDrawerOpen.value = false;
    planForm.reset('price_id');
    planForm.clearErrors();
    pendingPlanPriceId.value = null;
};

const canSubmitPlan = computed(() => hasPaymentMethod.value);

const formatPlanPrice = (plan: BillingPlanOption) => {
    const currency = plan.price.currency?.toUpperCase() ?? 'USD';
    try {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency,
        }).format(plan.price.unit_amount / 100);
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
    } catch (error) {
        return `$${(plan.price.unit_amount / 100).toFixed(0)}`;
    }
};

const formatPlanInterval = (plan: BillingPlanOption) => {
    if (!plan.price.interval) {
        return '';
    }

    const count = plan.price.interval_count ?? 1;
    const label = plan.price.interval;
    const suffix = count > 1 ? `${count} ${label}s` : label;

    return `/${suffix}`;
};

const formatPlanRenewDate = (value?: string | null) => {
    if (!value) {
        return 'soon';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return 'soon';
    }

    return date.toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const formatPlanChangeDate = (value?: string | null) => {
    if (!value) {
        return 'at period end';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return 'at period end';
    }

    const formatted = date.toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });

    return `on ${formatted}`;
};

const planActionLabel = (plan: BillingPlanOption) => {
    if (plan.is_current) {
        return 'Current plan';
    }

    return hasActivePlan.value ? 'Change plan' : 'Subscribe';
};

const planConfirmTitle = computed(() => {
    if (!selectedPlan.value) {
        return 'Confirm subscription';
    }

    return hasActivePlan.value ? 'Confirm plan change' : 'Confirm subscription';
});

const planConfirmActionLabel = computed(() => {
    if (!selectedPlan.value) {
        return 'Confirm';
    }

    return hasActivePlan.value ? 'Confirm change' : 'Confirm subscription';
});

const planConfirmDescription = computed(() => {
    if (!selectedPlan.value) {
        return 'Confirm to continue.';
    }

    const action = hasActivePlan.value ? 'change to' : 'subscribe to';

    return `You're about to ${action} ${selectedPlan.value.name}.`;
});

const isPlanProcessing = (plan: BillingPlanOption) =>
    planForm.processing && pendingPlanPriceId.value === plan.price.id;

const planActionStateLabel = (plan: BillingPlanOption) => {
    if (isPlanProcessing(plan)) {
        return hasActivePlan.value ? 'Updating...' : 'Subscribing...';
    }

    return planActionLabel(plan);
};

const openPlanConfirmation = (plan: BillingPlanOption) => {
    if (plan.is_current || !canSubmitPlan.value || planForm.processing) {
        return;
    }

    selectedPlan.value = plan;
    isPlanConfirmOpen.value = true;
};

const closePlanConfirmation = () => {
    isPlanConfirmOpen.value = false;
    selectedPlan.value = null;
};

const handlePlanConfirmOpen = (value: boolean) => {
    isPlanConfirmOpen.value = value;
    if (!value) {
        selectedPlan.value = null;
    }
};

const triggerPlanSuccessToast = (message: string) => {
    planSuccessMessage.value = message;
    showPlanSuccessToast.value = false;

    nextTick(() => {
        showPlanSuccessToast.value = true;
        window.setTimeout(() => {
            showPlanSuccessToast.value = false;
        }, 4200);
    });
};

const submitPlanChange = (plan: BillingPlanOption) => {
    pendingPlanPriceId.value = plan.price.id;
    planForm.price_id = plan.price.id;

    const route = hasActivePlan.value ? billingRoutes.subscription.swap().url : billingRoutes.subscription.store().url;

    planForm.post(route, {
        preserveScroll: true,
        onSuccess: () => {
            closePlansDrawer();
            const message = hasActivePlan.value
                ? `Plan updated to ${plan.name}.`
                : `Subscribed to ${plan.name}.`;
            triggerPlanSuccessToast(message);
        },
        onFinish: () => {
            planForm.reset('price_id');
            pendingPlanPriceId.value = null;
            selectedPlan.value = null;
        },
    });
};

const confirmPlanChange = () => {
    if (!selectedPlan.value || planForm.processing) {
        return;
    }

    isPlanConfirmOpen.value = false;
    submitPlanChange(selectedPlan.value);
};

const handleCancelSubscription = () => {
    if (!hasActivePlan.value || cancelForm.processing) {
        return;
    }

    cancelForm.post(billingRoutes.subscription.cancel().url, {
        preserveScroll: true,
    });
};

const resolveOrderHistoryTotal = (page: number, count: number, hasMore: boolean, rowsPerPage: number) => {
    const total = (page - 1) * rowsPerPage + count;

    return hasMore ? total + rowsPerPage : total;
};

const loadOrderHistoryFromServer = async () => {
    if (!window.axios) {
        orderHistoryError.value = 'Unable to load receipts.';
        return;
    }

    if (isLoadingOrderHistory.value) {
        return;
    }

    const page = orderHistoryServerOptions.value.page ?? 1;
    const rowsPerPage = orderHistoryServerOptions.value.rowsPerPage ?? 5;
    const cursor = page > 1 ? orderHistoryCursors.value[page] : null;

    if (page > 1 && !cursor) {
        orderHistoryServerOptions.value.page = Math.max(1, page - 1);
        return;
    }

    isLoadingOrderHistory.value = true;
    orderHistoryError.value = null;

    try {
        const response = await window.axios.get(
            billingRoutes.orderHistory({
                query: {
                    cursor: cursor ?? undefined,
                    per_page: rowsPerPage,
                },
            }).url,
        );
        const payload = response.data as BillingOrderHistory;
        const hasMore = payload.has_more ?? Boolean(payload.next_cursor);

        orderHistory.value = payload.data ?? [];
        orderHistoryCursors.value = {
            ...orderHistoryCursors.value,
            [page + 1]: payload.next_cursor ?? null,
        };
        orderHistoryServerItemsLength.value = resolveOrderHistoryTotal(
            page,
            orderHistory.value.length,
            hasMore,
            rowsPerPage,
        );
    } catch (error) {
        orderHistoryError.value = 'Unable to load receipts.';
    } finally {
        isLoadingOrderHistory.value = false;
    }
};

watch(orderHistoryServerOptions, () => {
    loadOrderHistoryFromServer();
}, { deep: true });

watch(
    () => props.orderHistory,
    (value) => {
        orderHistory.value = value?.data ?? [];
        orderHistoryCursors.value = {
            1: null,
            2: value?.next_cursor ?? null,
        };
        orderHistoryServerItemsLength.value = resolveOrderHistoryTotal(
            1,
            orderHistory.value.length,
            value?.has_more ?? Boolean(value?.next_cursor),
            orderHistoryServerOptions.value.rowsPerPage,
        );
    },
    { immediate: true },
);
</script>

<template>
    <Head title="Billing" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="mx-auto flex w-full max-w-6xl flex-col gap-10 px-6 pt-4 pb-12 text-accent">
            <header class="flex flex-col gap-2">
                <p class="text-xs font-semibold tracking-[0.4em] text-accent/50 uppercase">Account</p>
                <h1 class="text-2xl font-semibold tracking-tight">Billing</h1>
                <p class="text-sm text-accent/50">Manage payments, receipts, and the plan tied to this workspace.</p>
            </header>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <article class="npo-form-shadow flex flex-col gap-5 rounded-[16px] bg-surface p-4">
                    <header class="flex flex-col gap-2">
                        <h2 class="text-base font-semibold">Order history</h2>
                        <p class="text-sm text-accent/50">Manage billing information and view receipts.</p>
                    </header>

                    <div
                        v-if="!hasOrderHistory"
                        class="flex flex-col items-center justify-center gap-3 rounded-[14px] bg-background p-4 text-center shadow-neu-in"
                    >
                        <div class="flex size-12 items-center justify-center rounded-full bg-surface shadow-neu-in">
                            <FolderOpen class="h-5 w-5 text-accent/60" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-accent">No receipts yet</p>
                            <p class="text-xs text-accent/50">Orders will appear here once your first charge posts.</p>
                        </div>
                        <button
                            type="button"
                            class="neu-button active rounded-[10px] !bg-transparent px-4 py-2 text-xs font-semibold text-primary shadow-neu-in"
                            @click="openPlansDrawer"
                        >
                            Explore plans
                        </button>
                    </div>

                    <template v-else>
                        <div class="overflow-hidden rounded-[12px] shadow-neu-in">
                            <data-table
                                v-model:server-options="orderHistoryServerOptions"
                                table-class-name="soft-table"
                                :server-items-length="orderHistoryServerItemsLength"
                                :loading="isLoadingOrderHistory"
                                :headers="orderHistoryHeaders"
                                :items="orderHistory"
                                :rows-per-page="orderHistoryServerOptions.rowsPerPage"
                                hide-rows-per-page
                                :table-height="orderHistoryTableHeight"
                                :table-min-height="orderHistoryTableHeight"
                            >
                                <template #header="header">
                                    <span class="text-xs font-semibold tracking-[0.3em] uppercase text-accent/50">
                                        {{ header.text }}
                                    </span>
                                </template>
                                <template #item-receipt="{ receipt_url }">
                                    <div class="flex justify-end">
                                        <a
                                            v-if="receipt_url"
                                            :href="receipt_url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-primary !bg-transparent rounded-[12px] neu-button shadow-neu-out hover:opacity-90 focus-visible:ring-2 focus-visible:ring-primary/40"
                                        >
                                            Download
                                        </a>
                                        <button
                                            v-else
                                            type="button"
                                            disabled
                                            class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-primary !bg-transparent rounded-[12px] neu-button shadow-neu-out disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            Download
                                        </button>
                                    </div>
                                </template>
                            </data-table>
                        </div>
                        <p v-if="orderHistoryError" class="text-xs text-primary">
                            {{ orderHistoryError }}
                        </p>
                    </template>
                </article>

                <aside
                    v-if="hasActivePlan"
                    class="npo-form-shadow flex flex-col gap-4 rounded-[12px] bg-surface p-4 text-accent"
                >
                    <p class="text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase">Your plan</p>
                    <div class="space-y-1">
                        <h3 class="text-lg font-semibold">{{ activePlan?.name }}</h3>
                        <p v-if="activePlan?.is_canceling" class="text-sm text-accent/50">
                            Access until {{ activePlan?.ends_at ?? 'TBD' }}
                        </p>
                        <p v-else class="text-sm text-accent/50">
                            Next charge on {{ formatPlanRenewDate(activePlan?.renews_at) }}
                        </p>
                        <p v-if="activePlan?.pending_plan" class="text-sm text-accent/50">
                            Changes to {{ activePlan.pending_plan.name }}
                            {{ formatPlanChangeDate(activePlan.pending_plan.starts_at) }}
                        </p>
                    </div>
                    <div class="mt-auto flex flex-col gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-[12px] !bg-transparent px-4 py-2 text-xs font-semibold text-primary shadow-neu-out hover:opacity-90 focus-visible:ring-2 focus-visible:ring-primary/40"
                            @click="openPlansDrawer"
                        >
                            View plans
                        </button>
                        <button
                            type="button"
                            :disabled="activePlan?.is_canceling || cancelForm.processing"
                            class="inline-flex items-center justify-center rounded-[12px] bg-surface px-4 py-2 text-xs font-semibold tracking-[0.2em] text-accent uppercase shadow-neu-out hover:opacity-90 focus-visible:ring-2 focus-visible:ring-primary/40 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="handleCancelSubscription"
                        >
                            {{ activePlan?.is_canceling ? 'Cancellation scheduled' : 'Cancel subscription' }}
                        </button>
                    </div>
                </aside>
                <aside
                    v-else
                    class="flex flex-col items-center justify-center gap-4 rounded-[18px] bg-surface p-4 text-center shadow-neu-out"
                >
                    <div class="flex size-12 items-center justify-center rounded-full bg-background shadow-neu-in">
                        <Sparkles class="h-5 w-5 text-accent/60" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-accent">No active plan</p>
                        <p class="text-xs text-accent/50">Pick a subscription to unlock pro modules.</p>
                    </div>
                    <button
                        type="button"
                        class="active rounded-[12px] !bg-transparent px-4 py-2 text-xs font-semibold text-primary shadow-neu-out"
                        @click="openPlansDrawer"
                    >
                        Browse plans
                    </button>
                </aside>
            </div>

            <section class="npo-form-shadow flex flex-col gap-5 rounded-[16px] bg-surface p-4">
                <header class="flex flex-col gap-2">
                    <h2 class="text-base font-semibold">Payment method</h2>
                    <p class="text-sm text-accent/50">Manage billing information and view receipts.</p>
                </header>

                <div v-if="!hasPaymentMethod" class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                    <div
                        class="flex flex-col items-center justify-center gap-3 rounded-[14px] bg-background p-4 text-center shadow-neu-in"
                    >
                        <div class="flex size-12 items-center justify-center rounded-full bg-surface shadow-neu-in">
                            <CreditCard class="h-5 w-5 text-accent/60" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-accent">No payment method</p>
                            <p class="text-xs text-accent/50">Add a card to keep subscriptions active.</p>
                        </div>
                        <p v-if="!canCollectPayment" class="text-xs text-accent/50">
                            Payment setup is unavailable. Please try again later.
                        </p>
                        <button
                            type="button"
                            class="active rounded-[10px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-out"
                            @click="handleShowPaymentForm"
                        >
                            Add new payment method
                        </button>
                    </div>

                    <form
                        v-if="showPaymentForm && canCollectPayment"
                        class="flex flex-col gap-4 rounded-[14px] bg-background p-5 shadow-neu-in"
                        @submit.prevent="handleAddPaymentMethod"
                    >
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase">
                                Cardholder name
                            </label>
                            <input
                                v-model="cardHolderName"
                                type="text"
                                placeholder="Name on card"
                                class="rounded-[12px] bg-surface px-4 py-3 text-sm text-accent shadow-neu-in transition outline-none focus:ring-2 focus:ring-primary/40"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase">
                                Card details
                            </label>
                            <div
                                ref="cardElementRef"
                                class="min-h-[48px] rounded-[12px] bg-surface px-4 py-3 text-sm text-accent shadow-neu-in"
                            />
                        </div>
                        <p v-if="stripeError" class="text-xs text-primary">
                            {{ stripeError }}
                        </p>
                        <p v-else-if="paymentMethodError" class="text-xs text-primary">
                            {{ paymentMethodError }}
                        </p>
                        <button
                            type="submit"
                            :disabled="paymentForm.processing || !stripeReady"
                            class="active rounded-[12px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-out disabled:cursor-not-allowed disabled:text-accent/50"
                        >
                            {{ paymentForm.processing ? 'Saving card...' : 'Save card' }}
                        </button>
                        <p v-if="!stripeReady" class="text-xs text-accent/50">Loading secure card form...</p>
                    </form>
                </div>

                <div v-else class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase">
                            Saved payment methods
                        </p>
                        <button
                            type="button"
                            class="active inline-flex items-center justify-center rounded-[10px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-out"
                            @click="handleShowPaymentForm"
                        >
                            Add new payment method
                        </button>
                    </div>

                    <div class="grid gap-3">
                        <PaymentMethodCard
                            v-for="method in paymentMethods"
                            :key="method.id"
                            :method="method"
                            :is-default="method.id === defaultPaymentMethodId"
                        />
                    </div>
                </div>
                <form
                    v-if="hasPaymentMethod && showPaymentForm && canCollectPayment"
                    class="mt-4 flex flex-col gap-4 rounded-[14px] bg-background p-5 shadow-neu-in"
                    @submit.prevent="handleAddPaymentMethod"
                >
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase">
                            Cardholder name
                        </label>
                        <input
                            v-model="cardHolderName"
                            type="text"
                            placeholder="Name on card"
                            class="rounded-[12px] bg-surface px-4 py-3 text-sm text-accent shadow-neu-in transition outline-none focus:ring-2 focus:ring-primary/40"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase">
                            Card details
                        </label>
                        <div
                            ref="cardElementRef"
                            class="min-h-[48px] rounded-[12px] bg-surface px-4 py-3 text-sm text-accent shadow-neu-in"
                        />
                    </div>
                    <p v-if="stripeError" class="text-xs text-primary">
                        {{ stripeError }}
                    </p>
                    <p v-else-if="paymentMethodError" class="text-xs text-primary">
                        {{ paymentMethodError }}
                    </p>
                    <button
                        type="submit"
                        :disabled="paymentForm.processing || !stripeReady"
                        class="active rounded-[12px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-out disabled:cursor-not-allowed disabled:text-accent/50"
                    >
                        {{ paymentForm.processing ? 'Saving card...' : 'Save card' }}
                    </button>
                    <p v-if="!stripeReady" class="text-xs text-accent/50">Loading secure card form...</p>
                </form>
                <p v-else-if="hasPaymentMethod && showPaymentForm" class="text-xs text-accent/50">
                    Payment setup is unavailable. Please try again later.
                </p>
            </section>
        </section>
        <ToastAlert
            v-if="showPlanSuccessToast"
            :visible="showPlanSuccessToast"
            type="success"
            title="Billing updated"
            :msg="planSuccessMessage ?? ''"
            :timer="4200"
            :show-confirm-button="false"
            :show-close-button="true"
        />
        <teleport to="body">
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="isPlanDrawerOpen" class="fixed inset-0 z-40 bg-black/50" @click="closePlansDrawer"></div>
            </transition>
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="translate-x-full opacity-0"
                enter-to-class="translate-x-0 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="translate-x-0 opacity-100"
                leave-to-class="translate-x-full opacity-0"
            >
                <aside v-if="isPlanDrawerOpen" class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md">
                    <section class="npo-form-shadow flex h-full w-full flex-col gap-4 bg-surface p-4">
                        <header class="flex items-start justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <p class="text-xs font-semibold tracking-widest text-accent/50 uppercase">Plans</p>
                                <h2 class="text-lg font-semibold text-accent">Choose a plan</h2>
                            </div>
                            <button
                                type="button"
                                class="flex items-center justify-center rounded-[12px] px-2 py-2 text-xs font-semibold text-primary shadow-neu-out hover:opacity-90 focus-visible:ring-2 focus-visible:ring-primary/40 disabled:cursor-not-allowed disabled:opacity-50"
                                @click="closePlansDrawer"
                            >
                                <X class="h-4 w-4" />
                            </button>
                        </header>

                        <div class="flex flex-col gap-3 overflow-y-auto">
                            <div
                                v-if="!hasPlans"
                                class="flex flex-col items-center gap-2 rounded-[12px] bg-background p-4 text-center shadow-neu-in"
                            >
                                <p class="text-sm font-semibold text-accent">Plans unavailable</p>
                                <p class="text-xs text-accent/50">Check back later or contact support.</p>
                            </div>

                            <BillingPlanCard
                                v-for="plan in plans"
                                :key="plan.key"
                                :plan="plan"
                                :price-label="formatPlanPrice(plan)"
                                :interval-label="formatPlanInterval(plan)"
                                :action-label="planActionStateLabel(plan)"
                                :can-submit="canSubmitPlan"
                                :is-processing="isPlanProcessing(plan)"
                                @action="openPlanConfirmation"
                            />
                        </div>

                        <p v-if="!hasPaymentMethod" class="text-xs text-accent/50">
                            Add a payment method to subscribe to a plan.
                        </p>
                        <p v-if="planError" class="text-xs text-primary">
                            {{ planError }}
                        </p>
                    </section>
                </aside>
            </transition>
        </teleport>
        <Dialog :open="isPlanConfirmOpen" @update:open="handlePlanConfirmOpen">
            <DialogContent
                class="npo-form-shadow w-[90vw] max-w-md rounded-[16px] border-0 bg-surface p-5 text-accent"
            >
                <DialogHeader class="space-y-2">
                    <DialogTitle class="text-lg font-semibold text-accent">
                        {{ planConfirmTitle }}
                    </DialogTitle>
                    <DialogDescription class="text-sm text-accent/60">
                        {{ planConfirmDescription }}
                    </DialogDescription>
                </DialogHeader>
                <div class="flex flex-col gap-2 rounded-[12px] bg-background p-4 text-xs text-accent/70 shadow-neu-in">
                    <p v-if="selectedPlan" class="text-sm font-semibold text-accent">
                        {{ selectedPlan.name }}
                    </p>
                    <p v-if="selectedPlan" class="text-xs text-accent/60">
                        {{ formatPlanPrice(selectedPlan) }}{{ formatPlanInterval(selectedPlan) }}
                    </p>
                </div>
                <DialogFooter class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <DialogClose as-child>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-[12px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-out hover:opacity-90"
                        >
                            Cancel
                        </button>
                    </DialogClose>
                    <button
                        type="button"
                        :disabled="planForm.processing"
                        class="inline-flex items-center justify-center rounded-[12px] bg-surface px-4 py-2 text-xs font-semibold text-primary shadow-neu-out hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="confirmPlanChange"
                    >
                        {{ planConfirmActionLabel }}
                    </button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
