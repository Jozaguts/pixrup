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
                class="flex w-full items-center justify-center gap-3 p-6"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="32"
                    height="32"
                    viewBox="0 0 16 16"
                >
                    <!-- Icon from Material Icon Theme by Material Extensions - https://github.com/material-extensions/vscode-material-icon-theme/blob/main/LICENSE -->
                    <g fill="none" fill-rule="evenodd" clip-rule="evenodd">
                        <path
                            fill="#F44336"
                            d="M7.209 1.061c.725-.081 1.154-.081 1.933 0a6.57 6.57 0 0 1 3.65 1.82a100 100 0 0 0-1.986 1.93q-1.876-1.59-4.188-.734q-1.696.78-2.362 2.528a78 78 0 0 1-2.148-1.658a.26.26 0 0 0-.16-.027q1.683-3.245 5.26-3.86"
                            opacity=".987"
                        />
                        <path
                            fill="#FFC107"
                            d="M1.946 4.92q.085-.013.161.027a78 78 0 0 0 2.148 1.658A7.6 7.6 0 0 0 4.04 7.99q.037.678.215 1.331L2 11.116Q.527 8.038 1.946 4.92"
                            opacity=".997"
                        />
                        <path
                            fill="#448AFF"
                            d="M12.685 13.29a26 26 0 0 0-2.202-1.74q1.15-.812 1.396-2.228H8.122V6.713q3.25-.027 6.497.055q.616 3.345-1.423 6.032a7 7 0 0 1-.51.49"
                            opacity=".999"
                        />
                        <path
                            fill="#43A047"
                            d="M4.255 9.322q1.23 3.057 4.51 2.854a3.94 3.94 0 0 0 1.718-.626q1.148.812 2.202 1.74a6.62 6.62 0 0 1-4.027 1.684a6.4 6.4 0 0 1-1.02 0Q3.82 14.524 2 11.116z"
                            opacity=".993"
                        />
                    </g>
                </svg>
                Continue with Google
            </Button>
        </div>

        <Form
            v-bind="auth.register.store()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6 p-6 shadow-neu-in"
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
                        class="is-pressed"
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
                        class="is-pressed"
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
                        class="is-pressed"
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
                        class="is-pressed"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <Checkbox
                            id="terms"
                            name="terms"
                            :tabindex="5"
                            required
                            class="neu-checkbox"
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
