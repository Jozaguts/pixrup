<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import ThemeToggle from '@/components/ThemeToggle.vue';
import WelcomeNavbar from '@/components/welcome/WelcomeNavbar.vue';
import { computed } from 'vue';
const props = withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
const navItems = [
    { label: 'Features', href: '#features' },
    { label: 'Pricing', href: '#pricing' },
    { label: 'Use cases', href: '#use-cases' },
    { label: 'Blog', href: '#blog' },
];
const primaryLink = { label: 'Sign up', href: 'register' };
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap"
        />
    </Head>
    <div class="min-h-screen px-4 text-slate-900 sm:px-6 lg:px-10">
        <div class="relative z-10 mx-auto flex min-h-screen w-full flex-col">
            <WelcomeNavbar
                :is-authenticated="isAuthenticated"
                :can-register="props.canRegister"
                :nav-items="navItems"
                :primary-link="primaryLink"
            />

            <main>
                <slot name="main"></slot>
            </main>
        </div>
        <ThemeToggle />
    </div>
</template>
