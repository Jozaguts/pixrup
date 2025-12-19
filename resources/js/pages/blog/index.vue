<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { nextTick, onBeforeUnmount, onMounted } from 'vue';
import { initHomeAnimations } from '@/lib/homeAnimations';
import OurBlogs from '@/pages/blog/our-blogs.vue';
import Swiper from 'swiper';
const props = withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
let swiperInstance: Swiper | null = null;
onMounted(async () => {
    await nextTick();
    await initHomeAnimations();
    const Swiper = (await import('swiper')).default;
    const { Autoplay, Pagination } = await import('swiper/modules');
    await Promise.all([import('swiper/css'), import('swiper/css/pagination'), import('swiper/css/autoplay')]);
    swiperInstance = new Swiper('.blog-article-swiper', {
        modules: [Autoplay, Pagination], // 👈 CLAVE
        slidesPerView: 1,
        spaceBetween: 40,
        loop: true,
        effect: 'slide',
        speed: 1000,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.pagination-bullets',
            clickable: true,
            type: 'bullets',
        },
        on: {
            slideChange(swiper) {
                swiper.slides.forEach((slide, index) => {
                    slide.style.transition =
                        index === swiper.activeIndex
                            ? 'all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)'
                            : 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                });
            },
        },
    });
});

onBeforeUnmount(() => {
    swiperInstance?.destroy(true, true);
});
</script>

<template>
    <GuestLayout :can-register="props.canRegister" title="Blog">
        <template #main>
            <section class="pt-32 sm:pt-36 md:pt-42 xl:pt-[180px]" aria-label="Page hero section">
                <div class="main-container">
                    <!-- Hero content -->
                    <div class="space-y-2 pb-14 text-center lg:pb-[72px]">
                        <span
                            data-ns-animate
                            data-delay="0.1"
                            class="hero-badge inline-block text-tagline-1 text-secondary dark:text-accent"
                        >
                            <a
                                href="./index.html"
                                class="transition-colors duration-300 hover:text-primary-600 dark:hover:text-primary-400"
                                >Home</a
                            >
                            <span class="mx-2">-</span>
                            <a
                                href="#"
                                class="transition-colors duration-300 hover:text-primary-500 dark:hover:text-primary-400"
                                >Blog</a
                            >
                        </span>
                        <h1 data-ns-animate data-delay="0.2" class="font-normal lg:text-heading-2">Blog</h1>
                    </div>
                </div>
            </section>
            <section class="pt-7 pb-14 sm:pt-16 md:pt-20 md:pb-16 lg:pt-24 lg:pb-[88px] xl:pt-32 xl:pb-[100px]">
                <div class="main-container">
                    <div class="space-y-10 md:space-y-[70px]">
                        <h2 data-ns-animate data-delay="0.2" class="mx-auto max-w-[700px] text-center">
                            Latest articles published by NextSaaS
                        </h2>
                        <div class="relative" data-ns-animate data-delay="0.3">
                            <div class="swiper blog-article-swiper">
                                <div class="swiper-wrapper">
                                    <!-- slide 1 -->
                                    <div class="swiper-slide">
                                        <article
                                            class="scale-100 transition-transform duration-500 hover:scale-[99%] hover:transition-transform hover:duration-500"
                                        >
                                            <figure class="max-h-[550px] w-full overflow-hidden rounded-t-[20px]">
                                                <img
                                                    src="images/blogs/blog-cover.png"
                                                    alt="Blog-Article"
                                                    class="h-full w-full object-cover"
                                                />
                                            </figure>
                                            <div
                                                class="space-y-6 rounded-b-[20px] bg-background-1 px-4 py-8 md:p-8 dark:bg-background-6"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="mr-1 badge badge-primary">Technology</span>

                                                    <span
                                                        rel="author"
                                                        class="text-tagline-3 font-normal text-secondary/60 dark:text-accent/60"
                                                        >James Wilson</span
                                                    >
                                                    <span class="h-[6px] w-[5px] rounded-full bg-[#ECE8FF]"> </span>
                                                    <time
                                                        datetime="2025-04-15"
                                                        class="text-tagline-3 font-normal text-secondary/60 dark:text-accent/60"
                                                    >
                                                        April 15, 2025
                                                    </time>
                                                </div>
                                                <div>
                                                    <h3 class="mb-2 text-tagline-1 font-normal sm:text-heading-5">
                                                        <a
                                                            href="./blog-details-page.html"
                                                            aria-label="Read full article about electronic prescription in finance sector"
                                                        >
                                                            Revolutionize Your Workflow with AI-Powered Automation
                                                        </a>
                                                    </h3>
                                                    <p
                                                        class="text-tagline-2 font-normal text-secondary/60 sm:text-tagline-1 dark:text-accent/60"
                                                    >
                                                        Discover how artificial intelligence is transforming business
                                                        processes and boosting productivity across industries.
                                                    </p>
                                                </div>
                                                <div>
                                                    <a
                                                        href="./blog-details-page.html"
                                                        class="btn inline-block btn-md btn-white hover:btn-primary dark:btn-transparent"
                                                        aria-label="Read full article about electronic prescription in finance sector"
                                                    >
                                                        <span>Read more</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                    <!-- slide 2 -->
                                    <div class="swiper-slide">
                                        <article
                                            class="scale-100 transition-transform duration-500 hover:scale-[99%] hover:transition-transform hover:duration-500"
                                        >
                                            <figure class="max-h-[550px] w-full overflow-hidden rounded-t-[20px]">
                                                <img
                                                    src="images/blogs/blog-38.png"
                                                    alt="Blog-Article"
                                                    class="h-full w-full object-cover"
                                                />
                                            </figure>
                                            <div
                                                class="space-y-6 rounded-b-[20px] bg-background-1 px-4 py-8 md:p-8 dark:bg-background-6"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="mr-1 badge badge-primary">Security</span>

                                                    <span
                                                        rel="author"
                                                        class="text-tagline-3 font-normal text-secondary/60 dark:text-accent/60"
                                                        >Sarah Chen</span
                                                    >
                                                    <span class="h-[6px] w-[5px] rounded-full bg-[#ECE8FF]"> </span>
                                                    <time
                                                        datetime="2025-04-12"
                                                        class="text-tagline-3 font-normal text-secondary/60 dark:text-accent/60"
                                                    >
                                                        April 12, 2025
                                                    </time>
                                                </div>
                                                <div>
                                                    <h3 class="mb-2 text-tagline-1 font-normal sm:text-heading-5">
                                                        <a
                                                            href="./blog-details-page.html"
                                                            aria-label="Read full article about electronic prescription in finance sector"
                                                        >
                                                            Essential Cybersecurity Practices for Modern Businesses
                                                        </a>
                                                    </h3>
                                                    <p
                                                        class="text-tagline-2 font-normal text-secondary/60 sm:text-tagline-1 dark:text-accent/60"
                                                    >
                                                        Learn the latest strategies to protect your organization from
                                                        emerging cyber threats and data breaches.
                                                    </p>
                                                </div>
                                                <div>
                                                    <a
                                                        href="./blog-details-page.html"
                                                        class="btn inline-block btn-md btn-white hover:btn-primary dark:btn-transparent"
                                                        aria-label="Read full article about electronic prescription in finance sector"
                                                    >
                                                        <span>Read more</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                    <!-- slide 3 -->
                                    <div class="swiper-slide">
                                        <article
                                            class="scale-100 transition-transform duration-500 hover:scale-[99%] hover:transition-transform hover:duration-500"
                                        >
                                            <figure class="max-h-[550px] w-full overflow-hidden rounded-t-[20px]">
                                                <img
                                                    src="images/blogs/blog-01.png"
                                                    alt="Blog-Article"
                                                    class="h-full w-full object-cover"
                                                />
                                            </figure>
                                            <div
                                                class="space-y-6 rounded-b-[20px] bg-background-1 px-4 py-8 md:p-8 dark:bg-background-6"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="mr-1 badge badge-primary">Innovation</span>

                                                    <span
                                                        rel="author"
                                                        class="text-tagline-3 font-normal text-secondary/60 dark:text-accent/60"
                                                        >Michael Brown</span
                                                    >
                                                    <span class="h-[6px] w-[5px] rounded-full bg-[#ECE8FF]"> </span>
                                                    <time
                                                        datetime="2025-04-10"
                                                        class="text-tagline-3 font-normal text-secondary/60 dark:text-accent/60"
                                                    >
                                                        April 10, 2025
                                                    </time>
                                                </div>
                                                <div>
                                                    <h3 class="mb-2 text-tagline-1 font-normal sm:text-heading-5">
                                                        <a
                                                            href="./blog-details-page.html"
                                                            aria-label="Read full article about electronic prescription in finance sector"
                                                        >
                                                            Digital Transformation Trends Shaping the Future
                                                        </a>
                                                    </h3>
                                                    <p
                                                        class="text-tagline-2 font-normal text-secondary/60 sm:text-tagline-1 dark:text-accent/60"
                                                    >
                                                        Explore the latest digital transformation trends that are
                                                        revolutionizing how businesses operate and compete.
                                                    </p>
                                                </div>
                                                <div>
                                                    <a
                                                        href="./blog-details-page.html"
                                                        class="btn inline-block btn-md btn-white hover:btn-primary dark:btn-transparent"
                                                        aria-label="Read full article about electronic prescription in finance sector"
                                                    >
                                                        <span>Read more</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                </div>
                                <div class="pagination-bullets mt-5 md:mt-14"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <OurBlogs />
        </template>
    </GuestLayout>
</template>

<style scoped></style>
