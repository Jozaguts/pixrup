<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType, DashboardPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, X } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import ToastAlert from "@/components/shared/ToastAlert.vue";
import {LimitExceededToastProps as ToastProps} from "@/lib/shared/LimitExceededToastProps";
import Swal from 'sweetalert2';


interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
const page = usePage<DashboardPageProps>();
const successToastStorageKey = 'pixrup:new-property-toast';
const propertyToastMessage = ref('');
const flashStatus = computed(() => page.props.flash?.status ?? null);
const limitExceededStatus = computed(() => page.props.flash?.limitExceeded ?? false);

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
</script>

<template>
    <AppShell variant="sidebar">
        <ToastAlert
            :title="ToastProps.title"
            :msg="ToastProps.description"
            :type="ToastProps.type as 'success'"
            :visible="limitExceededStatus"
            redirect-url="/settings/billing"
        />
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <Transition name="fade-slide">
                <div
                    v-if="propertyToastMessage"
                    class="mb-4 flex flex-col gap-3 neu-surface rounded-[24px] px-5 py-4 text-sm text-[#1f2933] md:flex-row md:items-center md:justify-between"
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
            <slot />
        </AppContent>
    </AppShell>
</template>
