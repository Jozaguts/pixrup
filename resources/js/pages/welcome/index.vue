<script setup lang="ts">
import AddressSearch, { type AddressSelection } from '@/components/welcome/AddressSearch.vue';
import ContinueButtons from '@/components/welcome/ContinueButtons.vue';
import FloatingRobot from '@/components/welcome/FloatingRobot.vue';
import HeroSection from '@/components/welcome/HeroSection.vue';
import WelcomeBackground from '@/components/welcome/WelcomeBackground.vue';
import WelcomeFooter from '@/components/welcome/WelcomeFooter.vue';
import WelcomeGallery from '@/components/welcome/WelcomeGallery.vue';
import WorthPreviewModal, { type ComparableProperty } from '@/components/welcome/WorthPreviewModal.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import type { ServiceCard } from '@/components/template/services/types';
import GuestLayout from '@/layouts/GuestLayout.vue';
import UseCaseContainer from '@/pages/welcome/use-cases/UseCaseContainer.vue';
import PricingContainer from '@/pages/welcome/pricing/PricingContainer.vue';
import featuresRoutes from '@/routes/features/index';
import { useLandingTranslations } from '@/composables/useLandingTranslations';
const props = withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
const featureState = ref<{ loading: boolean; data: ServiceCard[] | null | [] }>({
    loading: false,
    data: [],
});

const landingTranslations = useLandingTranslations();
const pageTranslations = computed(() => landingTranslations.value.page ?? {});
const supportTranslations = computed(() => landingTranslations.value.support ?? {});
const supportForm = computed(() => supportTranslations.value.form ?? {});
const supportTerms = computed(() => supportForm.value.terms ?? {});
const worthPreviewTranslations = computed(
    () => landingTranslations.value.worth_preview ?? {},
);

const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));

const addressQuery = ref('');
const selectedAddress = ref<AddressSelection | null>(null);
const estimatedValue = ref<number>();
const comparableProperties = ref<ComparableProperty[]>([]);
const isWorthModalOpen = ref(false);

const createMockPreview = (selection: AddressSelection) => {
    const rawEstimate =
        Math.round((Math.abs(selection.location.lat) + Math.abs(selection.location.lng)) * 14000) + 225000;

    const estimate = Math.min(Math.max(rawEstimate, 185000), 1750000);

    const [streetSegment = '', citySegment = '', stateSegment = ''] = selection.formattedAddress.split(',');

    const trimmedStreet = streetSegment.trim();
    const cityState = [citySegment?.trim(), stateSegment?.trim()].filter(Boolean).join(', ');

    const numericPortion = Number.parseInt(trimmedStreet, 10);
    const streetOnly = trimmedStreet.replace(/^\d+\s*/, '').trim();

    const buildComparableStreet = (delta: number, fallback: string) => {
        if (Number.isNaN(numericPortion) || !streetOnly) {
            return `${trimmedStreet} ${fallback}`.trim();
        }

        return `${numericPortion + delta} ${streetOnly}`;
    };

    const unitA =
        worthPreviewTranslations.value.comparable_unit_a ?? 'Unit A';
    const unitB =
        worthPreviewTranslations.value.comparable_unit_b ?? 'Unit B';

    const comps: ComparableProperty[] = [
        {
            id: `${selection.placeId}-comp-a`,
            address: `${buildComparableStreet(4, unitA)}${cityState ? ` · ${cityState}` : ''}`,
            value: Math.round(estimate * 0.97),
        },
        {
            id: `${selection.placeId}-comp-b`,
            address: `${buildComparableStreet(-3, unitB)}${cityState ? ` · ${cityState}` : ''}`,
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
onMounted(async () => {
    try {
        featureState.value.loading = true;
        const res = await fetch(featuresRoutes.index().url);
        featureState.value.data = await res.json();
    } catch (e) {
        console.error(e);
    } finally {
        featureState.value.loading = false;
    }
});
</script>
<template>
    <GuestLayout :can-register="props.canRegister" :title="pageTranslations.title ?? 'Welcome'">
        <template #main>
            <div class="mt-20 flex flex-1 flex-col items-center justify-center text-center">
                <FloatingRobot />
                <WelcomeBackground />
                <HeroSection />
                <div class="neu-bg-surface-color z-[100] mt-15 w-full max-w-lg rounded-[12px] bg-white/90">
                    <AddressSearch v-model="addressQuery" @place-selected="handlePlaceSelected" />
                </div>
                <ContinueButtons
                    :address-data="selectedAddress"
                    :is-authenticated="isAuthenticated"
                    @continue-web="isWorthModalOpen = false"
                    @continue-app="isWorthModalOpen = false"
                />
                <div>
                    <p class="text-primary">
                        {{
                            pageTranslations.intro ??
                            'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deserunt, doloribus ducimus ipsum iusto maxime nisi officia pariatur quam vel voluptates? Cumque pariatur, soluta! Ab adipisci, alias asperiores aspernatur consequuntur culpa deserunt dicta, ea eaque eius facilis incidunt maiores quis quisquam repellat suscipit vitae voluptas? Animi architecto delectus deleniti distinctio eaque, eius enim error fugit, illo in ipsa itaque iure labore libero minima minus, odit placeat quae quasi qui quis ratione reprehenderit sed ullam unde vel veniam. Aliquam exercitationem id nisi perferendis voluptates. Aut delectus eius expedita iure maxime minima nihil non officiis provident quam quo sint unde, vel veniam voluptate.'
                        }}
                    </p>
                </div>

                <WelcomeGallery :listings="featureState.data" v-if="!featureState.loading" />
                <UseCaseContainer />
                <PricingContainer />
                <section
                    id="support"
                    class="pt-7 pb-14 md:pb-16 lg:pb-20 xl:pb-[100px]"
                    :aria-label="supportTranslations.aria_label ?? 'Contact Information and Form'"
                >
                    <div class="main-container">
                        <div class="space-y-[70px]">
                            <!-- heading  -->
                            <div class="mx-auto max-w-[780px] space-y-3 text-center">
                                <span data-ns-animate data-delay="0.2" class="mb-5 badge badge-primary">
                                    {{ supportTranslations.badge ?? 'Support' }}
                                </span>
                                <h2 data-ns-animate data-delay="0.2">
                                    {{
                                        supportTranslations.title ??
                                        'Reach out to our support team.'
                                    }}
                                </h2>
                                <p data-ns-animate data-delay="0.3">
                                    {{
                                        supportTranslations.description ??
                                        'Whether you have a question, need technical assistance, or just want some guidance, our support team is here to help. We\'re available around the clock to provide quick and friendly support.'
                                    }}
                                </p>
                            </div>
                            <div
                                class="flex flex-col items-center justify-center gap-10 lg:flex-row lg:items-start lg:gap-8 xl:gap-[70px]"
                            >
                                <!-- contact info cards  -->

                                <!-- contact form  -->
                                <div
                                    data-ns-animate
                                    data-delay="0.3"
                                    class="mx-auto w-full max-w-[847px] rounded-4xl bg-white p-4 md:p-8 lg:p-11 dark:bg-background-6"
                                >
                                    <form action="/index.html" method="POST" class="space-y-8">
                                        <!-- name and phone number  -->
                                        <div class="flex flex-col items-center justify-between gap-8 md:flex-row">
                                            <!--  name -->
                                            <div class="w-full space-y-2 lg:max-w-[364px]">
                                                <label
                                                    for="fullname"
                                                    class="block text-tagline-2 font-medium text-secondary dark:text-accent"
                                                    >{{ supportForm.name?.label ?? 'Your name' }}</label
                                                >
                                                <input
                                                    type="text"
                                                    id="fullname"
                                                    name="fullname"
                                                    :placeholder="supportForm.name?.placeholder ?? 'Enter your name'"
                                                    required
                                                    autocomplete="name"
                                                    class="h-[48px] w-full rounded-full border border-stroke-3 bg-background-1 px-[18px] py-3 text-tagline-2 font-normal placeholder:text-tagline-2 placeholder:font-normal placeholder:text-secondary/60 focus:border-secondary focus:outline-none xl:h-[41px] dark:border-stroke-7 dark:bg-background-6 dark:text-accent dark:placeholder:text-accent/60 dark:focus-visible:border-stroke-4/20"
                                                />
                                            </div>

                                            <!-- number -->
                                            <div class="w-full max-w-[364px] space-y-2">
                                                <label
                                                    for="number"
                                                    class="block text-tagline-2 font-medium text-secondary dark:text-accent"
                                                    >{{ supportForm.phone?.label ?? 'Your number' }}</label
                                                >
                                                <input
                                                    type="text"
                                                    id="number"
                                                    name="number"
                                                    :placeholder="supportForm.phone?.placeholder ?? 'Enter your number'"
                                                    required
                                                    autocomplete="tel"
                                                    class="h-[48px] w-full rounded-full border border-stroke-3 bg-background-1 px-[18px] py-3 text-tagline-2 font-normal placeholder:text-tagline-2 placeholder:font-normal placeholder:text-secondary/60 focus:border-secondary focus:outline-none xl:h-[41px] dark:border-stroke-7 dark:bg-background-6 dark:text-accent dark:placeholder:text-accent/60 dark:focus-visible:border-stroke-4/20"
                                                />
                                            </div>
                                        </div>

                                        <!-- email  -->
                                        <div class="space-y-2">
                                            <label
                                                for="email"
                                                class="block text-tagline-2 font-medium text-secondary dark:text-accent"
                                                >{{ supportForm.email?.label ?? 'Email address' }}</label
                                            >
                                            <input
                                                type="email"
                                                id="email"
                                                name="email"
                                                :placeholder="supportForm.email?.placeholder ?? 'Enter your email'"
                                                required
                                                autocomplete="email"
                                                class="h-[48px] w-full rounded-full border border-stroke-3 bg-background-1 px-[18px] py-3 text-tagline-2 font-normal placeholder:text-tagline-2 placeholder:font-normal placeholder:text-secondary/60 focus:border-secondary focus:outline-none xl:h-[41px] dark:border-stroke-7 dark:bg-background-6 dark:text-accent dark:placeholder:text-accent/60 dark:focus-visible:border-stroke-4/20"
                                            />
                                        </div>

                                        <!-- subject  -->
                                        <div class="space-y-2">
                                            <label
                                                for="subject"
                                                class="block text-tagline-2 font-medium text-secondary dark:text-accent"
                                                >{{ supportForm.subject?.label ?? 'Subject' }}</label
                                            >
                                            <input
                                                type="text"
                                                id="subject"
                                                name="subject"
                                                :placeholder="supportForm.subject?.placeholder ?? 'Enter your subject'"
                                                required
                                                class="h-[48px] w-full rounded-full border border-stroke-3 bg-background-1 px-[18px] py-3 text-tagline-2 font-normal placeholder:text-tagline-2 placeholder:font-normal placeholder:text-secondary/60 focus:border-secondary focus:outline-none xl:h-[41px] dark:border-stroke-7 dark:bg-background-6 dark:text-accent dark:placeholder:text-accent/60 dark:focus-visible:border-stroke-4/20"
                                            />
                                        </div>

                                        <!-- message -->
                                        <div class="space-y-2">
                                            <label
                                                for="message"
                                                class="block text-tagline-2 font-medium text-secondary dark:text-accent"
                                                >{{ supportForm.message?.label ?? 'Write message' }}</label
                                            >
                                            <textarea
                                                id="message"
                                                name="message"
                                                rows="7"
                                                :placeholder="supportForm.message?.placeholder ?? 'Enter your messages'"
                                                required
                                                class="w-full rounded-xl border border-stroke-3 bg-background-1 px-[18px] py-3 text-tagline-2 font-normal placeholder:text-tagline-2 placeholder:font-normal placeholder:text-secondary/60 focus:border-secondary focus:outline-none dark:border-stroke-7 dark:bg-background-6 dark:text-accent dark:placeholder:text-accent/60 dark:focus-visible:border-stroke-4/20"
                                            ></textarea>
                                        </div>

                                        <!-- terms checkbox -->
                                        <fieldset class="mb-4 flex items-center gap-2">
                                            <label for="terms" class="flex items-center gap-x-3">
                                                <input id="terms" type="checkbox" class="peer sr-only" required />
                                                <span
                                                    class="relative size-4 cursor-pointer rounded-full border border-stroke-3 peer-checked:border-primary-500 after:absolute after:top-1/2 after:left-1/2 after:size-2.5 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-primary-500 after:opacity-0 peer-checked:after:opacity-100 dark:border-stroke-7"
                                                ></span>
                                            </label>
                                            <label
                                                for="terms"
                                                class="cursor-pointer text-tagline-3 text-secondary/60 dark:text-accent/60"
                                            >
                                                {{ supportTerms.text ?? 'I agree with the' }}
                                                <a href="#" class="text-tagline-3 text-primary-500 underline"
                                                    >{{ supportTerms.link ?? 'terms and conditions' }}</a
                                                >
                                            </label>
                                        </fieldset>

                                        <!-- submit button -->
                                        <button
                                            type="submit"
                                            class="btn btn-md w-full btn-secondary first-letter:uppercase before:content-none hover:btn-primary dark:btn-accent"
                                        >
                                            {{ supportForm.submit ?? 'Submit' }}
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
