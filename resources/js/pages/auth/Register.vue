<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import auth from '@/routes/auth';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <div class="mb-6">
            <Button
                as="a"
                :href="auth.google.redirect().url"
                variant="outline"
                class="flex w-full items-center justify-center gap-3"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 488 512"
                    aria-hidden="true"
                >
                    <path
                        fill="currentColor"
                        d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.4 64.9C277.7 80.7 150.2 116.6 150.2 256c0 94.4 76.3 171.1 171.8 171.1 109.6 0 150.6-78.6 157.2-119.5H322v-94.8h166z"
                    />
                </svg>
                Continue with Google
            </Button>
        </div>

        <Form
            v-bind="auth.register.store()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Full name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-start gap-3">
                        <Checkbox
                            id="terms"
                            name="terms"
                            :tabindex="5"
                            required
                            data-test="terms-checkbox"
                        />
                        <Label
                            for="terms"
                            class="text-sm leading-6 text-muted-foreground"
                        >
                            By signing up you agree to our
                            <TextLink
                                href="https://pixrup.com/terms"
                                class="mx-1 underline underline-offset-4"
                                target="_blank"
                                rel="noreferrer"
                            >
                                Terms of Service
                            </TextLink>
                            and
                            <TextLink
                                href="https://pixrup.com/privacy"
                                class="ml-1 underline underline-offset-4"
                                target="_blank"
                                rel="noreferrer"
                            >
                                Privacy Policy
                            </TextLink>
                            .
                        </Label>
                    </div>
                    <InputError :message="errors.terms" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="6"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="auth.login.show()"
                    class="underline underline-offset-4"
                    :tabindex="6"
                    >Log in</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
