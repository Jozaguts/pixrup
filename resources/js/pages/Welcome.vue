<script setup lang="ts">
import ThemeToggle from '@/components/ThemeToggle.vue';
import AddressSearch, {
    type AddressSelection,
} from '@/components/welcome/AddressSearch.vue';
import ContinueButtons from '@/components/welcome/ContinueButtons.vue';
import FloatingRobot from '@/components/welcome/FloatingRobot.vue';
import HeroSection from '@/components/welcome/HeroSection.vue';
import WelcomeBackground from '@/components/welcome/WelcomeBackground.vue';
import WelcomeFooter from '@/components/welcome/WelcomeFooter.vue';
import WelcomeGallery from '@/components/welcome/WelcomeGallery.vue';
import WelcomeNavbar from '@/components/welcome/WelcomeNavbar.vue';
import WorthPreviewModal, {
    type ComparableProperty,
} from '@/components/welcome/WorthPreviewModal.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { ServiceCard } from '@/components/template/services/types';
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

const listings: ServiceCard [] = [
    {
        title: 'Pix Worth',
        details: 'Get accurate property estimates with real local comparables ready for your report.',
        cta: 'read more',
        link:'/services',
        shape:'ns-shape-47'

    },
    {
        title: 'Pix Transform',
        details:
            'Upload a photo, try different styles, and generate stunning “before & after” renders in seconds.',
        cta: 'read more',
        link:'/services',
        shape:'ns-shape-48'
    },
    {
        title: 'Pix Treasure',
        details:
            'Explore square footage, recent sales, and local trends on a map to spot undervalued properties fast.',
        cta: 'read more',
        link:'/services',
        shape:'ns-shape-49'
    },
    {

        title: 'Pix Closer',
        cta: 'read more',
        link:'/services',
        details:
            'Collaborate with teammates in real time — comments, mentions, and shared files in one place.',
        shape:'ns-shape-50'
    },
    // {
    //     title: 'Pix AiVision',
    //     details:
    //         'Embed Matterport links seamlessly and let your clients explore properties in full detail.',
    //     cta: 'read more',
    //     link:'/services',
    // },
    // {
    //
    //     title: 'Pix Seal',
    //     details:
    //         'Generate sleek, professional PDFs with your logo and colors — ready to share or print instantly.',
    //     cta: 'read more',
    //     link:'/services',
    // },
];

const addressQuery = ref('');
const selectedAddress = ref<AddressSelection | null>(null);
const estimatedValue = ref<number>();
const comparableProperties = ref<ComparableProperty[]>([]);
const isWorthModalOpen = ref(false);

const buildQueryFromSelection = (selection: AddressSelection) => {
    const query = new URLSearchParams({
        address: selection.formattedAddress,
        lat: selection.location.lat.toString(),
        lng: selection.location.lng.toString(),
        placeId: selection.placeId,
    });

    return query.toString();
};

const navigateToWeb = () => {
    if (!selectedAddress.value) {
        return;
    }

    const destination = isAuthenticated.value ? '/dashboard' : '/register';
    const query = buildQueryFromSelection(selectedAddress.value);

    router.visit(`${destination}?${query}`);
};

const createMockPreview = (selection: AddressSelection) => {
    const rawEstimate =
        Math.round(
            (Math.abs(selection.location.lat) +
                Math.abs(selection.location.lng)) *
                14000,
        ) + 225000;

    const estimate = Math.min(Math.max(rawEstimate, 185000), 1750000);

    const [streetSegment = '', citySegment = '', stateSegment = ''] =
        selection.formattedAddress.split(',');

    const trimmedStreet = streetSegment.trim();
    const cityState = [citySegment?.trim(), stateSegment?.trim()]
        .filter(Boolean)
        .join(', ');

    const numericPortion = Number.parseInt(trimmedStreet, 10);
    const streetOnly = trimmedStreet.replace(/^\d+\s*/, '').trim();

    const buildComparableStreet = (delta: number, fallback: string) => {
        if (Number.isNaN(numericPortion) || !streetOnly) {
            return `${trimmedStreet} ${fallback}`.trim();
        }

        return `${numericPortion + delta} ${streetOnly}`;
    };

    const comps: ComparableProperty[] = [
        {
            id: `${selection.placeId}-comp-a`,
            address: `${buildComparableStreet(4, 'Unit A')}${
                cityState ? ` · ${cityState}` : ''
            }`,
            value: Math.round(estimate * 0.97),
        },
        {
            id: `${selection.placeId}-comp-b`,
            address: `${buildComparableStreet(-3, 'Unit B')}${
                cityState ? ` · ${cityState}` : ''
            }`,
            value: Math.round(estimate * 1.02),
        },
    ];

    return { estimate, comps };
};

const handlePlaceSelected = (selection: AddressSelection) => {
    addressQuery.value = selection.formattedAddress;
    selectedAddress.value = selection;

    const preview = createMockPreview(selection);
    estimatedValue.value = preview.estimate;
    comparableProperties.value = preview.comps;

    isWorthModalOpen.value = true;
};
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
        <FloatingRobot />
        <WelcomeBackground />

        <div class="relative z-10 mx-auto flex min-h-screen w-full flex-col">
            <WelcomeNavbar
                :is-authenticated="isAuthenticated"
                :can-register="props.canRegister"
                :nav-items="navItems"
                :primary-link="primaryLink"
            />

            <main
                class="flex flex-1 flex-col items-center justify-center text-center mt-20"
            >
                <HeroSection />
                <div class="bg-white/90 neu-bg-surface-color w-full max-w-lg rounded-[12px]">
                    <AddressSearch
                        v-model="addressQuery"
                        @place-selected="handlePlaceSelected"
                    />
                </div>
                <ContinueButtons
                    :address-data="selectedAddress"
                    :is-authenticated="isAuthenticated"
                    @continue-web="isWorthModalOpen = false"
                    @continue-app="isWorthModalOpen = false"
                />
                <WelcomeGallery :listings="listings" />
                <WelcomeFooter />
            </main>
        </div>
<!--        <WorthPreviewModal-->
<!--            :open="isWorthModalOpen && Boolean(selectedAddress)"-->
<!--            :address="selectedAddress?.formattedAddress"-->
<!--            :estimated-value="estimatedValue"-->
<!--            :comps="comparableProperties"-->
<!--            @update:open="(value) => (isWorthModalOpen = value)"-->
<!--            @appraise="handleAppraiseFullProperty"-->
<!--        />-->
        <ThemeToggle />
    </div>
</template>
