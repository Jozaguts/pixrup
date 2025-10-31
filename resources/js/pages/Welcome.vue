<script setup lang="ts">
import WelcomeBackground from '@/components/welcome/WelcomeBackground.vue';
import WelcomeFooter from '@/components/welcome/WelcomeFooter.vue';
import WelcomeNavbar from '@/components/welcome/WelcomeNavbar.vue';
import AddressSearch, {
    type AddressSelection,
} from '@/components/welcome/AddressSearch.vue';
import ContinueButtons from '@/components/welcome/ContinueButtons.vue';
import WelcomeGallery from '@/components/welcome/WelcomeGallery.vue';
import HeroSection from '@/components/welcome/HeroSection.vue';
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

    <div
        class="relative min-h-screen overflow-hidden px-4 text-slate-900 sm:px-6 lg:px-10"
    >
        <WelcomeBackground />

        <div class="relative z-10 mx-auto flex min-h-screen w-full flex-col">
            <header>
                <div
                    class="header-one opacity-0 rounded-full lp:!max-w-[1290px] xl:max-w-[1140px] lg:max-w-[960px] md:max-w-[720px] sm:max-w-[540px] min-[500px]:max-w-[450px] min-[425px]:max-w-[375px] max-w-[350px] mx-auto w-full fixed left-1/2 -translate-x-1/2 z-50 flex items-center justify-between px-2.5 xl:py-0 py-2.5 {=$class}"
                    data-ns-animate
                    data-direction="up"
                    data-offset="100"
                >
                    <div>
                        <a href="./index.html">
                            <span class="sr-only">Home</span>
                            <figure class="lg:max-w-[198px] lg:block hidden">
                                <img src="./images/shared/main-logo.svg" alt="NextSaaS" class="dark:invert" />
                            </figure>
                            <figure class="max-w-[44px] lg:hidden block">
                                <img src="./images/shared/logo.svg" alt="NextSaaS" class="w-full dark:hidden block" />
                                <img
                                    src="./images/shared/logo-dark.svg"
                                    alt="NextSaaS"
                                    class="w-full dark:block hidden"
                                />
                            </figure>
                        </a>
                    </div>
                    <nav class="hidden xl:flex items-center">
                        <ul class="flex items-center">
                            <li class="relative nav-item cursor-pointer py-2.5" data-menu="home-mega-menu">
                                <a href="#" class="nav-item-link {=$nav-item-class}">
                                    <span>Home</span>
                                    <span class="nav-arrow block origin-center transition-all duration-300 translate-y-px">
              <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-4"
              >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m19.5 8.25-7.5 7.5-7.5-7.5"
                />
              </svg>
            </span>
                                </a>
                                <Component src="src/components/shared/home-mega-menu.htm" class="{=$mega-menu-color}" />
                            </li>
                            <li class="relative nav-item cursor-pointer py-2.5" data-menu="pages-mega-menu">
                                <a href="#" class="nav-item-link {=$nav-item-class}">
                                    <span>Pages</span>
                                    <span class="nav-arrow block origin-center transition-all duration-300 translate-y-px">
              <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-4"
              >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m19.5 8.25-7.5 7.5-7.5-7.5"
                />
              </svg>
            </span>
                                </a>
                                <Component src="src/components/shared/pages-mega-menu.htm" class="{=$mega-menu-color}" />
                            </li>
                            <li class="relative nav-item cursor-pointer py-2.5" data-menu="about-menu">
                                <a href="#" class="nav-item-link {=$nav-item-class}">
                                    <span>About</span>
                                    <span class="nav-arrow block origin-center transition-all duration-300 translate-y-px">
              <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-4"
              >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m19.5 8.25-7.5 7.5-7.5-7.5"
                />
              </svg>
            </span>
                                </a>
                                <Component src="src/components/shared/about-menu.htm" class="{=$mega-menu-color}" />
                            </li>
                            <li class="relative nav-item cursor-pointer py-2.5" data-menu="services-menu">
                                <a href="#" class="nav-item-link {=$nav-item-class}">
                                    <span>Services</span>
                                    <span class="nav-arrow block origin-center transition-all duration-300 translate-y-px">
              <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-4"
              >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m19.5 8.25-7.5 7.5-7.5-7.5"
                />
              </svg>
            </span>
                                </a>
                                <Component src="src/components/shared/services-menu.htm" class="{=$mega-menu-color}" />
                            </li>
                            <li class="relative nav-item cursor-pointer py-2.5" data-menu="blog-menu">
                                <a href="#" class="nav-item-link {=$nav-item-class}">
                                    <span>Blog</span>
                                    <span class="nav-arrow block origin-center transition-all duration-300 translate-y-px">
              <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-4"
              >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m19.5 8.25-7.5 7.5-7.5-7.5"
                />
              </svg>
            </span>
                                </a>
                                <Component src="src/components/shared/blog-menu.htm" class="{=$mega-menu-color}" />
                            </li>
                            <li class="py-2.5">
                                <a
                                    href="./contact-us-page.html"
                                    class="flex items-center gap-1 px-4 py-2 border border-transparent hover:border-stroke-2 dark:hover:border-stroke-7 rounded-full text-tagline-1 font-normal text-secondary/60 hover:text-secondary transition-all duration-200 dark:text-accent/60 dark:hover:text-accent {=$nav-item-class}"
                                >
                                    Contact Us
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <div class="xl:flex hidden items-center justify-center">
                        <a href="./signup-page-01.html" class="btn btn-md {=$btn-class}">
                            <span>Get started</span>
                        </a>
                    </div>
                    <div class="xl:hidden block">
                        <button
                            class="nav-hamburger flex flex-col gap-[5px] size-12 bg-background-4 dark:bg-background-6 rounded-full items-center justify-center cursor-pointer"
                        >
                            <span class="sr-only">Menu</span>
                            <span class="block w-6 h-0.5 bg-stroke-9 dark:bg-stroke-1"></span>
                            <span class="block w-6 h-0.5 bg-stroke-9 dark:bg-stroke-1"></span>
                            <span class="block w-6 h-0.5 bg-stroke-9 dark:bg-stroke-1"></span>
                        </button>
                    </div>
                </div>
                <Component src="src/components/shared/mobile-menu.htm" />
            </header>
<!--            <WelcomeNavbar-->
<!--                :is-authenticated="isAuthenticated"-->
<!--                :can-register="props.canRegister"-->
<!--                :nav-items="navItems"-->
<!--                :primary-link="primaryLink"-->
<!--            />-->

            <main
                class="flex flex-1 flex-col items-center justify-center gap-10 py-2 text-center"
            >
                <HeroSection />
                <AddressSearch
                    v-model="addressQuery"
                    @place-selected="handlePlaceSelected"
                />
                <p class="text-sm ">
                    Get a quick PixrWorth Lite estimate before deciding where to continue.
                </p>
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
    </div>
</template>
