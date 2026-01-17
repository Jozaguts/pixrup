<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import ThemeToggle from '@/components/ThemeToggle.vue';
import Footer from '@/components/shared/Footer.vue';
import WelcomeNavbar from '@/components/welcome/WelcomeNavbar.vue';
import { computed } from 'vue';
import { useLandingTranslations } from '@/composables/useLandingTranslations';

interface NavbarLabels {
    home: string;
    toggleNavigation: string;
    closeMenu: string;
    dashboard: string;
    getStarted: string;
    logIn: string;
}
const props = withDefaults(
    defineProps<{
        canRegister: boolean;
        title: string;
    }>(),
    {
        canRegister: true,
        title: 'Welcome',
    },
);
const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
const landingTranslations = useLandingTranslations();
const navTranslations = computed(() => landingTranslations.value.nav ?? {});
const ctaTranslations = computed(() => navTranslations.value.cta ?? {});
const srTranslations = computed(() => navTranslations.value.sr ?? {});

const navItems = computed(() => [
    { label: navTranslations.value.features ?? 'Features', href: 'features' },
    { label: navTranslations.value.use_cases ?? 'Use cases', href: 'use-cases' },
    { label: navTranslations.value.pricing ?? 'Pricing', href: 'pricing' },
    { label: navTranslations.value.blog ?? 'Blog', href: 'blog' },
    { label: navTranslations.value.support ?? 'Support', href: 'support' },
]);

const primaryLink = computed(() => ({
    label: ctaTranslations.value.sign_up ?? 'Sign up',
    href: 'register',
}));

const navbarLabels = computed<NavbarLabels>(() => ({
    home: srTranslations.value.home ?? 'Home',
    toggleNavigation: srTranslations.value.toggle_navigation ?? 'Toggle navigation',
    closeMenu: srTranslations.value.close_menu ?? 'Close menu',
    dashboard: ctaTranslations.value.dashboard ?? 'Dashboard',
    getStarted: ctaTranslations.value.get_started ?? 'Get started',
    logIn: ctaTranslations.value.log_in ?? 'Log in',
}));
</script>

<template>
    <Head :title="props.title">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap"
        />
    </Head>
    <div class="min-h-screen px-4 text-slate-900 sm:px-2 lg:px-4">
        <div class="relative z-10 mx-auto flex min-h-screen w-full flex-col">
            <WelcomeNavbar
                :is-authenticated="isAuthenticated"
                :can-register="props.canRegister"
                :nav-items="navItems"
                :primary-link="primaryLink"
                :labels="navbarLabels"
            />
            <main>
                <slot name="main"></slot>
            </main>
            <Footer />
        </div>

        <ThemeToggle />
    </div>
</template>
