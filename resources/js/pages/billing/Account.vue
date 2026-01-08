<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import billingRoutes from '@/routes/billing';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { CreditCard, FolderOpen, Sparkles } from 'lucide-vue-next';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import PaymentMethodCard from '@/components/billing/PaymentMethodCard.vue';

interface BillingOrder {
    date: string;
    type: string;
    receipt_url?: string | null;
}

interface BillingPlan {
    name: string;
    renews_at?: string | null;
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
    orderHistory?: BillingOrder[];
    activePlan?: BillingPlan | null;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billing',
        href: billingRoutes.account().url,
    },
];

const orderHistory = computed(() => props.orderHistory ?? []);
const activePlan = computed(() => props.activePlan ?? null);
const paymentMethods = computed(() => props.paymentMethods ?? []);
const defaultPaymentMethodId = computed(() => props.defaultPaymentMethodId ?? null);

const hasOrderHistory = computed(() => orderHistory.value.length > 0);
const hasActivePlan = computed(() => Boolean(activePlan.value));
const hasPaymentMethod = computed(() => paymentMethods.value.length > 0);

const cardHolderName = ref('');
const stripeError = ref<string | null>(null);
const stripeReady = ref(false);
const cardElementRef = ref<HTMLDivElement | null>(null);

const paymentForm = useForm({
    payment_method: '',
});
const paymentMethodError = computed(() => paymentForm.errors.payment_method ?? null);

const canCollectPayment = computed(
    () => Boolean(props.stripeKey) && Boolean(props.setupIntent?.client_secret),
);
const showPaymentForm = ref(false);
const shouldMountStripe = computed(
    () => showPaymentForm.value && canCollectPayment.value,
);

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

watch(hasPaymentMethod, (value) => {
    showPaymentForm.value = !value;
}, { immediate: true });

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
</script>

<template>
    <Head title="Billing" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="mx-auto flex w-full max-w-6xl flex-col gap-10 px-6 pb-12 pt-4 text-accent">
            <header class="flex flex-col gap-2">
                <p class="text-xs font-semibold tracking-[0.4em] text-accent/50 uppercase">Account</p>
                <h1 class="text-2xl font-semibold tracking-tight">Billing</h1>
                <p class="text-sm text-accent/50">Manage payments, receipts, and the plan tied to this workspace.</p>
            </header>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <article class="npo-form-shadow flex flex-col gap-5 rounded-[16px] bg-surface p-6">
                    <header class="flex flex-col gap-2">
                        <h2 class="text-base font-semibold">Order history</h2>
                        <p class="text-sm text-accent/50">Manage billing information and view receipts.</p>
                    </header>

                    <div
                        v-if="!hasOrderHistory"
                        class="flex flex-col items-center justify-center gap-3 rounded-[14px] bg-background p-6 text-center shadow-neu-in"
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
                            class="neu-button active rounded-[10px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-in"
                        >
                            Explore plans
                        </button>
                    </div>

                    <template v-else>
                        <div class="hidden text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase sm:grid sm:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_110px]">
                            <span>Date</span>
                            <span>Type</span>
                            <span class="text-right">Receipt</span>
                        </div>

                        <div class="grid gap-3">
                            <div
                                v-for="entry in orderHistory"
                                :key="`${entry.date}-${entry.type}`"
                                class="grid gap-3 rounded-[12px] bg-background p-4 shadow-neu-in sm:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_110px] sm:items-center"
                            >
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-accent">{{ entry.date }}</span>
                                    <span class="text-xs text-accent/50 sm:hidden">{{ entry.type }}</span>
                                </div>
                                <span class="hidden text-sm text-accent sm:block">{{ entry.type }}</span>
                                <div class="flex sm:justify-end">
                                    <button
                                        type="button"
                                        class="neu-button active inline-flex items-center justify-center rounded-[10px] !bg-transparent px-3 py-2 text-xs font-semibold text-accent shadow-neu-in"
                                    >
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="self-start text-xs font-semibold tracking-[0.3em] text-primary uppercase"
                        >
                            Load more
                        </button>
                    </template>
                </article>

                <aside v-if="hasActivePlan" class="flex flex-col gap-4 rounded-[18px] bg-primary p-6 text-white shadow-neu-out">
                    <p class="text-xs font-semibold tracking-[0.3em] text-white/70 uppercase">Your plan</p>
                    <div class="space-y-1">
                        <h3 class="text-lg font-semibold">{{ activePlan.name }}</h3>
                        <p class="text-sm text-white/80">
                            Renews on {{ activePlan.renews_at ?? 'TBD' }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="mt-auto inline-flex items-center justify-center rounded-[12px] border border-white/70 px-4 py-2 text-xs font-semibold tracking-[0.2em] text-accent uppercase"
                    >
                        Cancel subscription
                    </button>
                </aside>
                <aside v-else class="flex flex-col items-center justify-center gap-4 rounded-[18px] bg-surface p-6 text-center shadow-neu-out">
                    <div class="flex size-12 items-center justify-center rounded-full bg-background shadow-neu-in">
                        <Sparkles class="h-5 w-5 text-accent/60" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-accent">No active plan</p>
                        <p class="text-xs text-accent/50">Pick a subscription to unlock pro modules.</p>
                    </div>
                    <button
                        type="button"
                        class="neu-button active rounded-[12px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-in"
                    >
                        Browse plans
                    </button>
                </aside>
            </div>

            <section class="npo-form-shadow flex flex-col gap-5 rounded-[16px] bg-surface p-6">
                <header class="flex flex-col gap-2">
                    <h2 class="text-base font-semibold">Payment method</h2>
                    <p class="text-sm text-accent/50">Manage billing information and view receipts.</p>
                </header>

                <div v-if="!hasPaymentMethod" class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                    <div
                        class="flex flex-col items-center justify-center gap-3 rounded-[14px] bg-background p-6 text-center shadow-neu-in"
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
                            class="neu-button active rounded-[10px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-in"
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
                                class="rounded-[12px] bg-surface px-4 py-3 text-sm text-accent shadow-neu-in outline-none transition focus:ring-2 focus:ring-primary/40"
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
                            class="neu-button active rounded-[12px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-in disabled:cursor-not-allowed disabled:text-accent/50"
                        >
                            {{ paymentForm.processing ? 'Saving card...' : 'Save card' }}
                        </button>
                        <p v-if="!stripeReady" class="text-xs text-accent/50">
                            Loading secure card form...
                        </p>
                    </form>
                </div>

                <div v-else class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs font-semibold tracking-[0.3em] text-accent/50 uppercase">
                            Saved payment methods
                        </p>
                        <button
                            type="button"
                            class="neu-button active inline-flex items-center justify-center rounded-[10px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-in"
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
                            class="rounded-[12px] bg-surface px-4 py-3 text-sm text-accent shadow-neu-in outline-none transition focus:ring-2 focus:ring-primary/40"
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
                        class="neu-button active rounded-[12px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-in disabled:cursor-not-allowed disabled:text-accent/50"
                    >
                        {{ paymentForm.processing ? 'Saving card...' : 'Save card' }}
                    </button>
                    <p v-if="!stripeReady" class="text-xs text-accent/50">
                        Loading secure card form...
                    </p>
                </form>
                <p v-else-if="hasPaymentMethod && showPaymentForm" class="text-xs text-accent/50">
                    Payment setup is unavailable. Please try again later.
                </p>
            </section>
        </section>
    </AppLayout>
</template>
