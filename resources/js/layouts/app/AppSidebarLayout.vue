<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType, DashboardPageProps, GlowUpJobPayload } from '@/types';
import { emitRealtimeEvent } from '@/lib/realtimeEvents';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import ToastAlert from "@/components/shared/ToastAlert.vue";
import {LimitExceededToastProps as ToastProps} from "@/lib/shared/LimitExceededToastProps";


interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
const page = usePage<DashboardPageProps & { property?: { id?: number | string } }>();
const successToastStorageKey = 'pixrup:new-property-toast';
const propertyToastMessage = ref('');
const flashStatus = computed(() => page.props.flash?.status ?? null);
const limitExceededStatus = computed(() => page.props.flash?.limitExceeded ?? false);
const propertyId = computed(() => page.props.property?.id ?? null);
const glowupChannelName = ref<string | null>(null);
let glowupSubscription: ReturnType<NonNullable<typeof window.Echo>['private']> | null = null;

onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }
    removeFlashMessageFromStorage();
});
const removeFlashMessageFromStorage = () => {
    const stored = window.sessionStorage.getItem(successToastStorageKey);
    if (stored) {
        propertyToastMessage.value = stored;
        window.sessionStorage.removeItem(successToastStorageKey);
    }
};

watch(
    flashStatus,
    (status) => {
        switch (status) {
            case 'property-created':
                propertyToastMessage.value = 'Property successfully created 🎉';
                break;
            case 'glowup-attached':
                propertyToastMessage.value = 'Glow up successfully attached 🎉';
                break;
        }
    },
    { immediate: true },
);

const dismissPropertyToast = () => {
    propertyToastMessage.value = '';
    removeFlashMessageFromStorage();
};

const leaveGlowupChannel = () => {
    if (glowupChannelName.value) {
        window.Echo?.leave(glowupChannelName.value);
        glowupChannelName.value = null;
        glowupSubscription = null;
    }
};

const subscribeGlowupChannel = (id: number | string) => {
    if (typeof window === 'undefined' || !window.Echo) {
        return;
    }

    const nextChannel = `glowup.jobs.${id}`;
    if (glowupChannelName.value === nextChannel) {
        return;
    }

    leaveGlowupChannel();
    glowupChannelName.value = nextChannel;
    glowupSubscription = window.Echo.private(nextChannel);
    glowupSubscription.listen('.GlowUpJobUpdated', (event: { job?: GlowUpJobPayload }) => {
        if (event?.job) {
            emitRealtimeEvent('glowup:job', event.job);
        }
    });
};

watch(
    propertyId,
    (id) => {
        if (!id) {
            leaveGlowupChannel();
            return;
        }
        subscribeGlowupChannel(id);
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    leaveGlowupChannel();
});
</script>

<template>
    <AppShell variant="sidebar">
        <ToastAlert
            :title="ToastProps.title"
            :msg="ToastProps.description"
            :type="ToastProps.type as 'success'"
            :visible="limitExceededStatus"
            redirect-url="/plan/upgrade"
        />
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <Transition name="fade-slide">
                <div
                    v-if="propertyToastMessage"
                    class="mb-4 flex flex-col gap-3 neu-surface rounded-[24px] px-5 py-4 text-sm text-accent md:flex-row md:items-center md:justify-between"
                >
                    <div class="flex items-center gap-3 text-accent">
                        <CheckCircle2 class="size-5 text-[#1fbf75]" />
                        <span class="font-semibold">
                            {{ propertyToastMessage }}
                        </span>
                    </div>
                    <button
                        type="button"
                        class="neu-btn inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-xs font-semibold text-accent"
                        @click="dismissPropertyToast"
                    >
                        <X class="size-4" />
                        Dismiss
                    </button>
                </div>
            </Transition>
            <slot />
        </AppContent>
    </AppShell>
</template>
