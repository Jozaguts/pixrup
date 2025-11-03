<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { detectAppContext } from '@/lib/detectAppContext';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { AddressSelection } from './AddressSearch.vue';

const props = defineProps<{
    addressData: AddressSelection | null;
    isAuthenticated: boolean;
}>();

const emit = defineEmits<{
    (e: 'continue-web'): void;
    (e: 'continue-app'): void;
}>();

const hasSelection = computed(() => Boolean(props.addressData));

const buildQuery = () => {
    if (!props.addressData) {
        return '';
    }

    const query = new URLSearchParams({
        address: props.addressData.formattedAddress,
        lat: props.addressData.location.lat.toString(),
        lng: props.addressData.location.lng.toString(),
        placeId: props.addressData.placeId,
    });

    return query.toString();
};

const handleContinueOnWeb = () => {
    const destination = props.isAuthenticated ? '/dashboard' : '/register';
    const query = buildQuery();
    const href = query ? `${destination}?${query}` : destination;

    router.visit(href);
    emit('continue-web');
};

const handleContinueInApp = () => {
    if (typeof window === 'undefined') {
        return;
    }

    if (!props.addressData) {
        window.alert('Select an address to continue in the app.');
        return;
    }

    const { isCapacitor, isInPWA, isMobile } = detectAppContext();
    const query = buildQuery();
    const deepLink = `pixrup://property?${query}`;

    if (isCapacitor) {
        window.location.href = deepLink;
        emit('continue-app');
        return;
    }

    if (isInPWA) {
        window.location.href = `/dashboard?${query}`;
        emit('continue-app');
        return;
    }

    if (isMobile) {
        window.location.href = deepLink;

        window.setTimeout(() => {
            const userAgent = navigator.userAgent;
            const appStoreUrl =
                'https://apps.apple.com/app/pixrup-ai-property/id1234567890';
            const playStoreUrl =
                'https://play.google.com/store/apps/details?id=com.pixrup.app';

            if (/iPhone|iPad/.test(userAgent)) {
                window.location.href = appStoreUrl;
            } else {
                window.location.href = playStoreUrl;
            }
        }, 1500);

        emit('continue-app');
        return;
    }

    window.alert('Open Pixrup on your phone to continue in the app.');
};
</script>

<template>
    <div class="flex w-full flex-col gap-3 sm:flex-row sm:justify-center max-w-xl ">
        <Button
            size="lg"
            class="h-12 flex-1 min-w-[200px] is-pressed"
            @click="handleContinueOnWeb"
        >
            Continue on Web
        </Button>
        <Button
            variant="secondary"
            size="lg"
            class="h-12 flex-1 min-w-[200px]"
            :disabled="!hasSelection"
            @click="handleContinueInApp"
        >
            Continue in App
        </Button>
    </div>
</template>
