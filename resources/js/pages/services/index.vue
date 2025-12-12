<script lang="ts" setup>
import GuestLayout from '@/layouts/GuestLayout.vue';
import { nextTick, onMounted } from 'vue';
import { initHomeAnimations } from '@/lib/homeAnimations';
import ContentList from '@/pages/services/content-list.vue';
import ContentListV2 from '@/pages/services/content-list-v2.vue';
import ContentListV3 from '@/pages/services/content-list-v3.vue';
const props = withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
onMounted(async () => {
    await nextTick();
    await initHomeAnimations();
});
const service_1 = new URL('../../../images/services-1.png', import.meta.url).href;
const service_2 = new URL('../../../images/services-2.png', import.meta.url).href;
</script>
<template>
    <GuestLayout :can-register="props.canRegister" title="Service details">
        <template #main>
            <section class="{=$class} z-[9999] pt-32 sm:pt-36 md:pt-42 xl:pt-[180px]" aria-label="Page hero section">
                <div class="main-container">
                    <!-- Hero content -->
                    <div class="space-y-2 pb-14 text-center lg:pb-[72px]">
                        <span
                            data-ns-animate
                            data-delay="0.1"
                            class="hero-badge inline-block text-tagline-1 text-secondary dark:text-accent"
                        >
                            <a
                                href="/"
                                class="transition-colors duration-300 hover:text-primary-600 dark:hover:text-primary-400"
                                >Home</a
                            >
                            <span class="mx-2">-</span>
                            <a
                                href="#"
                                class="transition-colors duration-300 hover:text-primary-500 dark:hover:text-primary-400"
                                >Service details</a
                            >
                        </span>
                        <h1 data-ns-animate data-delay="0.2" class="font-normal !text-primary lg:text-heading-2">
                            Service details
                        </h1>
                    </div>
                </div>
            </section>
            <section class="pt-14 md:pt-16 lg:pt-[88px] xl:pt-[100px] pb-24 md:pb-36 lg:pb-44 xl:pb-[200px]">
                <div class="main-container">
                    <div class="flex items-start lg:gap-[72px]">
                        <div class="lg:max-w-[767px] max-w-full w-full">
                            <div class="services-details-content mb-[72px]">
                                <h2 data-ns-animate data-delay="0.3" id="track-conversions">Track conversions</h2>
                                <p data-ns-animate data-delay="0.4">
                                    Track every action your users take—from address search to report export—and turn that behavior into clear, measurable insights. Pixrup gives you full visibility into how properties, reports, and AI features are being used across your workflow.
                                </p>
                                <p data-ns-animate data-delay="0.5">
                                    Understand which properties convert, which features drive value, and where users drop off. This allows you to optimize your sales process, improve client engagement, and focus on what actually moves deals forward.
                                </p>
                                <p data-ns-animate data-delay="0.6">
                                    With real-time tracking and historical insights, you can confidently measure ROI, usage patterns, and performance across teams, properties, and time.
                                </p>
                                <figure
                                    data-ns-animate
                                    data-delay="0.6"
                                    data-instant
                                    class="rounded-xl overflow-hidden max-w-[767px]"
                                >
                                    <img
                                        :src="service_1"
                                        alt="service-details"
                                        class="size-full object-cover"
                                    />
                                </figure>

                                <h2 data-ns-animate data-delay="0.1" id="sales-management">What’s included</h2>
                                <p data-ns-animate data-delay="0.2">
                                    Pixrup comes with a complete set of tools designed to track, analyze, and act on user behavior across the entire property lifecycle.
                                </p>
                                <p data-ns-animate data-delay="0.3">
                                    Every interaction—AI appraisals, GlowUp generations, market analysis, report exports, and collaboration—is automatically logged and structured into actionable data.
                                </p>
                                <p data-ns-animate data-delay="0.3">
                                    You get clarity without manual setup, complex dashboards, or external analytics tools.
                                </p>

                                <content-list />

                                <figure data-ns-animate data-delay="0.3" class="max-w-[767px] rounded-xl overflow-hidden">
                                    <img
                                        :src="service_2"
                                        alt="service-details"
                                        class="size-full object-cover"
                                    />
                                </figure>

                                <h2 data-ns-animate data-delay="0.2" id="use-cases">Use cases</h2>
                                <p data-ns-animate data-delay="0.3">
                                    Pixrup analytics adapts to different business needs, whether you’re an independent agent or a growing real estate team.
                                </p>
                                <p data-ns-animate data-delay="0.4">
                                    Use it to identify which properties are client-ready, which reports drive engagement, and which AI tools deliver the most impact. Track adoption across your team and uncover bottlenecks before they become problems.
                                </p>
                                <p data-ns-animate data-delay="0.6">
                                    From sales optimization to operational oversight, Pixrup turns raw activity into strategic insight.
                                </p>

                                <content-list-v2 />
                                <div class="my-8"></div>
                                <h2 data-ns-animate data-delay="0.2" id="real-time-analytics">
                                    Built-in security & controls
                                </h2>
                                <p data-ns-animate data-delay="0.3">
                                    All tracking and analytics are built with privacy, control, and security at the core. Data is scoped per account, per property, and per user—no cross-tenant leakage, no shared exposure.
                                </p>
                                <p data-ns-animate data-delay="0.4">
                                    You decide who sees what. Sensitive actions are protected, logged, and auditable, ensuring compliance and accountability across your organization.
                                </p>
                                <p data-ns-animate data-delay="0.6">
                                    Pixrup analytics works seamlessly with your existing permission model, without introducing risk or complexity.
                                </p>

                                <content-list-v3 />
                            </div>
                            <div class="mt-[70px] space-y-14" id="live-data-insights">
                                <div class="space-y-3">
                                    <h4 data-ns-animate data-delay="0.1" class="text-heading-2">What our users say</h4>
                                    <p data-ns-animate data-delay="0.2" class="text-tagline-1">
                                        “Pixrup gave us visibility we never had before. We can finally see which properties are ready, which reports convert, and where to focus our efforts—without spreadsheets or guesswork.”
                                    </p>
                                </div>
                                <div data-ns-animate data-delay="0.1" class="bg-secondary p-8 rounded-[20px] space-y-6">
                                    <figure
                                        class="size-16 rounded-full overflow-hidden bg-linear-[180deg,#ffffff_0%,#83e7ee_100%]"
                                    >
                                        <img class="size-full object-cover" src="images/avatar/avatar-1.png" alt="avatar" />
                                    </figure>
                                    <blockquote>
                                        <p data-ns-animate data-delay="0.3" class="text-white">
                                            “Workflow Automation helped us eliminate hours of manual work each week. Now, our
                                            ops team can focus on strategic initiatives instead of button-clicking.”
                                        </p>
                                    </blockquote>
                                    <div>
                                        <p data-ns-animate data-delay="0.4" class="text-lg font-medium text-white">
                                            John Smith
                                        </p>
                                        <p data-ns-animate data-delay="0.5" class="text-tagline-2 font-normal text-accent/60">
                                            Real Estate Investor & Broker
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Table of Contents -->
                        <div
                            class="table-of-contents lg:max-w-[449px] w-full lg:block lg:sticky lg:top-20 hidden"
                            data-ns-animate
                            data-delay="0.3"
                        >
                            <div class="p-11 rounded-[20px] bg-background-1 dark:bg-background-6 space-y-4 w-full">
                                <h3 class="text-heading-5">Categories</h3>
                                <ul class="table-of-list w-full">
                                    <li>
                                        <a
                                            href="#track-conversions"
                                            class="lenis-scroll-to py-4 flex items-center justify-between border-b border-b-stroke-4 dark:border-b-stroke-7"
                                        >
                <span class="text-lg leading-[27px] font-medium text-secondary dark:text-accent">
                  Track conversions
                </span>

                                            <span>
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="25"
                      viewBox="0 0 24 25"
                      fill="none"
                  >
                    <path
                        d="M10 8.5L14 12.5L10 16.5"
                        class="stroke-secondary dark:stroke-accent"
                        stroke-opacity="0.6"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                  </svg>
                </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="#sales-management"
                                            class="lenis-scroll-to py-4 flex items-center justify-between border-b border-b-stroke-4 dark:border-b-stroke-7"
                                        >
                <span
                    class="text-lg leading-[27px] font-normal text-secondary/60 dark:text-accent/60"
                >
                  Sales management
                </span>
                                            <span class="invisible">
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="25"
                      viewBox="0 0 24 25"
                      fill="none"
                  >
                    <path
                        d="M10 8.5L14 12.5L10 16.5"
                        class="stroke-secondary dark:stroke-accent"
                        stroke-opacity="0.6"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                  </svg>
                </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="#use-cases"
                                            class="lenis-scroll-to py-4 flex items-center justify-between border-b border-b-stroke-4 dark:border-b-stroke-7"
                                        >
                <span
                    class="text-lg leading-[27px] font-normal text-secondary/60 dark:text-accent/60"
                >
                  Use cases
                </span>
                                            <span class="invisible">
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="25"
                      viewBox="0 0 24 25"
                      fill="none"
                  >
                    <path
                        d="M10 8.5L14 12.5L10 16.5"
                        class="stroke-secondary dark:stroke-accent"
                        stroke-opacity="0.6"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                  </svg>
                </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="#real-time-analytics"
                                            class="lenis-scroll-to flex items-center justify-between py-4 border-b border-b-stroke-4 dark:border-b-stroke-7"
                                        >
                <span
                    class="text-lg leading-[27px] font-normal text-secondary/60 dark:text-accent/60"
                >
                  Real-time analytics
                </span>
                                            <span class="invisible">
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="25"
                      viewBox="0 0 24 25"
                      fill="none"
                  >
                    <path
                        d="M10 8.5L14 12.5L10 16.5"
                        class="stroke-secondary dark:stroke-accent"
                        stroke-opacity="0.6"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                  </svg>
                </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="#live-data-insights"
                                            class="lenis-scroll-to flex items-center justify-between py-4"
                                        >
                <span
                    class="text-lg leading-[27px] font-normal text-secondary/60 dark:text-accent/60"
                >
                  Live data insights
                </span>
                                            <span class="invisible">
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="25"
                      viewBox="0 0 24 25"
                      fill="none"
                  >
                    <path
                        d="M10 8.5L14 12.5L10 16.5"
                        class="stroke-secondary dark:stroke-accent"
                        stroke-opacity="0.6"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                  </svg>
                </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>
    </GuestLayout>
</template>
