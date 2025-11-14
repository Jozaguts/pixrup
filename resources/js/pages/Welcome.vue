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

const primaryLink = { label: 'Sign up', href: 'register' };
const listings = [
    {
        id: 1,
        title: 'Pix Worth',
        image: 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=400&q=80',
        headline: 'Real value, instantly.',
        description:
            'Get accurate property estimates with real local comparables ready for your report.',
    },
    {
        id: 2,
        title: 'Pix Transform',
        image: 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=400&q=80',
        headline: 'AI makeovers that sell.',
        description:
            'Upload a photo, try different styles, and generate stunning “before & after” renders in seconds.',
    },
    {
        id: 3,
        title: 'Pix Treasure',
        image: 'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=400&q=80',
        headline: 'Find hidden opportunities.',
        description:
            'Explore $/sqft, recent sales, and local trends on a map to spot undervalued properties fast.',
    },
    {
        id: 4,
        title: 'Pix Closer',
        image: 'https://images.unsplash.com/photo-1549187774-b4e9b0445b41?auto=format&fit=crop&w=400&q=80',
        headline: 'Close deals faster, together.',
        description:
            'Collaborate with teammates in real time — comments, mentions, and shared files in one place.',
    },
    {
        id: 5,
        title: 'Pix AiVision',
        image: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=400&q=80',
        headline: 'Immersive 3D tours.',
        description:
            'Embed Matterport links seamlessly and let your clients explore properties in full detail.',
    },
    {
        id: 6,
        title: 'Pix Seal',
        image: 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=400&q=80',
        headline: 'Branded PDF reports.',
        description:
            'Generate sleek, professional PDFs with your logo and colors — ready to share or print instantly.',
    },
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

const handleAppraiseFullProperty = () => {
    isWorthModalOpen.value = false;
    navigateToWeb();
};
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

    <div class="relative min-h-screen px-4 text-slate-900 sm:px-6 lg:px-10">
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
                class="flex flex-1 flex-col items-center justify-center gap-10 pt-32 text-center"
            >
                <HeroSection />
                <AddressSearch
                    v-model="addressQuery"
                    @place-selected="handlePlaceSelected"
                />
                <ContinueButtons
                    :address-data="selectedAddress"
                    :is-authenticated="isAuthenticated"
                    @continue-web="isWorthModalOpen = false"
                    @continue-app="isWorthModalOpen = false"
                />
                <WelcomeGallery :listings="listings" />
            </main>

            <WelcomeFooter />
        </div>

        <WorthPreviewModal
            :open="isWorthModalOpen && Boolean(selectedAddress)"
            :address="selectedAddress?.formattedAddress"
            :estimated-value="estimatedValue"
            :comps="comparableProperties"
            @update:open="(value) => (isWorthModalOpen = value)"
            @appraise="handleAppraiseFullProperty"
        />
        <ThemeToggle />
    </div>
</template>
