<script setup lang="ts">
import { computed, ref } from 'vue';
import { useLandingTranslations } from '@/composables/useLandingTranslations';

const isMonthlyPrice = ref(false)
const landingTranslations = useLandingTranslations();
const pricingTranslations = computed(
    () => landingTranslations.value.pricing ?? {},
);
const billingTranslations = computed(
    () => pricingTranslations.value.billing ?? {},
);
const simplifiedPlan = computed(
    () => pricingTranslations.value.plans?.simplified ?? {},
);
const basicPlan = computed(
    () => pricingTranslations.value.plans?.basic ?? {},
);
const enhancedPlan = computed(
    () => pricingTranslations.value.plans?.enhanced ?? {},
);
const simplifiedFeatures = computed(
    () => simplifiedPlan.value.features ?? {},
);
const basicFeatures = computed(() => basicPlan.value.features ?? {});
const enhancedFeatures = computed(
    () => enhancedPlan.value.features ?? {},
);
</script>

<template>
    <section class="relative pb-20 md:pb-[100px] lg:pb-[150px] xl:pb-[200px] pt-[100px]" id="pricing">
        <div class="main-container flex flex-col gap-[70px]">
            <div class="flex flex-col items-center text-center">
                <span data-ns-animate data-delay="0.2" class="badge badge-primary mb-5">
                    {{ pricingTranslations.badge ?? 'Pricing' }}
                </span>
                <h2 data-ns-animate data-delay="0.3" class="max-w-[650px] mx-auto mb-8">
                    {{
                        pricingTranslations.title ??
                        'Select the pricing plan that best suits your needs.'
                    }}
                </h2>

                <div data-ns-animate data-delay="0.4" class="relative z-0">
                    <label
                        class="relative inline-flex items-center cursor-pointer z-[10] bg-white dark:bg-background-9 py-6 px-[57px] rounded-full"
                    >
                        <span class="mr-2.5 text-base text-secondary dark:text-accent font-normal">
                            {{ billingTranslations.monthly ?? 'Monthly' }}
                        </span>
                        <input
                            type="checkbox"
                            id="priceCheck"
                            class="sr-only peer"
                            :aria-label="billingTranslations.toggle_aria ?? 'Toggle between monthly and yearly pricing'"
                            @change="isMonthlyPrice = !isMonthlyPrice"
                        />
                        <span
                            class="relative w-13 h-[28px] bg-secondary rounded-[34px] dark:bg-accent peer-checked:after:translate-x-full after:content-[''] after:absolute *: dark:after:bg-background-9 after:top-1/2 after:-translate-y-1/2 after:start-[2px] peer-checked:after:start-[2px] after:bg-accent d after:rounded-full after:h-6 after:w-6 after:transition-all"
                        ></span>
                        <span class="ms-2.5 text-base text-secondary dark:text-accent font-normal">
                            {{ billingTranslations.yearly ?? 'Yearly' }}
                        </span>
                    </label>
                </div>
            </div>
            <div class="relative">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 items-center gap-8">
                    <!-- Price Card 1 -->
                    <div
                        data-ns-animate
                        data-delay="0.3"
                        data-instant
                        class="bg-background-3 dark:bg-background-5 flex-1 p-8 rounded-[20px] max-lg:w-full"
                        v-auto-animate
                    >
                        <h3 class="mb-2 font-normal text-heading-5">
                            {{ simplifiedPlan.name ?? 'Simplified' }}
                        </h3>
                        <p class="mb-6 max-w-[250px]">
                            {{
                                simplifiedPlan.description ??
                                'For individuals and small teams with unlimited trial access.'
                            }}
                        </p>
                        <div v-if="isMonthlyPrice" class="price-month mb-7">
                            <h4 class="text-heading-4 font-normal">$<span>19.00</span></h4>
                            <p class="text-secondary dark:text-accent">
                                {{ billingTranslations.per_month ?? 'Per Month' }}
                            </p>
                        </div>
                        <div v-else class="price-year mb-7">
                            <h4 class="text-heading-4 font-normal">$<span>230.00</span></h4>
                            <p class="text-secondary dark:text-accent">
                                {{ billingTranslations.per_year ?? 'Per Year' }}
                            </p>
                        </div>
                        <a
                            href="./contact-us-page.html"
                            class="btn btn-md btn-white dark:btn-white-dark hover:btn-secondary dark:hover:btn-accent w-full block text-center mb-8 before:content-none first-letter:uppercase"
                        >
                            {{ pricingTranslations.cta ?? 'Get started' }}
                        </a>
                        <ul class="relative list-none space-y-2.5">
                            <li class="flex items-center gap-2.5">
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="shrink-0"
                                >
                                    <rect
                                        width="20"
                                        height="20"
                                        rx="10"
                                        fill=""
                                        class="fill-secondary dark:fill-accent"
                                    />
                                    <path
                                        d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                        fill=""
                                        class="fill-white dark:fill-black"
                                    />
                                </svg>

                                <span class="text-secondary dark:text-accent font-normal text-tagline-1"
                                >{{ simplifiedFeatures.single_payment ?? 'Single Payment' }}</span
                                >
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="shrink-0"
                                >
                                    <rect
                                        width="20"
                                        height="20"
                                        rx="10"
                                        fill=""
                                        class="fill-white dark:fill-background-9"
                                    />
                                    <path
                                        d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                        fill=""
                                        class="fill-secondary/60 dark:fill-accent/60"
                                    />
                                </svg>

                                <span class="text-secondary/60 dark:text-accent/60 font-normal text-tagline-1"
                                >{{ simplifiedFeatures.sell_items ?? 'Selling your own items' }}</span
                                >
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="shrink-0"
                                >
                                    <rect
                                        width="20"
                                        height="20"
                                        rx="10"
                                        fill=""
                                        class="fill-white dark:fill-background-9"
                                    />
                                    <path
                                        d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                        fill=""
                                        class="fill-secondary/60 dark:fill-accent/60"
                                    />
                                </svg>

                                <span class="text-secondary/60 dark:text-accent/60 font-normal text-tagline-1"
                                >{{ simplifiedFeatures.integrations ?? 'Powerful integration' }}</span
                                >
                            </li>
                        </ul>
                    </div>
                    <!-- Price Card 2 -->
                    <div
                        data-ns-animate
                        data-delay="0.4"
                        data-instant
                        class="p-2.5 rounded-[20px] flex-1 bg-[url('/images/home-page-2/price-bg.png')] bg-no-repeat bg-center bg-cover max-lg:w-full"
                    >
                        <div class="bg-white dark:bg-background-8 p-8 rounded-[12px]" v-auto-animate>
                            <h3 class="mb-2.5 font-normal text-heading-5">
                                {{ basicPlan.name ?? 'Basic' }}
                            </h3>
                            <p class="mb-6 text-secondary/60 dark:text-accent/60 max-w-[250px]">
                                {{
                                    basicPlan.description ??
                                    'For individuals and small teams with unlimited trial access.'
                                }}
                            </p>
                            <div  v-if="isMonthlyPrice" class="price-month mb-7">
                                <h4 class="text-heading-4 font-normal">$<span>3342.00</span></h4>
                                <p class="text-secondary dark:text-accent">
                                    {{ billingTranslations.per_month ?? 'Per Month' }}
                                </p>
                            </div>
                            <div v-else class="price-year mb-7">
                                <h4 class="text-heading-4 font-normal">$<span>4420.00</span></h4>
                                <p class="text-secondary dark:text-accent">
                                    {{ billingTranslations.per_year ?? 'Per Year' }}
                                </p>
                            </div>
                            <a
                                href="./contact-us-page.html"
                                class="btn btn-md btn-secondary dark:btn-accent hover:btn-primary w-full block mb-8 before:content-none first-letter:uppercase"
                            >
                                {{ pricingTranslations.cta ?? 'Get started' }}
                            </a>
                            <ul class="relative list-none space-y-2.5">
                                <li class="flex items-center gap-2.5">
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="shrink-0"
                                    >
                                        <rect
                                            width="20"
                                            height="20"
                                            rx="10"
                                            fill=""
                                            class="fill-secondary dark:fill-accent"
                                        />
                                        <path
                                            d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                            fill=""
                                            class="fill-white dark:fill-black"
                                        />
                                    </svg>

                                    <span class="text-secondary dark:text-accent font-normal text-tagline-1"
                                    >{{ basicFeatures.bandwidth ?? 'Unlimited Bandwidth' }}</span
                                    >
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="shrink-0"
                                    >
                                        <rect
                                            width="20"
                                            height="20"
                                            rx="10"
                                            fill=""
                                            class="fill-secondary dark:fill-accent"
                                        />
                                        <path
                                            d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                            fill=""
                                            class="fill-white dark:fill-black"
                                        />
                                    </svg>

                                    <span class="text-secondary dark:text-accent font-normal text-tagline-1"
                                    >{{ basicFeatures.promo_tools ?? 'Promotional Tools' }}</span
                                    >
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="shrink-0"
                                    >
                                        <rect
                                            width="20"
                                            height="20"
                                            rx="10"
                                            fill=""
                                            class="fill-secondary dark:fill-accent"
                                        />
                                        <path
                                            d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                            fill=""
                                            class="fill-white dark:fill-black"
                                        />
                                    </svg>

                                    <span class="text-secondary dark:text-accent font-normal text-tagline-1"
                                    >{{ basicFeatures.single_payment ?? 'Single Payment' }}</span
                                    >
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="shrink-0"
                                    >
                                        <rect
                                            width="20"
                                            height="20"
                                            rx="10"
                                            fill=""
                                            class="fill-secondary dark:fill-accent"
                                        />
                                        <path
                                            d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                            fill=""
                                            class="fill-white dark:fill-black"
                                        />
                                    </svg>

                                    <span class="text-secondary dark:text-accent font-normal text-tagline-1"
                                    >Single Payment</span
                                    >
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="shrink-0"
                                    >
                                        <rect
                                            width="20"
                                            height="20"
                                            rx="10"
                                            fill=""
                                            class="fill-white dark:fill-background-9"
                                        />
                                        <path
                                            d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                            fill=""
                                            class="fill-secondary/60 dark:fill-accent/60"
                                        />
                                    </svg>

                                    <span class="text-secondary/60 dark:text-accent/60 font-normal text-tagline-1"
                                    >{{ basicFeatures.sell_items ?? 'Selling your own items' }}</span
                                    >
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="shrink-0"
                                    >
                                        <rect
                                            width="20"
                                            height="20"
                                            rx="10"
                                            fill=""
                                            class="fill-white dark:fill-background-9"
                                        />
                                        <path
                                            d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                            fill=""
                                            class="fill-secondary/60 dark:fill-accent/60"
                                        />
                                    </svg>

                                    <span class="text-secondary/60 dark:text-accent/60 font-normal text-tagline-1"
                                    >{{ basicFeatures.integrations ?? 'Powerful integration' }}</span
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Price Card 3 -->
                    <div
                        data-ns-animate
                        data-delay="0.5"
                        data-instant
                        class="bg-background-3 dark:bg-background-5 flex-1 p-8 rounded-[20px] max-lg:w-full"
                        v-auto-animate
                    >
                        <h3 class="mb-2 font-normal text-heading-5">
                            {{ enhancedPlan.name ?? 'Enhanced' }}
                        </h3>
                        <p class="mb-6 max-w-[250px] text-secondary/60 dark:text-accent/60">
                            {{
                                enhancedPlan.description ??
                                'For individuals and small teams with unlimited trial access.'
                            }}
                        </p>
                        <div  v-if="isMonthlyPrice" class="price-month mb-7">
                            <h4 class="text-heading-4 font-normal">$<span>4800.00</span></h4>
                            <p class="text-secondary dark:text-accent">
                                {{ billingTranslations.per_month ?? 'Per Month' }}
                            </p>
                        </div>
                        <div v-else class="price-year mb-7">
                            <h4 class="text-heading-4 font-normal">$<span>5800.00</span></h4>
                            <p class="text-secondary dark:text-accent">
                                {{ billingTranslations.per_year ?? 'Per Year' }}
                            </p>
                        </div>
                        <a
                            href="./contact-us-page.html"
                            class="btn btn-md btn-white dark:btn-white-dark hover:btn-secondary dark:hover:btn-accent w-full block mb-8 before:content-none first-letter:uppercase"
                        >
                            {{ pricingTranslations.cta ?? 'Get started' }}
                        </a>
                        <ul class="relative list-none space-y-2.5">
                            <li class="flex items-center gap-2.5">
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="shrink-0"
                                >
                                    <rect
                                        width="20"
                                        height="20"
                                        rx="10"
                                        fill=""
                                        class="fill-secondary dark:fill-accent"
                                    />
                                    <path
                                        d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                        fill=""
                                        class="fill-white dark:fill-black"
                                    />
                                </svg>

                                <span class="text-secondary dark:text-accent font-normal text-tagline-1"
                                >{{ enhancedFeatures.sell_conditions ?? 'Selling on your own conditions' }}</span
                                >
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="shrink-0"
                                >
                                    <rect
                                        width="20"
                                        height="20"
                                        rx="10"
                                        fill=""
                                        class="fill-secondary dark:fill-accent"
                                    />
                                    <path
                                        d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                        fill=""
                                        class="fill-white dark:fill-black"
                                    />
                                </svg>

                                <span class="text-secondary dark:text-accent font-normal text-tagline-1"
                                >{{ enhancedFeatures.seamless_integrations ?? 'Seamless integrations' }}</span
                                >
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="shrink-0"
                                >
                                    <rect
                                        width="20"
                                        height="20"
                                        rx="10"
                                        fill=""
                                        class="fill-white dark:fill-background-9"
                                    />
                                    <path
                                        d="M9.31661 13.7561L14.7491 8.42144C15.0836 8.0959 15.0836 7.5697 14.7491 7.24416C14.4145 6.91861 13.8736 6.91861 13.539 7.24416L8.7116 11.9901L6.46096 9.78807C6.12636 9.46253 5.58554 9.46253 5.25095 9.78807C4.91635 10.1136 4.91635 10.6398 5.25095 10.9654L8.1066 13.7561C8.27347 13.9184 8.49253 14 8.7116 14C8.93067 14 9.14974 13.9184 9.31661 13.7561Z"
                                        fill=""
                                        class="fill-secondary/60 dark:fill-accent/60"
                                    />
                                </svg>

                                <span class="text-secondary/60 dark:text-accent/60 font-normal text-tagline-1"
                                >{{ enhancedFeatures.real_time ?? 'Real-time streaming' }}</span
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>

</style>
