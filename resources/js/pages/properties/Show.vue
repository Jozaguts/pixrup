<script setup lang="ts">
import PropertyWorkspaceCollab from '@/components/properties/workspace/PropertyWorkspaceCollab.vue';
import PropertyWorkspaceGlowUp from '@/components/properties/workspace/PropertyWorkspaceGlowUp.vue';
import PropertyWorkspaceOverview from '@/components/properties/workspace/PropertyWorkspaceOverview.vue';
import PropertyWorkspaceSeal from '@/components/properties/workspace/PropertyWorkspaceSeal.vue';
import PropertyWorkspaceSpyHunt from '@/components/properties/workspace/PropertyWorkspaceSpyHunt.vue';
import PropertyWorkspaceVision from '@/components/properties/workspace/PropertyWorkspaceVision.vue';
import PropertyWorkspaceWorth from '@/components/properties/workspace/PropertyWorkspaceWorth.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import propertiesRoutes from '@/routes/properties';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import {
    Box,
    Building2,
    CalendarClock,
    Gauge,
    Home,
    LayoutDashboard,
    LineChart,
    MapPin,
    MessageCircle,
    Radar,
    Sparkles,
    Stamp,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

import type {
    ModuleId,
    PropertyWorkspaceProperty,
    WorkspaceAction,
    WorkspaceModuleMeta,
} from '@/components/properties/workspace/types';
import NeuphormistTabs from "@/components/NeuphormistTabs.vue";

interface Props {
    property: PropertyWorkspaceProperty;
}

const props = defineProps<Props>();

interface WorkspaceModuleDefinition {
    id: ModuleId;
    label: string;
    subtitle: string;
    icon: typeof LayoutDashboard | string;
    component: unknown;
    description: string;
}

const modules: WorkspaceModuleDefinition[] = [
    {
        id: 'overview',
        label: 'Overview',
        subtitle: 'Summary',
        icon: 'material-symbols-light:overview-outline',
        component: PropertyWorkspaceOverview,
        description: 'Snapshot of property vitals and workspace activity.',
    },
    {
        id: 'pixrWorth',
        label: 'PixrWorth',
        subtitle: 'Appraisal',
        icon: 'mdi:scale-balance',
        component: PropertyWorkspaceWorth,
        description: 'Automated valuation insights and comps.',
    },
    {
        id: 'pixrGlowUp',
        label: 'PixrGlowUp',
        subtitle: 'AI Renovation',
        icon: 'mdi:chart-finance',
        component: PropertyWorkspaceGlowUp,
        description: 'Before/after AI visualizations and renovation jobs.',
    },
    {
        id: 'pixrSpyHunt',
        label: 'PixrSpyHunt',
        subtitle: 'Market Intel',
        icon: 'mdi:target-arrow',
        component: PropertyWorkspaceSpyHunt,
        description: 'Competitive market scan and nearby activity.',
    },
    // {
    //     id: 'pixrVision',
    //     label: 'PixrVision',
    //     subtitle: '3D Tour',
    //     icon: 'mdi:monitor-eye',
    //     component: PropertyWorkspaceVision,
    //     description: 'Immersive tour and assets management.',
    // },
    // {
    //     id: 'pixrSeal',
    //     label: 'PixrSeal',
    //     subtitle: 'Report Builder',
    //     icon: 'mdi:certificate-outline',
    //     component: PropertyWorkspaceSeal,
    //     description: 'Generate investor-ready docs and shareables.',
    // },
    // {
    //     id: 'pixrCollab',
    //     label: 'PixrCollab',
    //     subtitle: 'Realtime Chat',
    //     icon: 'mdi:account-group',
    //     component: PropertyWorkspaceCollab,
    //     description: 'Collaborate with partners and clients live.',
    // },
];

const activeModuleId = ref<ModuleId>('overview');

const moduleMeta = computed<Record<string, WorkspaceModuleMeta>>(
    () => props.property.workspace?.modules ?? {},
);

const actionButtons = computed<WorkspaceAction[]>(
    () => props.property.workspace?.actions ?? [],
);

const propertyId = computed(() => props.property.id);

const statusTokens: Record<
    string,
    { label: string; badge: string; dot: string }
> = {
    'in-progress': {
        label: 'In Progress',
        badge: 'bg-surface text-accent shadow-neu-in ',
        dot: 'bg-primary',
    },
    ready: {
        label: 'Ready',
        badge: 'bg-[#E4F9F0] text-[#0B6B4F] shadow-[inset_4px_4px_12px_rgba(134,212,182,0.35)]',
        dot: 'bg-[#1DBE78]',
    },
    processing: {
        label: 'Processing',
        badge: 'bg-[#E9EDFF] text-[#2E3A8C] shadow-[inset_4px_4px_12px_rgba(124,137,224,0.35)]',
        dot: 'bg-[#4C5FD5]',
    },
    draft: {
        label: 'Draft',
        badge: 'bg-[#F1F2F6] text-[#4B5563] shadow-[inset_4px_4px_12px_rgba(163,169,187,0.35)]',
        dot: 'bg-[#9CA3AF]',
    },
    'needs-action': {
        label: 'Needs Attention',
        badge: 'bg-[#FFE6E6] text-[#9A1B1B] shadow-[inset_4px_4px_12px_rgba(230,149,149,0.35)]',
        dot: 'bg-[#EA5455]',
    },
};
const propertyDisplayName = computed(() => {
    const fallbackId = propertyId.value
        ? `Property #${propertyId.value}`
        : 'Property';
    return props.property.title?.trim()?.length
        ? props.property.title
        : (composedAddress.value ?? fallbackId);
});
const breadcrumbs = computed<BreadcrumbItem[]>(() => {
    const id = propertyId.value;
    const fallbackHref = dashboard.url();
    const propertyHref = id
        ? propertiesRoutes.show.url({ property: id })
        : fallbackHref;
    return [
        {
            title: 'Dashboard',
            href: fallbackHref,
        },
        {
            title: propertyDisplayName.value,
            href: propertyHref,
        },
    ];
});

const propertyStatus = computed(() => {
    const statusKey = (props.property.status ?? 'in-progress').toString();
    return statusTokens[statusKey] ?? statusTokens['in-progress'];
});

const activateModule = (id: ModuleId) => {
    activeModuleId.value = id;
};

const activeModule = computed(
    () =>
        modules.find((module) => module.id === activeModuleId.value) ??
        modules[0],
);

const activeModuleMeta = computed(
    () => moduleMeta.value[activeModule.value.id] ?? null,
);

const composedAddress = computed(() => {
    const address = props.property.address;
    if (!address) {
        return null;
    }

    const segments = [
        address.line1,
        [address.city, address.state].filter(Boolean).join(', '),
    ]
        .filter((value) => value && value.trim().length > 0)
        .map((value) => value?.trim());

    if (address.postal_code) {
        segments[segments.length - 1] = [
            segments[segments.length - 1],
            address.postal_code,
        ]
            .filter(Boolean)
            .join(' ');
    }

    return segments.filter(Boolean).join(' • ');
});

const headerMetricCards = computed(() => {
    const summary = props.property.summary ?? {};
    const pricing = props.property.pricing ?? {};

    return [
        {
            id: 'bedrooms',
            label: 'Bedrooms',
            value: summary.bedrooms ? `${summary.bedrooms}` : '—',
            icon: Home,
        },
        {
            id: 'bathrooms',
            label: 'Bathrooms',
            value: summary.bathrooms ? `${summary.bathrooms}` : '—',
            icon: Gauge,
        },
        {
            id: 'livingArea',
            label: 'Living Area',
            value: summary.livingArea
                ? `${Intl.NumberFormat('en-US').format(summary.livingArea)} sq ft`
                : '—',
            icon: Building2,
        },
        {
            id: 'valuation',
            label: 'Current Estimate',
            value: pricing.currentEstimate
                ? `$${Intl.NumberFormat('en-US', {
                      maximumFractionDigits: 0,
                  }).format(pricing.currentEstimate)}`
                : '—',
            icon: LineChart,
        },
    ];
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="props.property.title ?? 'Property Workspace'" />

        <div class="min-h-screen pt-10 pb-16">
            <div
                class="flex flex-col gap-4"
            >
                <section
                    :class="[
                        'relative overflow-hidden lg:px-6 md:px-6',
                    ]"
                >
                    <div
                        class="flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <p
                                    class="text-xs font-medium tracking-[0.35em] text-accent uppercase "
                                >
                                    Property Workspace
                                </p>
                                <h1
                                    class="text-2xl font-semibold tracking-tight text-accent/50 sm:text-3xl md:text-4xl"
                                >
                                    {{ props.property.title ?? 'Property' }}
                                </h1>
                            </div>

                            <div
                                class="flex flex-col gap-4 text-sm text-accent sm:text-base"
                            >
                                <div class="flex flex-wrap items-center gap-4">
                                    <span
                                        class="flex items-center gap-3 rounded-full px-4 py-2 text-sm font-medium"
                                        :class="propertyStatus.badge"
                                    >
                                        <span
                                            class="inline-flex h-2.5 w-2.5 rounded-full"
                                            :class="propertyStatus.dot"
                                        />
                                        {{ propertyStatus.label }}
                                    </span>

                                    <div
                                        class="flex items-center gap-2 text-accent/50"
                                    >
                                        <CalendarClock class="h-4 w-4" />
                                        <span
                                            >Updated
                                            {{
                                                props.property
                                                    .last_updated_human ??
                                                'recently'
                                            }}</span
                                        >
                                    </div>
                                </div>


                                <div
                                    v-if="props.property.tags?.length"
                                    class="flex flex-wrap gap-3"
                                >
                                    <span
                                        v-for="tag in props.property.tags"
                                        :key="tag"
                                        class="rounded-full px-4 py-1.5 text-xs font-semibold tracking-wider text-[#7c4dff] uppercase"
                                        :class="[
                                            'bg-[#f7f4ff]',
                                            'shadow-[4px_4px_10px_rgba(186,162,255,0.35),-4px_-4px_10px_rgba(255,255,255,0.85)]',
                                        ]"
                                    >
                                        {{ tag }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex w-full flex-col gap-5 lg:w-auto">
                            <div class="grid gap-4 sm:grid-cols-2 ">
                                <div
                                    v-for="metric in headerMetricCards"
                                    :key="metric.id"
                                    class="flex items-center gap-4 rounded-[12px] p-4 text-sm text-accent bg-surface shadow-neu-in"
                                >
                                         <component
                                             :is="metric.icon"
                                             class="!h-4 !w-4 text-[#7c4dff]"
                                         />
                                    <div>
                                        <p
                                            class="text-xs tracking-[0.25em] text-accent uppercase"
                                        >
                                            {{ metric.label }}
                                        </p>
                                        <p class="font-semibold text-accent/50">
                                            {{ metric.value }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section
                    :id="`module-${activeModule.id}`"
                    role="tabpanel"
                    :aria-labelledby="`tab-${activeModule.id}`"
                    class="relative min-h-[420px] rounded-[12px] mx-"
                >
                    <div class=" lg:w-fit md:w-fit lg:m-0 md:m-0 mx-auto">
                        <NeuphormistTabs :isDisabled="true" :items="modules" :value="activeModule.id" @onchange=" v => activeModuleId  = v.id " />
                    </div>
                    <KeepAlive>
                        <component
                            :is="activeModule.component"
                            :property="props.property"
                            :meta="activeModuleMeta"
                            :module-id="activeModule.id"
                        />
                    </KeepAlive>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
