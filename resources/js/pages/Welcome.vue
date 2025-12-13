<script setup lang="ts">
import AddressSearch, {
    type AddressSelection,
} from '@/components/welcome/AddressSearch.vue';
import ContinueButtons from '@/components/welcome/ContinueButtons.vue';
import FloatingRobot from '@/components/welcome/FloatingRobot.vue';
import HeroSection from '@/components/welcome/HeroSection.vue';
import WelcomeBackground from '@/components/welcome/WelcomeBackground.vue';
import WelcomeFooter from '@/components/welcome/WelcomeFooter.vue';
import WelcomeGallery from '@/components/welcome/WelcomeGallery.vue';
import WorthPreviewModal, {
    type ComparableProperty,
} from '@/components/welcome/WorthPreviewModal.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { ServiceCard } from '@/components/template/services/types';
import GuestLayout from '@/layouts/GuestLayout.vue';
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
const navigateToWeb = () => {
    if (!selectedAddress.value) {
        return;
    }

    const destination = isAuthenticated.value ? '/dashboard' : '/register';
    const query = buildQueryFromSelection(selectedAddress.value);

    router.visit(`${destination}?${query}`);
};
const buildQueryFromSelection = (selection: AddressSelection) => {
    const query = new URLSearchParams({
        address: selection.formattedAddress,
        lat: selection.location.lat.toString(),
        lng: selection.location.lng.toString(),
        placeId: selection.placeId,
    });

    return query.toString();
};
const gradient = new URL('.images/gradient/gradient-22.png', import.meta.url).href;
</script>
<template>
    <GuestLayout :can-register="props.canRegister" title="Welcome">
        <template #main>
            <div class="mt-20 flex flex-1 flex-col items-center justify-center text-center">
                <FloatingRobot />
                <WelcomeBackground />
                <HeroSection />
                <div class="bg-white/90 neu-bg-surface-color w-full max-w-lg rounded-[12px] z-[100]">
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
                <section
                    class="pt-7 pb-14 md:pb-16 lg:pb-20 xl:pb-[100px]"
                    aria-label="Contact Information and Form"
                >
                    <div class="main-container">
                        <div class="space-y-[70px]">
                            <!-- heading  -->
                            <div class="max-w-[680px] mx-auto text-center space-y-3">
                                <h2 data-ns-animate data-delay="0.2">Reach out to our support team for help.</h2>
                                <p data-ns-animate data-delay="0.3">
                                    Whether you have a question, need technical assistance, or just want some guidance, our
                                    support team is here to help. We're available around the clock to provide quick and
                                    friendly support.
                                </p>
                            </div>

                            <div
                                class="flex lg:items-start flex-col justify-center items-center gap-10 lg:flex-row lg:gap-8 xl:gap-[70px]"
                            >
                                <!-- contact info cards  -->
                                <div data-ns-animate data-delay="0.4" class="flex flex-col gap-8 md:flex-row lg:flex-col">

                                    <!-- contact info two  -->
                                    <div
                                        class="card-item bg-secondary dark:bg-background-6 rounded-[20px] p-11 w-full md:max-w-[371px] text-center relative overflow-hidden"
                                    >
                                        <!-- bg overlay  -->
                                        <figure
                                            class="absolute size-[350px] select-none pointer-events-none overflow-hidden top-[-206px] left-[-36px] rotate-[62deg]"
                                        >
                                            <img
                                                src="images/gradient/gradient-17.png"
                                                alt="Decorative gradient overlay"
                                                class="size-full object-cover"
                                            />
                                        </figure>

                                        <div class="space-y-6">
                                            <figure class="size-10 overflow-hidden mx-auto">
                                                <img
                                                    src="images/icons/mail-open.svg"
                                                    alt="Email icon"
                                                    class="size-full object-cover"
                                                />
                                            </figure>

                                            <div class="space-y-2.5">
                                                <p class="text-heading-6 text-accent">Email Us</p>
                                                <p class="text-accent/60">
                                                    <a href="mailto:hello@nextsaaS.com">hello@pixrup.com</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>



                                </div>

                                <!-- contact form  -->
                                <div
                                    data-ns-animate
                                    data-delay="0.3"
                                    class="max-w-[847px] w-full mx-auto bg-white dark:bg-background-6 rounded-4xl p-6 md:p-8 lg:p-11"
                                >
                                    <form action="/index.html" method="POST" class="space-y-8">
                                        <!-- name and phone number  -->
                                        <div class="flex items-center flex-col md:flex-row gap-8 justify-between">
                                            <!--  name -->
                                            <div class="space-y-2 lg:max-w-[364px] w-full">
                                                <label
                                                    for="fullname"
                                                    class="block text-tagline-2 text-secondary dark:text-accent font-medium"
                                                >Your name</label
                                                >
                                                <input
                                                    type="text"
                                                    id="fullname"
                                                    name="fullname"
                                                    placeholder="Enter your name"
                                                    required
                                                    autocomplete="name"
                                                    class="w-full px-[18px] dark:focus-visible:border-stroke-4/20 dark:border-stroke-7 py-3 h-[48px] xl:h-[41px] rounded-full dark:bg-background-6 border border-stroke-3 bg-background-1 text-tagline-2 placeholder:text-secondary/60 focus:outline-none focus:border-secondary placeholder:text-tagline-2 dark:placeholder:text-accent/60 dark:text-accent placeholder:font-normal font-normal"
                                                />
                                            </div>

                                            <!-- number -->
                                            <div class="space-y-2 max-w-[364px] w-full">
                                                <label
                                                    for="number"
                                                    class="block text-tagline-2 text-secondary dark:text-accent font-medium"
                                                >Your number</label
                                                >
                                                <input
                                                    type="text"
                                                    id="number"
                                                    name="number"
                                                    placeholder="Enter your number"
                                                    required
                                                    autocomplete="tel"
                                                    class="w-full px-[18px] dark:focus-visible:border-stroke-4/20 dark:border-stroke-7 py-3 h-[48px] xl:h-[41px] rounded-full dark:bg-background-6 border border-stroke-3 bg-background-1 text-tagline-2 placeholder:text-secondary/60 focus:outline-none focus:border-secondary placeholder:text-tagline-2 dark:placeholder:text-accent/60 dark:text-accent placeholder:font-normal font-normal"
                                                />
                                            </div>
                                        </div>

                                        <!-- email  -->
                                        <div class="space-y-2">
                                            <label
                                                for="email"
                                                class="block text-tagline-2 text-secondary dark:text-accent font-medium"
                                            >Email address</label
                                            >
                                            <input
                                                type="email"
                                                id="email"
                                                name="email"
                                                placeholder="Enter your email"
                                                required
                                                autocomplete="email"
                                                class="w-full px-[18px] dark:focus-visible:border-stroke-4/20 dark:border-stroke-7 py-3 h-[48px] xl:h-[41px] rounded-full dark:bg-background-6 border border-stroke-3 bg-background-1 text-tagline-2 placeholder:text-secondary/60 focus:outline-none focus:border-secondary placeholder:text-tagline-2 dark:placeholder:text-accent/60 dark:text-accent placeholder:font-normal font-normal"
                                            />
                                        </div>

                                        <!-- subject  -->
                                        <div class="space-y-2">
                                            <label
                                                for="subject"
                                                class="block text-tagline-2 text-secondary dark:text-accent font-medium"
                                            >Subject</label
                                            >
                                            <input
                                                type="text"
                                                id="subject"
                                                name="subject"
                                                placeholder="Enter your subject"
                                                required
                                                class="w-full px-[18px] dark:focus-visible:border-stroke-4/20 dark:border-stroke-7 py-3 h-[48px] xl:h-[41px] rounded-full dark:bg-background-6 border border-stroke-3 bg-background-1 text-tagline-2 placeholder:text-secondary/60 focus:outline-none focus:border-secondary placeholder:text-tagline-2 dark:placeholder:text-accent/60 dark:text-accent placeholder:font-normal font-normal"
                                            />
                                        </div>

                                        <!-- message -->
                                        <div class="space-y-2">
                                            <label
                                                for="message"
                                                class="block text-tagline-2 text-secondary dark:text-accent font-medium"
                                            >Write message</label
                                            >
                                            <textarea
                                                id="message"
                                                name="message"
                                                rows="7"
                                                placeholder="Enter your messages"
                                                required
                                                class="w-full px-[18px] py-3 rounded-xl border dark:bg-background-6 dark:border-stroke-7 border-stroke-3 bg-background-1 text-tagline-2 placeholder:text-secondary/60 focus:outline-none focus:border-secondary dark:focus-visible:border-stroke-4/20 placeholder:text-tagline-2 dark:placeholder:text-accent/60 dark:text-accent placeholder:font-normal font-normal"
                                            ></textarea>
                                        </div>

                                        <!-- terms checkbox -->
                                        <fieldset class="flex items-center gap-2 mb-4">
                                            <label for="terms" class="flex items-center gap-x-3">
                                                <input id="terms" type="checkbox" class="sr-only peer" required />
                                                <span
                                                    class="size-4 rounded-full border border-stroke-3 dark:border-stroke-7 relative after:absolute after:size-2.5 after:bg-primary-500 after:rounded-full after:top-1/2 after:left-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:opacity-0 peer-checked:after:opacity-100 peer-checked:border-primary-500 cursor-pointer"
                                                ></span>
                                            </label>
                                            <label
                                                for="terms"
                                                class="text-tagline-3 cursor-pointer text-secondary/60 dark:text-accent/60"
                                            >
                                                I agree with the
                                                <a href="#" class="text-primary-500 underline text-tagline-3"
                                                >terms and conditions</a
                                                >
                                            </label>
                                        </fieldset>

                                        <!-- submit button -->
                                        <button
                                            type="submit"
                                            class="btn btn-md btn-secondary w-full hover:btn-primary dark:btn-accent before:content-none first-letter:uppercase"
                                        >
                                            Submit
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <WelcomeFooter />
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
    </GuestLayout>
</template>
