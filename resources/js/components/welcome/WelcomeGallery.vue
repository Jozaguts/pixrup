<script setup lang="ts">
import { initHomePage33Animations } from '@/lib/homePage33Animations';
import { nextTick, onMounted } from 'vue';

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
        class="pt-14 pb-[220px] sm:pb-14 md:pt-16 md:pb-16 lg:pt-[88px] lg:pb-[88px] xl:pt-[100px] xl:pb-[100px]"
    >
        <div class="main-container">
            <div
                class="flex flex-col items-start justify-center max-md:gap-y-18 md:flex-row md:justify-between md:gap-x-[120px]"
            >
                <!-- Left: Intro -->
                <div class="lg:sticky lg:top-28">
                    <span
                        data-ns-animate
                        data-delay="0.1"
                        class="mb-5 inline-block p-4 text-3xl font-bold text-primary"
                        >Pixrup overview</span
                    >
                    <div
                        class="mb-14 space-y-2 text-slate-800 md:max-w-[595px] dark:text-white"
                    >
                        <h2 data-ns-animate data-delay="0.2">
                            AI-powered creation, built for speed.
                        </h2>
                        <p
                            data-ns-animate
                            data-delay="0.3"
                            class="max-w-[512px]"
                        >
                            Pixrup helps you generate, transform, and deliver
                            on-brand content in minutes—secure, collaborative,
                            and PWA-ready for any team.
                        </p>
                    </div>
                </div>
                <!-- Right: Features List -->
                <div class="stack-cards js-stack-cards w-full max-w-xl">
                    <div
                        v-for="listing in listings"
                        :key="listing.id"
                        class="stack-cards__item js-stack-cards__item relative z-0 my-6 flex min-h-[170px] flex-col space-y-4 neu-surface rounded-[12px] p-8 shadow-sm shadow-neu-out md:flex-row dark:border-stroke-5"
                    >
                        <div
                            class="relative shrink-0 overflow-hidden p-2.5 md:w-2/5"
                        >
                            <img
                                :src="listing.image"
                                :alt="listing.title"
                                class="h-full w-full rounded-md object-cover md:rounded-lg"
                            />
                        </div>
                        <div class="p-6">
                            <div
                                class="mb-4 w-30 rounded-full border border-transparent bg-primary-600 px-4 py-2 text-center text-xs text-white shadow-sm transition-all"
                            >
                                {{ listing.title }}
                            </div>
                            <h4
                                class="mb-2 text-xl font-semibold text-slate-800 dark:text-white"
                            >
                                {{ listing.headline }}
                            </h4>
                            <p
                                class="mb-8 leading-normal font-light text-slate-800 dark:text-white/90"
                            >
                                {{ listing.description }}
                            </p>
                            <div>
                                <a
                                    href="#"
                                    class="flex items-center text-sm font-semibold text-slate-800 hover:underline dark:text-white"
                                >
                                    Learn More
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="ml-2 h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                                        />
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
