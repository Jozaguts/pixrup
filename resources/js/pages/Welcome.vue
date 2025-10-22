<script setup lang="ts">
import WelcomeBackground from '@/components/welcome/WelcomeBackground.vue';
import WelcomeFooter from '@/components/welcome/WelcomeFooter.vue';
import WelcomeGallery from '@/components/welcome/WelcomeGallery.vue';
import WelcomeHero from '@/components/welcome/WelcomeHero.vue';
import WelcomeNavbar from '@/components/welcome/WelcomeNavbar.vue';
import { Head, usePage } from '@inertiajs/vue3';
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
    { label: 'Resources', href: '#resources' },
];

const primaryLink = { label: 'Send files', href: '#send' };

const listings = [
    {
        id: 1,
        title: 'Pix Worth',
        image:
            'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=400&q=80',
    },
    {
        id: 2,
        title: 'Pix Transform',
        image:
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=400&q=80',
    },
    {
        id: 3,
        title: 'Pix Treasure',
        image:
            'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=400&q=80',
    },
    {
        id: 4,
        title: 'Pix Closer',
        image:
            'https://images.unsplash.com/photo-1549187774-b4e9b0445b41?auto=format&fit=crop&w=400&q=80',
    },
    {
        id: 5,
        title: 'Pix AiVision',
        image:
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=400&q=80',
    },
    {
        id: 6,
        title: 'Pix Seal',
        image:
            'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=400&q=80',
    },
];
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap"
        />
    </Head>

    <div
        class="relative min-h-screen overflow-hidden bg-[#f9f7ff] px-4 font-['Inter',sans-serif] text-slate-900 sm:px-6 lg:px-10"
    >
        <WelcomeBackground />

        <div class="relative z-10 mx-auto flex min-h-screen w-full flex-col">
            <WelcomeNavbar
                :is-authenticated="isAuthenticated"
                :can-register="props.canRegister"
                :nav-items="navItems"
                :primary-link="primaryLink"
            />

            <main class="flex flex-1 flex-col items-center justify-center gap-10">
                <WelcomeHero />
                <WelcomeGallery :listings="listings" />
            </main>

            <WelcomeFooter />
        </div>
    </div>
</template>
