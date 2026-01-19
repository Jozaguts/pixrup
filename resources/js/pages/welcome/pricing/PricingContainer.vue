<script setup lang="ts">
import { computed, ref, watchEffect } from 'vue';
import { useLandingTranslations } from '@/composables/useLandingTranslations';
import { cn } from '@/lib/utils';
import { usePage } from '@inertiajs/vue3';
import type { AppPageProps, PricingPlan } from '@/types';

type PricingPlanPrice = NonNullable<PricingPlan['prices']['month']>;

const props = withDefaults(
    defineProps<{
        plans?: PricingPlan[];
    }>(),
    {
        plans: () => [],
    },
);

const landingTranslations = useLandingTranslations();
const pricingTranslations = computed(
    () => landingTranslations.value.pricing ?? {},
);
const billingTranslations = computed(
    () => pricingTranslations.value.billing ?? {},
);

const hasMonthly = computed(() =>
    props.plans.some((plan) => Boolean(plan.prices?.month)),
);
const hasYearly = computed(() =>
    props.plans.some((plan) => Boolean(plan.prices?.year)),
);
const showToggle = computed(() => hasMonthly.value && hasYearly.value);

const billingInterval = ref<'month' | 'year'>('month');

watchEffect(() => {
    if (!hasMonthly.value && hasYearly.value) {
        billingInterval.value = 'year';
    }

    if (!hasYearly.value && hasMonthly.value) {
        billingInterval.value = 'month';
    }
});

const page = usePage<AppPageProps>();
const formatterLocale = computed(() =>
    page.props.locale === 'es' ? 'es-ES' : 'en-US',
);

const formatPrice = (price?: PricingPlanPrice | null) => {
    if (!price) {
        return '--';
    }

    const currency = price.currency?.toUpperCase() ?? 'USD';

    try {
        return new Intl.NumberFormat(formatterLocale.value, {
            style: 'currency',
            currency,
        }).format(price.unit_amount / 100);
    } catch (error) {
        return `$${(price.unit_amount / 100).toFixed(0)}`;
    }
};

const resolvePlanPrice = (plan: PricingPlan) => {
    const monthly = plan.prices?.month ?? null;
    const yearly = plan.prices?.year ?? null;
    const preferred = billingInterval.value === 'year' ? yearly : monthly;

    if (preferred) {
        return { price: preferred, interval: billingInterval.value };
    }

    if (monthly) {
        return { price: monthly, interval: 'month' };
    }

    if (yearly) {
        return { price: yearly, interval: 'year' };
    }

    return { price: null, interval: null };
};

const resolveIntervalLabel = (interval: 'month' | 'year' | null) => {
    if (!interval) {
        return '';
    }

    if (interval === 'year') {
        return billingTranslations.value.per_year ?? 'Per Year';
    }

    return billingTranslations.value.per_month ?? 'Per Month';
};

const toggleBillingInterval = () => {
    if (!showToggle.value) {
        return;
    }

    billingInterval.value =
        billingInterval.value === 'month' ? 'year' : 'month';
};

const cardOuterClass = (plan: PricingPlan) =>
    cn(
        'flex-1 max-lg:w-full rounded-[20px]',
        plan.is_featured
            ? "p-2.5 bg-[url('/images/home-page-2/price-bg.png')] bg-center bg-cover bg-no-repeat"
            : 'p-8 bg-background-3 dark:bg-background-5',
    );

const cardInnerClass = (plan: PricingPlan) =>
    cn(
        plan.is_featured
            ? 'p-8 bg-white rounded-[12px] dark:bg-background-8'
            : '',
    );

const gridClass = computed(() =>
    cn(
        'grid grid-cols-1 items-center gap-8',
        props.plans.length > 1 && 'md:grid-cols-2',
        props.plans.length === 2 && 'lg:grid-cols-2',
        props.plans.length === 3 && 'lg:grid-cols-3',
        props.plans.length >= 4 && 'lg:grid-cols-4',
    ),
);
</script>

<template>
    <section
        id="pricing"
        class="relative pt-[100px] pb-20 md:pb-[100px] lg:pb-[150px] xl:pb-[200px]"
    >
        <div class="main-container flex flex-col gap-[70px]">
            <div class="flex flex-col items-center text-center">
                <span
                    data-ns-animate
                    data-delay="0.2"
                    class="badge badge-primary mb-5"
                >
                    {{ pricingTranslations.badge ?? 'Pricing' }}
                </span>
                <h2
                    data-ns-animate
                    data-delay="0.3"
                    class="mx-auto mb-8 max-w-[650px]"
                >
                    {{
                        pricingTranslations.title ??
                        'Select the pricing plan that best suits your needs.'
                    }}
                </h2>

                <div
                    v-if="showToggle"
                    data-ns-animate
                    data-delay="0.4"
                    class="relative z-0"
                >
                    <label
                        class="relative z-[10] inline-flex cursor-pointer items-center rounded-full bg-white px-[57px] py-6 dark:bg-background-9"
                    >
                        <span
                            class="mr-2.5 text-base font-normal text-secondary dark:text-accent"
                        >
                            {{ billingTranslations.monthly ?? 'Monthly' }}
                        </span>
                        <input
                            id="priceCheck"
                            type="checkbox"
                            class="peer sr-only"
                            :checked="billingInterval === 'year'"
                            :aria-label="billingTranslations.toggle_aria ?? 'Toggle between monthly and yearly pricing'"
                            @change="toggleBillingInterval"
                        />
                        <span
                            class="relative h-[28px] w-13 rounded-[34px] bg-secondary after:absolute after:start-[2px] after:top-1/2 after:h-6 after:w-6 after:-translate-y-1/2 after:rounded-full after:bg-accent after:transition-all after:content-[''] peer-checked:after:translate-x-full dark:bg-accent dark:after:bg-background-9"
                        ></span>
                        <span
                            class="ms-2.5 text-base font-normal text-secondary dark:text-accent"
                        >
                            {{ billingTranslations.yearly ?? 'Yearly' }}
                        </span>
                    </label>
                </div>
            </div>
            <div class="relative">
                <div :class="gridClass">
                    <div
                        v-for="plan in props.plans"
                        :key="plan.id ?? plan.key"
                        data-ns-animate
                        data-instant
                        class="flex-1"
                        :class="cardOuterClass(plan)"
                        v-auto-animate
                    >
                        <div :class="cardInnerClass(plan)">
                            <h3 class="mb-2 font-normal text-heading-5">
                                {{ plan.name }}
                            </h3>
                            <p
                                v-if="plan.description"
                                class="mb-6 max-w-[250px] text-secondary/60 dark:text-accent/60"
                            >
                                {{ plan.description }}
                            </p>
                            <div class="mb-7">
                                <h4 class="text-heading-4 font-normal">
                                    <span
                                        v-if="resolvePlanPrice(plan).price"
                                        >{{ formatPrice(resolvePlanPrice(plan).price) }}</span
                                    >
                                    <span v-else>--</span>
                                </h4>
                                <p class="text-secondary dark:text-accent">
                                    {{
                                        resolveIntervalLabel(
                                            resolvePlanPrice(plan).interval,
                                        )
                                    }}
                                </p>
                            </div>
                            <a
                                href="./contact-us-page.html"
                                class="btn btn-md mb-8 w-full text-center before:content-none first-letter:uppercase"
                                :class="
                                    plan.is_featured
                                        ? 'btn-secondary hover:btn-primary dark:btn-accent'
                                        : 'btn-white hover:btn-secondary dark:btn-white-dark dark:hover:btn-accent'
                                "
                            >
                                {{ pricingTranslations.cta ?? 'Get started' }}
                            </a>
                            <ul
                                v-if="plan.features?.length"
                                class="relative list-none space-y-2.5"
                            >
                                <li
                                    v-for="(feature, index) in plan.features"
                                    :key="`${plan.key}-feature-${index}`"
                                    class="flex items-center gap-2.5"
                                >
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
                                    <span
                                        class="text-tagline-1 font-normal text-secondary dark:text-accent"
                                    >
                                        {{ feature }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
