<script setup lang="ts">
import { nextTick, onMounted } from 'vue';
import { initHomePage33Animations } from '@/lib/homePage33Animations';

interface ListingCard {
    id: number | string;
    title: string;
    image: string;
    headline: string;
    description: string;
}

defineProps<{
    listings: ListingCard[];
}>();
onMounted(async () => {
    await nextTick();
    await initHomePage33Animations();
});
</script>

<template>
    <section
        class="pt-14 md:pt-16 lg:pt-[88px] xl:pt-[100px] pb-[220px] sm:pb-14 md:pb-16 lg:pb-[88px] xl:pb-[100px]"
    >
        <div class="main-container">
            <div
                class="flex flex-col md:flex-row items-start max-md:gap-y-18 md:gap-x-[120px] justify-center md:justify-between"
            >
                <!-- Left: Intro -->
                <div class="lg:sticky lg:top-28">
                    <span data-ns-animate data-delay="0.1" class="neu-btn neu-center-shadow badge badge-cyan mb-5">Pixrup overview</span>
                    <div class="space-y-2 md:max-w-[595px] mb-14">
                        <h2 data-ns-animate data-delay="0.2">  AI-powered creation, built for speed.</h2>
                        <p data-ns-animate data-delay="0.3" class="max-w-[512px]">
                            Pixrup helps you generate, transform, and deliver on-brand content in minutes—secure, collaborative, and PWA-ready for any team.
                        </p>
                    </div>
                    <div>
                        <a
                            data-ns-animate
                            data-delay="0.4"
                            href="./our-services-page-01.html"
                            class="neu-btn neu-center-shadow btn btn-secondary dark:btn-transparent btn-md hover:bg-primary hover:text-white"
                        >
                            <span>Explore all features</span>
                        </a>
                    </div>
                </div>
                <!-- Right: Features List -->
                <div class="w-full max-w-xl stack-cards js-stack-cards">
                    <div v-for="listing in listings" :key="listing.id"
                         class="stack-cards__item js-stack-cards__item border border-stroke-4/50 dark:border-stroke-5  neu-surface shadow-neu-out neu-center-shadow dark:bg-background-8 min-h-[170px] rounded-[20px] space-y-4 p-8 z-0 relative flex flex-col md:flex-row w-full my-6 bg-white shadow-sm border border-slate-200 rounded-lg w-96">
                        <div class="relative p-2.5 md:w-2/5 shrink-0 overflow-hidden">
                            <img
                                :src="listing.image"
                                :alt="listing.title"
                                class="h-full w-full rounded-md md:rounded-lg object-cover" />
                        </div>
                            <div class="p-6">
                                <div class="mb-4 rounded-full bg-primary-600 py-0.5 px-2.5 border border-transparent text-xs text-white transition-all shadow-sm w-30 text-center">   {{ listing.title }}</div>
                                <h4 class="mb-2 text-slate-800 text-xl font-semibold">
                                    {{listing.headline}}
                                </h4>
                                <p class="mb-8 text-slate-600 leading-normal font-light">
                                    {{listing.description}}
                                </p>
                                <div>
                                    <a href="#" class="text-slate-800 font-semibold text-sm hover:underline flex items-center">
                                        Learn More
                                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                        </div>
                            </div>
                            </div>
                            </div>
                        </div>
                    </section>
</template>
