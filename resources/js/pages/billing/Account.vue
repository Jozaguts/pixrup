<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import billingRoutes from '@/routes/billing';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { CreditCard, FolderOpen, Sparkles } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billing',
        href: billingRoutes.account().url,
    },
];

const orderHistory = [
    {
        date: 'Oct 21, 2021',
        type: 'Pro Annual',
    },
    {
        date: 'Aug 21, 2021',
        type: 'Pro Portfolio',
    },
    {
        date: 'Jul 21, 2021',
        type: 'Sponsored Post',
    },
    {
        date: 'Jun 21, 2021',
        type: 'Sponsored Post',
    },
];

const activePlan = {
    name: 'Pro Annual',
    renewsAt: 'Nov. 2021',
};

const paymentMethod = {
    brand: 'Visa',
    last4: '2255',
    label: 'Primary card',
};

const hasOrderHistory = orderHistory.length > 0;
const hasActivePlan = Boolean(activePlan);
const hasPaymentMethod = Boolean(paymentMethod);
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

                <aside v-if="hasActivePlan" class="flex flex-col gap-4 rounded-[18px] npo-form-shadow  p-6 text-accent shadow-neu-out">
                    <p class="text-xs font-semibold tracking-[0.3em] text-accent/70 uppercase">Your plan</p>
                    <div class="space-y-1">
                        <h3 class="text-lg font-semibold">{{ activePlan.name }}</h3>
                        <p class="text-sm text-accent/80">Renews on {{ activePlan.renewsAt }}</p>
                    </div>
                    <button
                        type="button"
                        class="mt-auto inline-flex items-center justify-center rounded-[12px] border border-accent/70 px-4 py-2 text-xs font-semibold tracking-[0.2em] text-accent uppercase"
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

                <div
                    v-if="!hasPaymentMethod"
                    class="flex flex-col items-center justify-center gap-3 rounded-[14px] bg-background p-6 text-center shadow-neu-in"
                >
                    <div class="flex size-12 items-center justify-center rounded-full bg-surface shadow-neu-in">
                        <CreditCard class="h-5 w-5 text-accent/60" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-accent">No payment method</p>
                        <p class="text-xs text-accent/50">Add a card to keep subscriptions active.</p>
                    </div>
                    <button
                        type="button"
                        class="neu-button active rounded-[10px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-in"
                    >
                        Add card
                    </button>
                </div>

                <div v-else class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4 rounded-[12px] bg-background p-4 shadow-neu-in">
                        <div class="flex h-10 w-16 items-center justify-center rounded-[10px] bg-white text-sm font-semibold text-[#1a1f36] shadow-neu-in">
                            {{ paymentMethod.brand.toUpperCase() }}
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-accent">
                                {{ paymentMethod.brand }} ending in {{ paymentMethod.last4 }}
                            </span>
                            <span class="text-xs text-accent/50">{{ paymentMethod.label }}</span>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="neu-button active inline-flex items-center justify-center rounded-[10px] !bg-transparent px-4 py-2 text-xs font-semibold text-accent shadow-neu-in"
                    >
                        Remove
                    </button>
                </div>
            </section>
        </section>
    </AppLayout>
</template>
