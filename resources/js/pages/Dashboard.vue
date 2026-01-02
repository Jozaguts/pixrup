<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, DashboardPageProps, DashboardProperty, PlanUsagePayload, User } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { TrendingUp } from 'lucide-vue-next';
import { computed } from 'vue';
import DashboardSection from '@/components/DashboardSection.vue';
import { Icon } from '@iconify/vue';
import CardDisplay from '@/components/card-display.vue';
import PropertyCard from '@/components/properties/property-card.vue';

type DashboardUser = User & {
    plan?: string | null;
    plan_tier?: string | null;
    property_usage_limit?: number | null;
    property_usage_count?: number | null;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const mockProperties: DashboardProperty[] = [
    {
        id: 1,
        title: 'Skyline Loft Renovation',
        address: '455 Grand Ave, Brooklyn, NY',
        status: 'in-progress',
        estimatedValue: 1120000,
        progress: 68,
        thumbnail: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=720&q=80',
        links: {
            view: '/properties/1',
            report: '/properties/1/report',
        },
    },
    {
        id: 2,
        title: 'Palm Heights Villas',
        address: '920 Ocean Drive, Miami, FL',
        status: 'ready',
        estimatedValue: 1780000,
        progress: 100,
        thumbnail: 'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=720&q=80',
        links: {
            view: '/properties/2',
            report: '/properties/2/report',
        },
    },
    {
        id: 3,
        title: 'Lakeside Retreat',
        address: '88 Maplewood Lane, Austin, TX',
        status: 'pending',
        estimatedValue: 640000,
        progress: 42,
        thumbnail: null,
        links: {
            view: '/properties/3',
            report: '/properties/3/report',
        },
    },
];

const planDefinitions = {
    free: { name: 'Free', limit: 1 },
    micro: { name: 'Micro', limit: 5 },
    starter: { name: 'Starter', limit: 12 },
    pro: { name: 'Professional', limit: 25 },
    professional: { name: 'Professional', limit: 25 },
    business: { name: 'Business', limit: 50 },
    premium: { name: 'Premium', limit: 80 },
    enterprise: { name: 'Enterprise', limit: null },
} as const;

const page = usePage<DashboardPageProps>();

const user = computed<DashboardUser | null>(() => {
    return (page.props.auth.user ?? null) as DashboardUser | null;
});

const planUsage = computed<PlanUsagePayload | null>(() => page.props.planUsage ?? null);

const planKey = computed(() => {
    const plan = user.value?.plan_tier ?? user.value?.plan;
    return typeof plan === 'string' && plan.length > 0 ? plan.toLowerCase() : 'professional';
});

const formatTitle = (value: string) =>
    value
        .replace(/[_-]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
        .replace(/\b\w/g, (char) => char.toUpperCase());

const planDetails = computed(() => {
    const usagePlan = planUsage.value?.plan;
    if (usagePlan) {
        return {
            label: usagePlan.label ?? formatTitle(usagePlan.tier),
            limit: planUsage.value?.limit ?? usagePlan.limit ?? null,
        };
    }

    const key = planKey.value;
    const preset = planDefinitions[key as keyof typeof planDefinitions];
    const fallbackLimit = preset?.limit === undefined ? 20 : (preset?.limit ?? null);

    return {
        label: preset?.name ?? formatTitle(key),
        limit: fallbackLimit,
    };
});

const usageCount = computed(() => planUsage.value?.used ?? 0);

const usageLimit = computed(() => planUsage.value?.limit ?? planDetails.value.limit ?? null);

const usageLimitLabel = computed(() => {
    const limit = usageLimit.value;
    return limit == null ? 'Unlimited' : `${limit}`;
});

const remainingSlots = computed(() => {
    if (planUsage.value?.remaining !== undefined) {
        const remaining = planUsage.value.remaining;
        if (remaining === null) {
            return null;
        }

        return Math.max(remaining, 0);
    }

    const limit = usageLimit.value;
    if (!limit) {
        return null;
    }

    return Math.max(limit - usageCount.value, 0);
});

const usagePercent = computed(() => {
    const limit = usageLimit.value;
    if (!limit || limit <= 0) {
        return 0;
    }

    const percent = Math.round((usageCount.value / limit) * 100);
    return Math.max(0, Math.min(100, percent));
});

const usageState = computed<'success' | 'warning' | 'danger'>(() => {
    if (usagePercent.value >= 100) {
        return 'danger';
    }
    if (usagePercent.value >= 80) {
        return 'warning';
    }
    return 'success';
});

const progressGradient = computed(() => {
    switch (usageState.value) {
        case 'warning':
            return 'from-[#FFE29A] to-[#FFB74A]';
        case 'danger':
            return 'from-[#FF8A8A] to-[#EA5455]';
        default:
            return 'from-[#6e33ff] to-[#864ffe]';
    }
});

const usageMessage = computed(() => {
    if (usageLimit.value == null) {
        return 'Unlimited monthly property usage—enjoy full access to your tools.';
    }

    if (usageState.value === 'danger') {
        return 'You reached your monthly property usage limit. Upgrade to keep generating reports and AI outputs.';
    }

    if (usageState.value === 'warning') {
        return 'You are close to your monthly usage limit. Consider upgrading for more capacity.';
    }

    const slots = remainingSlots.value ?? 0;
    return `Great pace! You still have ${slots} property use${slots === 1 ? '' : 's'} left this month.`;
});

const usagePercentText = computed(() => {
    if (usageLimit.value == null) {
        return 'Unlimited plan';
    }

    return `${usagePercent.value}% of monthly usage`;
});

const remainingSlotsText = computed(() => {
    if (usageLimit.value == null) {
        return 'Unlimited capacity';
    }

    const slots = remainingSlots.value ?? 0;
    return `${slots} use${slots === 1 ? '' : 's'} left`;
});

const usageResetLabel = computed(() => {
    const resetAt = planUsage.value?.resets_at;
    if (!resetAt) {
        return null;
    }

    const date = new Date(resetAt);
    if (Number.isNaN(date.getTime())) {
        return null;
    }

    return date.toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
});

const firstName = computed(() => {
    const fullName = user.value?.name ?? '';
    return fullName.split(' ')[0] || fullName || 'there';
});

const resolvedProperties = computed<DashboardProperty[]>(() => {
    const provided = page.props.properties;

    if (Array.isArray(provided)) {
        return provided;
    }

    return mockProperties;
});

const openNewPropertyWizard = () => {
    router.visit('/properties/new');
};

const visitLink = (link?: string) => {
    if (!link) {
        return;
    }

    router.visit(link);
};
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 text-accent">
            <section
                class="flex flex-col gap-6 rounded-[12px] p-6 md:flex-row md:items-center md:justify-between md:gap-10"
            >
                <div class="flex flex-col gap-2">
                    <h1 class="text-3xl font-semibold tracking-tight md:text-3xl">
                        Welcome back, {{ firstName }}
                        <Icon icon="mdi:hand-wave-outline" class="ml-2 inline h-8 w-8" />
                    </h1>
                    <p class="text-md ml-2 text-accent/50 md:text-base">Here’s your property summary.</p>
                </div>
            </section>

            <DashboardSection class="flex flex-col items-stretch gap-6 md:flex-row lg:flex-row">
                <!-- Quick Actions -->
                <div class="flex flex-col gap-4 rounded-[12px] bg-surface p-5 shadow-neu-in">
                    <h3 class="text-sm font-semibold tracking-wide text-accent uppercase">Quick actions</h3>
                    <button
                        type="button"
                        class="neu-button flex transform cursor-pointer items-center justify-center gap-2 rounded-[12px] !bg-transparent px-4 py-4 text-sm font-medium text-accent transition-transform duration-200 ease-out hover:scale-101"
                        @click="openNewPropertyWizard"
                    >
                        New property
                        <Icon icon="mdi:office-building-plus-outline" class="ml-2 !size-6" />
                    </button>
                    <button
                        type="button"
                        class="neu-button flex transform cursor-pointer items-center justify-center gap-2 rounded-[12px] !bg-transparent px-4 py-4 text-sm font-medium text-accent transition-transform duration-200 ease-out hover:scale-101"
                        @click="visitLink('/billing')"
                    >
                        Explore plans
                        <Icon icon="mdi:text-box-search-outline" class="ml-2 !size-6" />
                    </button>
                    <p class="text-xs text-accent/50">
                        These shortcuts stay at hand so you can act quickly as soon as you land.
                    </p>
                </div>

                <!-- Plan usage -->
                <div class="flex flex-col gap-4 rounded-[12px] bg-surface p-5 shadow-neu-in sm:w-full md:flex-1">
                    <h3 class="text-sm font-semibold tracking-wide text-accent uppercase">Plan usage</h3>
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="pointer-events-none flex size-12 items-center justify-center rounded-full text-black shadow-neu-in"
                            >
                                <TrendingUp class="size-5 text-accent" />
                            </div>
                            <div>
                                <p class="text-xs tracking-wide text-accent uppercase">Current plan</p>
                                <p class="font-semibold text-accent/50">{{ planDetails.label }} Plan</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs tracking-wide text-accent uppercase">Properties used this month</p>
                            <p class="text-lg font-semibold text-accent">{{ usageCount }} / {{ usageLimitLabel }}</p>
                            <p v-if="usageResetLabel" class="text-xs text-accent/50">Resets {{ usageResetLabel }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="relative h-3 w-full overflow-hidden rounded-full bg-surface shadow-neu-in">
                            <div
                                class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r transition-all duration-500 ease-out"
                                :class="progressGradient"
                                :style="{ width: `${usagePercent}%` }"
                            />
                        </div>
                        <div class="flex items-center justify-between text-xs text-accent/50">
                            <span>{{ usagePercentText }}</span>
                            <span>{{ remainingSlotsText }}</span>
                        </div>
                    </div>
                    <p
                        class="ring-accent-300 rounded-[12px] bg-surface p-3 px-5 text-sm font-medium text-accent/50 ring-1 shadow-neu-in"
                        :class="{
                            'text-accent': usageState === 'success',
                            'text-[#9A6B00]': usageState === 'warning',
                            'text-[#B91C1C]': usageState === 'danger',
                        }"
                    >
                        {{ usageMessage }}
                    </p>
                </div>
            </DashboardSection>

            <DashboardSection
                title="Your properties"
                description="Track status, values, and jump back into each project."
                class="flex flex-col gap-6 py-5"
            >
                <CardDisplay class="lg:grid-cols-12 md:grid-cols-12 grid gap-0">
                    <PropertyCard
                        v-for="property in resolvedProperties"
                        :key="property.id"
                        :item="property"
                        class="cols-span-12 md:cols-span-3 "
                    />
                </CardDisplay>
            </DashboardSection>
        </div>
    </AppLayout>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition:
        opacity 0.2s ease,
        transform 0.2s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(6px);
}
</style>
