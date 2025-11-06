<script setup lang="ts">
import { Alert, AlertDescription } from '@/components/ui/alert';
import { SidebarInset } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import { AlertTriangle, CheckCircle2, Info } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);

const flashStatus = computed(() => usePage().props.flash?.status ?? null);

const statusDetails = computed(() => {
    switch (flashStatus.value) {
        case 'already-verified':
            return {
                icon: CheckCircle2,
                variant: 'default' as const,
                message: 'Your email address is already verified.',
            };
        case 'verification-link-invalid':
            return {
                icon: AlertTriangle,
                variant: 'destructive' as const,
                message:
                    'The verification link is invalid or expired. Please request a new one.',
            };
        case 'verification-link-sent':
            return {
                icon: CheckCircle2,
                variant: 'default' as const,
                message: 'We just sent you a new verification link.',
            };
        case 'must-verify-email':
            return {
                icon: Info,
                variant: 'default' as const,
                message: 'Please verify your email to unlock all features.',
            };
        default:
            return null;
    }
});
</script>

<template>
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
        <Alert
            v-if="statusDetails"
            :variant="statusDetails.variant"
            class="mb-4"
        >
            <component :is="statusDetails.icon" />
            <AlertDescription>{{ statusDetails.message }}</AlertDescription>
        </Alert>
        <slot />
    </SidebarInset>
    <main
        v-else
        class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl"
        :class="className"
    >
        <Alert
            v-if="statusDetails"
            :variant="statusDetails.variant"
            class="mb-4"
        >
            <component :is="statusDetails.icon" />
            <AlertDescription>{{ statusDetails.message }}</AlertDescription>
        </Alert>
        <slot />
    </main>
</template>
