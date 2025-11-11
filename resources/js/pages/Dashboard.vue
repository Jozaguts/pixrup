<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { AppPageProps, BreadcrumbItem, User } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    Building2,
    CheckCircle2,
    Compass,
    Home,
    MapPin,
    Plus,
    TrendingUp,
    X,
} from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

type PropertyStatus = 'in-progress' | 'ready' | 'pending' | 'draft';

interface DashboardProperty {
    id: number | string;
    title: string;
    address: string;
    status: PropertyStatus;
    estimatedValue?: number;
    progress?: number;
    thumbnail?: string | null;
    links?: {
        view?: string;
        report?: string;
    };
}

type DashboardPageProps = AppPageProps<{
    properties?: DashboardProperty[];
}>;

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
        thumbnail:
            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=720&q=80',
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
        thumbnail:
            'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=720&q=80',
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

const statusStyles: Record<
    PropertyStatus,
    { label: string; badgeClass: string; dotClass: string }
> = {
    'in-progress': {
        label: 'In Progress',
        badgeClass:
            'bg-[#FFF4DA] text-[#9A6B00] shadow-[inset_2px_2px_6px_rgba(226,189,116,0.4)]',
        dotClass: 'bg-[#FFB74A]',
    },
    ready: {
        label: 'Ready',
        badgeClass:
            'bg-[#E4F9F0] text-[#0B6B4F] shadow-[inset_2px_2px_6px_rgba(134,212,182,0.35)]',
        dotClass: 'bg-[#1DBE78]',
    },
    pending: {
        label: 'Pending Review',
        badgeClass:
            'bg-[#E9EDFF] text-[#2E3A8C] shadow-[inset_2px_2px_6px_rgba(124,137,224,0.35)]',
        dotClass: 'bg-[#4C5FD5]',
    },
    draft: {
        label: 'Draft',
        badgeClass:
            'bg-[#F1F2F6] text-[#4B5563] shadow-[inset_2px_2px_6px_rgba(163,169,187,0.35)]',
        dotClass: 'bg-[#9CA3AF]',
    },
};

const page = usePage<DashboardPageProps>();

const user = computed<DashboardUser | null>(() => {
    return (page.props.auth.user ?? null) as DashboardUser | null;
});

const planKey = computed(() => {
    const plan = user.value?.plan_tier ?? user.value?.plan;
    return typeof plan === 'string' && plan.length > 0
        ? plan.toLowerCase()
        : 'professional';
});

const formatTitle = (value: string) =>
    value
        .replace(/[_-]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
        .replace(/\b\w/g, (char) => char.toUpperCase());

const planDetails = computed(() => {
    const key = planKey.value;
    const preset = planDefinitions[key as keyof typeof planDefinitions];
    const limitFromUser = user.value?.property_usage_limit;
    const fallbackLimit =
        preset?.limit === undefined ? 20 : preset?.limit ?? null;
    const usageLimit = limitFromUser ?? fallbackLimit;

    return {
        label: preset?.name ?? formatTitle(key),
        limit: usageLimit,
    };
});

const usageCount = computed(() => user.value?.property_usage_count ?? 0);

const usageLimit = computed(() => planDetails.value.limit ?? null);

const usageLimitLabel = computed(() => {
    const limit = usageLimit.value;
    return limit == null ? 'Unlimited' : `${limit}`;
});

const remainingSlots = computed(() => {
    const limit = usageLimit.value;
    if (!limit) {
        return null;
    }

    return Math.max(limit - usageCount.value, 0);
});

const usagePercent = computed(() => {
    const limit = usageLimit.value;
    if (!limit) {
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
            return 'from-[#A7F3D0] to-[#34D399]';
    }
});

const usageMessage = computed(() => {
    if (usageLimit.value == null) {
        return 'Unlimited property slots—keep building your portfolio.';
    }

    if (usageState.value === 'danger') {
        return 'You have reached your property limit. Upgrade to keep scaling.';
    }

    if (usageState.value === 'warning') {
        return 'Almost there! Consider upgrading your plan to add more properties.';
    }

    const slots = remainingSlots.value ?? 0;
    return `Great pace! You still have ${slots} property slot${slots === 1 ? '' : 's'} available.`;
});

const usagePercentText = computed(() => {
    if (usageLimit.value == null) {
        return 'Unlimited plan';
    }

    return `${usagePercent.value}% used`;
});

const remainingSlotsText = computed(() => {
    if (usageLimit.value == null) {
        return 'Unlimited capacity';
    }

    const slots = remainingSlots.value ?? 0;
    return `${slots} slot${slots === 1 ? '' : 's'} left`;
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

const hasProperties = computed(() => resolvedProperties.value.length > 0);

const isUsingMockData = computed(() => !Array.isArray(page.props.properties));

const currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
});

const formatCurrency = (value?: number) => {
    if (value == null) {
        return '—';
    }

    return currencyFormatter.format(value);
};

const openNewPropertyWizard = () => {
    router.visit('/properties/new');
};

const visitLink = (link?: string) => {
    if (!link) {
        return;
    }

    router.visit(link);
};

const successToastStorageKey = 'pixrup:new-property-toast';
const propertyToastMessage = ref('');
const flashStatus = computed(() => page.props.flash?.status ?? null);

onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }

    const stored = window.sessionStorage.getItem(successToastStorageKey);

    if (stored) {
        propertyToastMessage.value = stored;
        window.sessionStorage.removeItem(successToastStorageKey);
    }
});

watch(
    flashStatus,
    (status) => {
        if (status === 'property-created') {
            propertyToastMessage.value = 'Property successfully created 🎉';
        }
    },
    { immediate: true },
);

const dismissPropertyToast = () => {
    propertyToastMessage.value = '';
};
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 text-[#1f2933]">
            <Transition name="fade-slide">
                <div
                    v-if="propertyToastMessage"
                    class="neu-surface flex flex-col gap-3 rounded-[24px] bg-[#f4f5fa] px-5 py-4 text-sm text-[#1f2933] shadow-neu-out md:flex-row md:items-center md:justify-between"
                >
                    <div class="flex items-center gap-3 text-[#1f2933]">
                        <CheckCircle2 class="size-5 text-[#1fbf75]" />
                        <span class="font-semibold">
                            {{ propertyToastMessage }}
                        </span>
                    </div>
                    <button
                        type="button"
                        class="neu-btn inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-xs font-semibold text-[#6b7280] transition-all hover:text-[#1f2933]"
                        @click="dismissPropertyToast"
                    >
                        <X class="size-4" />
                        Dismiss
                    </button>
                </div>
            </Transition>

            <section
                class="neu-surface flex flex-col gap-6 rounded-[28px] bg-[#f4f5fa] p-6 shadow-neu-out md:flex-row md:items-center md:justify-between md:gap-10"
            >
                <div class="flex flex-col gap-2">
                    <h1
                        class="text-2xl font-semibold tracking-tight md:text-3xl"
                    >
                        Welcome back, {{ firstName }} 👋
                    </h1>
                    <p class="text-sm text-[#6b7280] md:text-base">
                        Here’s your property summary.
                    </p>
                </div>

                <button
                    type="button"
                    class="group relative inline-flex items-center gap-2 rounded-2xl bg-[#7C4DFF] px-5 py-3 font-semibold text-white shadow-[12px_12px_24px_rgba(78,47,155,0.35),-12px_-12px_24px_rgba(152,117,255,0.45)] transition-all duration-200 ease-out hover:shadow-[inset_8px_8px_18px_rgba(78,47,155,0.35),inset_-8px_-8px_18px_rgba(152,117,255,0.35)] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#7C4DFF] focus-visible:ring-offset-2 focus-visible:ring-offset-[#f4f5fa]"
                    @click="openNewPropertyWizard"
                >
                    <Plus class="size-5" />
                    <span class="whitespace-nowrap">New Property</span>
                </button>
            </section>

            <section
                class="neu-surface flex flex-col gap-6 rounded-[28px] bg-[#f4f5fa] p-6 shadow-neu-out"
            >
                <header class="flex flex-col gap-2">
                    <h2 class="text-lg font-semibold md:text-xl">Plan usage</h2>
                    <p class="text-sm text-[#6b7280]">
                        Keeping track of your plan ensures you always have room
                        to grow.
                    </p>
                </header>

                <div
                    class="grid gap-6 md:grid-cols-[minmax(0,1fr)_minmax(0,280px)] md:items-center"
                >
                    <div class="flex flex-col gap-4">
                        <div
                            class="neu-surface flex flex-col gap-4 rounded-[24px] bg-[#f4f5fa] p-5"
                        >
                            <div
                                class="flex items-center justify-between gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex size-12 items-center justify-center rounded-2xl bg-white/80 text-[#7C4DFF] shadow-[6px_6px_16px_rgba(200,206,224,0.4),-6px_-6px_16px_rgba(255,255,255,0.8)]"
                                    >
                                        <TrendingUp class="size-5" />
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-[#9CA3AF] uppercase"
                                        >
                                            Current plan
                                        </p>
                                        <p class="font-semibold text-[#3f3f46]">
                                            {{ planDetails.label }} Plan
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p
                                        class="text-xs tracking-wide text-[#9CA3AF] uppercase"
                                    >
                                        Properties registered
                                    </p>
                                    <p class="text-lg font-semibold">
                                        {{ usageCount }} / {{
                                            usageLimitLabel
                                        }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3">
                                <div
                                    class="relative h-3 w-full overflow-hidden rounded-full bg-[#e4e6ee] shadow-inner"
                                >
                                    <div
                                        class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r transition-all duration-500 ease-out"
                                        :class="progressGradient"
                                        :style="{ width: `${usagePercent}%` }"
                                    />
                                </div>
                                <div
                                    class="flex items-center justify-between text-xs text-[#6b7280]"
                                >
                                    <span>{{ usagePercentText }}</span>
                                    <span>{{ remainingSlotsText }}</span>
                                </div>
                            </div>
                        </div>

                        <p
                            class="px-4 py-3 text-sm"
                            :class="{
                                'text-[#1f2933]': usageState === 'success',
                                'text-[#9A6B00]': usageState === 'warning',
                                'text-[#B91C1C]': usageState === 'danger',
                            }"
                        >
                            {{ usageMessage }}
                        </p>
                    </div>

                    <div
                        class="neu-surface flex flex-col gap-4 rounded-[24px] p-5"
                    >
                        <h3
                            class="text-sm font-semibold tracking-wide text-[#6b7280] uppercase"
                        >
                            Quick actions
                        </h3>
                        <button
                            type="button"
                            class="neu-btn flex items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold text-[#7C4DFF] transition-colors hover:text-[#5c35c4]"
                            @click="openNewPropertyWizard"
                        >
                            <Plus class="size-4" />
                            Add property
                        </button>
                        <button
                            type="button"
                            class="neu-btn flex items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold text-[#1f2933] transition-colors hover:text-[#111827]"
                            @click="visitLink('/billing')"
                        >
                            <Compass class="size-4" />
                            Explore plans
                        </button>
                        <p class="text-xs text-[#94a3b8]">
                            These shortcuts stay at hand so you can act quickly
                            as soon as you land.
                        </p>
                    </div>
                </div>
            </section>

            <section
                class="neu-surface flex flex-col gap-6 rounded-[28px] p-6 shadow-neu-out"
            >
                <header
                    class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-semibold md:text-xl">
                            Your properties
                        </h2>
                        <p class="text-sm text-[#6b7280]">
                            Track status, values, and jump back into each
                            project.
                        </p>
                    </div>

                    <span
                        v-if="isUsingMockData"
                        class="rounded-full px-4 py-2 text-xs text-[#9CA3AF] shadow-[inset_4px_4px_12px_rgba(193,199,216,0.35),inset_-4px_-4px_12px_rgba(255,255,255,0.85)]"
                    >
                        Preview data shown — connect API to replace.
                    </span>
                </header>

                <div
                    v-if="hasProperties"
                    class="grid gap-5 md:grid-cols-2 xl:grid-cols-3"
                >
                    <article
                        v-for="property in resolvedProperties"
                        :key="property.id"
                        class="neu-surface group flex h-full flex-col overflow-hidden"
                    >
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img
                                v-if="property.thumbnail"
                                :src="property.thumbnail"
                                :alt="`Preview of ${property.title}`"
                                class="size-full object-cover"
                            />
                            <div
                                v-else
                                class="flex size-full items-center justify-center bg-gradient-to-br from-[#eef1fb] via-[#f4f5fa] to-[#dbe2ff] text-[#7C4DFF]"
                            >
                                <img src="/images/pixrup-icon.svg" alt="pixrup icon" class="w-15 h-15">
                            </div>

                            <div
                                class="absolute top-4 left-4 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium"
                                :class="
                                    statusStyles[property.status].badgeClass
                                "
                            >
                                <span
                                    class="size-2 rounded-full"
                                    :class="
                                        statusStyles[property.status].dotClass
                                    "
                                />
                                {{ statusStyles[property.status].label }}
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col gap-4 p-5">
                            <div class="flex flex-col gap-2">
                                <h3
                                    class="text-base font-semibold text-[#1f2933]"
                                >
                                    {{ property.title }}
                                </h3>
                                <p
                                    class="flex items-center gap-2 text-sm text-[#6b7280]"
                                >
                                    <MapPin class="size-4 text-[#7C4DFF]" />
                                    <span>{{ property.address }}</span>
                                </p>
                            </div>

                            <div
                                class="flex flex-wrap items-center gap-4 text-sm text-[#475569]"
                            >
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs tracking-wide text-[#9CA3AF] uppercase"
                                    >
                                        Estimated value
                                    </span>
                                    <span class="font-semibold">
                                        {{
                                            formatCurrency(
                                                property.estimatedValue,
                                            )
                                        }}
                                    </span>
                                </div>
                                <div
                                    v-if="property.progress != null"
                                    class="flex flex-col"
                                >
                                    <span
                                        class="text-xs tracking-wide text-[#9CA3AF] uppercase"
                                    >
                                        Progress
                                    </span>
                                    <span class="font-semibold">
                                        {{ property.progress }}%
                                    </span>
                                </div>
                            </div>

                            <div class="mt-auto flex flex-wrap gap-3">
                                <button
                                    type="button"
                                    class="neu-btn flex-1 rounded-2xl bg-white/90 px-4 py-2 text-sm font-semibold text-[#1f2933]"
                                    @click="visitLink(property.links?.view)"
                                >
                                    View project
                                </button>
                                <button
                                    type="button"
                                    class="neu-btn flex-1 rounded-2xl bg-white/90 px-4 py-2 text-sm font-semibold text-[#7C4DFF]"
                                    @click="visitLink(property.links?.report)"
                                >
                                    Report
                                </button>
                            </div>
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="neu-surface flex flex-col items-center justify-center gap-4 rounded-[28px] bg-[#f4f5fa] p-12 text-center shadow-neu-in"
                >
                    <div
                        class="flex size-16 items-center justify-center rounded-3xl bg-white/90 text-[#7C4DFF] shadow-[8px_8px_20px_rgba(193,199,216,0.35),-8px_-8px_20px_rgba(255,255,255,0.8)]"
                    >
                        <Building2 class="size-8" />
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-semibold text-[#1f2933]">
                            You haven’t added any properties yet.
                        </h3>
                        <p class="text-sm text-[#6b7280]">
                            Start by adding your first property to unlock
                            tailored dashboards and reports.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="group inline-flex items-center gap-2 rounded-2xl bg-[#7C4DFF] px-5 py-3 font-semibold text-white shadow-[12px_12px_24px_rgba(78,47,155,0.35),-12px_-12px_24px_rgba(152,117,255,0.45)] transition-all duration-200 ease-out hover:shadow-[inset_8px_8px_18px_rgba(78,47,155,0.35),inset_-8px_-8px_18px_rgba(152,117,255,0.35)]"
                        @click="openNewPropertyWizard"
                    >
                        <Plus class="size-5" />
                        Add your first property
                    </button>
                </div>
            </section>
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
