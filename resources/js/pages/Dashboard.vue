<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type {
    BreadcrumbItem,
    DashboardPageProps,
    DashboardProperty,
    PlanUsagePayload,
    UsageBucketPayload,
    User,
} from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { TrendingUp } from 'lucide-vue-next';
import { computed } from 'vue';
import DashboardSection from '@/components/DashboardSection.vue';
import { Icon } from '@iconify/vue';
import CardDisplay from '@/components/card-display.vue';
import PropertyCard from '@/components/properties/property-card.vue';

type DashboardUser = User & {
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

const planDefinitions = {
    price_starter: { name: 'Starter' },
    price_pro: { name: 'Pro' },
    price_enterprise: { name: 'Enterprise' },
} as const;

const page = usePage<DashboardPageProps>();

const user = computed<DashboardUser | null>(() => {
    return (page.props.auth.user ?? null) as DashboardUser | null;
});

const planUsage = computed<PlanUsagePayload | null>(() => page.props.planUsage ?? null);
const docsUsage = computed<UsageBucketPayload | null>(() => planUsage.value?.usage?.docs ?? null);
const rendersUsage = computed<UsageBucketPayload | null>(() => planUsage.value?.usage?.renders ?? null);

const planKey = computed(() => {
    const plan = user.value?.plan_tier;
    return typeof plan === 'string' && plan.length > 0 ? plan.toLowerCase() : 'price_starter';
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
        };
    }

    const key = planKey.value.replace(/^price_/, '');
    const preset = planDefinitions[key as keyof typeof planDefinitions];

    return {
        label: preset?.name ?? formatTitle(key),
    };
});

type UsageState = 'success' | 'warning' | 'danger' | 'blocked' | 'unlimited';

const stateToGradient = (state: UsageState) => {
    switch (state) {
        case 'warning':
            return 'from-[#FFE29A] to-[#FFB74A]';
        case 'danger':
            return 'from-[#FF8A8A] to-[#EA5455]';
        default:
            return 'from-[#6e33ff] to-[#864ffe]';
    }
};

const stateToTextClass = (state: UsageState) => {
    switch (state) {
        case 'warning':
            return 'text-[#9A6B00]';
        case 'danger':
        case 'blocked':
            return 'text-[#B91C1C]';
        default:
            return 'text-accent';
    }
};

const buildUsageStats = (bucket: UsageBucketPayload | null, label: string, unit: string) => {
    const limit = bucket?.limit ?? 0;
    const used = bucket?.used ?? 0;
    const isUnlimited = bucket?.is_unlimited ?? limit === -1;
    const isBlocked = bucket?.is_blocked ?? limit === 0;
    const remaining =
        bucket?.remaining ??
        (isUnlimited ? null : isBlocked ? 0 : Math.max(0, limit - used));
    const percentUsed =
        bucket?.percent_used ??
        (limit > 0 ? Math.round((used / limit) * 100) : 0);
    const state: UsageState = isBlocked
        ? 'blocked'
        : isUnlimited
            ? 'unlimited'
            : percentUsed >= 100
                ? 'danger'
                : percentUsed >= 80
                    ? 'warning'
                    : 'success';
    const limitLabel = isUnlimited ? 'Unlimited' : `${limit}`;
    const remainingLabel = isUnlimited ? 'Unlimited' : `${remaining ?? 0}`;
    const percentText = isUnlimited ? 'Unlimited usage' : `${percentUsed}% of monthly ${unit} usage`;
    const remainingText = isUnlimited
        ? 'Unlimited capacity'
        : `${remaining ?? 0} ${unit} left`;
    const message = isBlocked
        ? `${label} are not included in your current plan.`
        : isUnlimited
            ? `Unlimited ${unit} this month.`
            : state === 'danger'
                ? `You reached your monthly ${unit} limit.`
                : state === 'warning'
                    ? `You're close to your monthly ${unit} limit.`
                    : `Great pace! You still have ${remaining ?? 0} ${unit} left this month.`;

    return {
        label,
        used,
        limit,
        remaining,
        limitLabel,
        remainingLabel,
        percentUsed,
        percentText,
        remainingText,
        message,
        state,
        progressGradient: stateToGradient(state),
        textClass: stateToTextClass(state),
        isUnlimited,
        isBlocked,
    };
};

const docsStats = computed(() => buildUsageStats(docsUsage.value, 'Docs', 'docs'));
const rendersStats = computed(() => buildUsageStats(rendersUsage.value, 'Renders', 'renders'));
const usageBuckets = computed(() => [
    { key: 'docs', stats: docsStats.value },
    { key: 'renders', stats: rendersStats.value },
]);

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

    return [];
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
                class="flex flex-col gap-6 rounded-[12px] p-4 md:flex-row md:items-center md:justify-between md:gap-10"
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
                <div class="flex flex-col gap-4 rounded-[12px] bg-surface p-4 npo-form-shadow">
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
                <div class="flex flex-col gap-4 rounded-[12px] bg-surface p-5 npo-form-shadow sm:w-full md:flex-1">
                    <h3 class="text-sm font-semibold tracking-wide text-accent uppercase">Plan usage</h3>
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="pointer-events-none flex size-12 items-center justify-center rounded-full text-accent shadow-neu-in"
                            >
                                <TrendingUp class="size-5 text-accent" />
                            </div>
                            <div>
                                <p class="text-xs tracking-wide text-accent uppercase">Current plan</p>
                                <p class="font-semibold text-accent/50">{{ planDetails.label }} Plan</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs tracking-wide text-accent uppercase">Monthly reset</p>
                            <p v-if="usageResetLabel" class="text-xs text-accent/50">Resets {{ usageResetLabel }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div
                            v-for="bucket in usageBuckets"
                            :key="bucket.key"
                            class="flex flex-col gap-3 rounded-[12px] p-4   shadow-neu-out"
                        >
                            <div class="flex items-center justify-between">
                                <p class="text-xs tracking-wide text-accent uppercase">{{ bucket.stats.label }}</p>
                                <p class="text-sm font-semibold text-accent">
                                    {{ bucket.stats.used }} / {{ bucket.stats.limitLabel }}
                                </p>
                            </div>
                            <div class="relative h-3 w-full overflow-hidden rounded-full bg-background shadow-neu-in">
                                <div
                                    class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r transition-all duration-500 ease-out"
                                    :class="bucket.stats.progressGradient"
                                    :style="{ width: `${bucket.stats.percentUsed}%` }"
                                />
                            </div>
                            <div class="flex items-center justify-between text-xs text-accent/50">
                                <span>{{ bucket.stats.percentText }}</span>
                                <span>{{ bucket.stats.remainingText }}</span>
                            </div>
                            <p
                                class="ring-accent-300 rounded-[12px] bg-background p-3 px-4 text-sm font-medium text-accent/50 ring-1 shadow-neu-in"
                                :class="bucket.stats.textClass"
                            >
                                {{ bucket.stats.message }}
                            </p>
                        </div>
                    </div>
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
