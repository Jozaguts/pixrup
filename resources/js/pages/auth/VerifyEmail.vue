<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import auth from '@/routes/auth';
import { send } from '@/routes/verification';
import { Form, Head } from '@inertiajs/vue3';
import {
    AlertTriangle,
    CheckCircle2,
    Info,
    LoaderCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    status?: string;
}>();

const statusDetails = computed(() => {
    switch (props.status) {
        case 'verification-link-sent':
            return {
                icon: CheckCircle2,
                variant: 'default' as const,
                message:
                    'We sent a fresh verification link to the email you provided. Please check your inbox.',
            };
        case 'already-verified':
            return {
                icon: CheckCircle2,
                variant: 'default' as const,
                message:
                    'Your email is already verified. You can continue to the dashboard.',
            };
        case 'verification-link-invalid':
            return {
                icon: AlertTriangle,
                variant: 'destructive' as const,
                message:
                    'The verification link is invalid or has expired. Request a new one below.',
            };
        case 'must-verify-email':
            return {
                icon: Info,
                variant: 'default' as const,
                message: 'Please verify your email address to access Pixrup.',
            };
        default:
            return null;
    }
});
</script>

<template>
    <AuthLayout
        title="Verify email"
        description="Please verify your email address by clicking on the link we just emailed to you."
    >
        <Head title="Email verification" />

        <Alert
            v-if="statusDetails"
            :variant="statusDetails.variant"
            class="mb-6 text-left"
        >
            <component :is="statusDetails.icon" />
            <AlertDescription>{{ statusDetails.message }}</AlertDescription>
        </Alert>

        <Form
            v-bind="send.form()"
            class="space-y-6 text-center"
            v-slot="{ processing }"
        >
            <Button :disabled="processing" variant="secondary">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Resend verification email
            </Button>

            <TextLink
                :href="auth.logout()"
                as="button"
                class="mx-auto block text-sm"
            >
                Log out
            </TextLink>
        </Form>
    </AuthLayout>
</template>
