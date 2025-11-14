<script setup lang="ts">
import NeuInput from '@/components/NeuInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import auth from '@/routes/auth';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, LockIcon, LucideMail } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase
        title="Log in to your account"
        description="Enter your email and password below to log in"
    >
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-2 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>
        <div class="">
            <Button
                as="a"
                :href="auth.google.redirect().url"
                variant="ghost"
                class="neu-button mb-2 flex w-full items-center justify-center p-3 py-6"
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
                Sign in using Google
            </Button>
            <div class="flex items-center">
                <div class="h-px flex-1 bg-gray-300"></div>
                <span class="px-3 text-sm text-gray-500"> Or </span>
                <div class="h-px flex-1 bg-gray-300"></div>
            </div>
        </div>

        <Form
            v-bind="auth.login.store()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6 p-6 py-3"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <NeuInput
                        label="Email address"
                        name="email"
                        :error="errors.email"
                        tabindex="1"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="Please enter your email"
                    >
                        <template #icon>
                            <LucideMail
                                class="h-5 w-5 text-muted-foreground/50 group-focus-within:text-slate-500"
                            />
                        </template>
                    </NeuInput>
                </div>

                <div class="grid gap-2">
                    <NeuInput
                        label="Password"
                        name="password"
                        :error="errors.password"
                        tabindex="2"
                        type="password"
                        required
                        placeholder="Enter your password"
                    >
                        <template #icon>
                            <LockIcon
                                class="h-5 w-5 text-muted-foreground/50 group-focus-within:text-slate-500"
                            />
                        </template>
                    </NeuInput>
                </div>

                <div class="flex items-center justify-between">
                    <Label
                        for="remember"
                        class="flex items-center space-x-3 font-kulim"
                    >
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="neu-button mt-2 w-full p-3 py-6"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Sign in
                </Button>
            </div>

            <div
                class="text-center text-sm text-muted-foreground"
                v-if="canRegister"
            >
                Don't have an account?
                <TextLink :href="auth.register.show()" :tabindex="5"
                    >Sign up</TextLink
                >
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Dont have an account?
                <TextLink
                    :href="auth.register.show()"
                    class="underline underline-offset-4"
                    :tabindex="6"
                    >Sign up</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
