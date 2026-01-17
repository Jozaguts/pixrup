<script setup lang="ts">
import { computed } from 'vue';
import { useLandingTranslations } from '@/composables/useLandingTranslations';

const landingTranslations = useLandingTranslations();
const footerTranslations = computed(() => landingTranslations.value.footer ?? {});
</script>

<template>
    <footer class="relative w-full overflow-hidden rounded-xl bg-background px-4 mt-10 pb-4">
        <!-- right gradient -->
        <figure
            data-ns-animate
            data-delay="0.4"
            data-duration="0.8"
            data-direction="right"
            data-offset="400"
            class="{=$hide-gradient} pointer-events-none absolute top-[-17%] right-[-64%] size-[550px] rotate-[-30deg] bg-top-right select-none max-[376px]:right-[-83%] md:top-[-25%] md:right-[-30%] lg:right-[-19%] xl:top-[-32%] xl:right-[-9%]"
        >
            <img
                class="size-full object-cover object-top"
                src="images/gradient/gradient-1.png"
                alt="Decorative gradient background"
            />
        </figure>

        <!-- left gradient -->
        <figure
            data-ns-animate
            data-delay="0.4"
            data-duration="0.8"
            data-direction="left"
            data-offset="400"
            class="{=$hide-gradient} pointer-events-none absolute bottom-[-33%] left-[-83%] size-[728px] select-none min-[2559px]:!left-[-16%] md:bottom-[-60%] md:left-[-52%] md:size-[870px] lg:left-[-38%] xl:bottom-[-77%] xl:left-[-30%] 2xl:left-[-22%]"
        >
            <img
                class="size-full object-bottom"
                src="images/gradient/gradient-2.png"
                alt="Decorative gradient background"
            />
        </figure>
        <div class="main-container px-5">
            <div class="grid grid-cols-12 justify-between gap-x-0 gap-y-16 pt-16 pb-12 xl:pt-[90px]">
                <div class="col-span-12 xl:col-span-4">
                    <div data-ns-animate data-delay="0.3" class="max-w-[306px]">
                        <figure>
                            <img src="images/pixrup-2.svg" alt="NextSass Logo" />
                        </figure>
                        <p class="mt-4 mb-7 text-tagline-1 font-normal text-accent/60">
                            {{ footerTranslations.tagline ?? 'Get real insights in seconds' }}
                        </p>
                        <div class="flex items-center gap-3">
                            <a href="https://facebook.com/pixrup" class="footer-social-link">
                                <span class="sr-only">Facebook</span>
                                <img class="size-6" src="images/icons/facebook.svg" alt="Facebook" />
                            </a>
                            <div class="h-6 w-px bg-stroke-1/20"></div>
                            <a href="https://instagram.com/pixrup" class="footer-social-link">
                                <span class="sr-only">Instagram</span>
                                <img class="size-6" src="images/icons/instagram.svg" alt="Instagram" />
                            </a>
                            <div class="h-6 w-px bg-stroke-1/20"></div>
                            <a href="https://youtube.com/pixrup" class="footer-social-link">
                                <span class="sr-only">Youtube</span>
                                <img class="size-6" src="images/icons/youtube.svg" alt="Youtube" />
                            </a>
                            <div class="h-6 w-px bg-stroke-1/20"></div>
                            <a href="https://linkedin.com/pixrup" class="footer-social-link">
                                <span class="sr-only">LinkedIn</span>
                                <img class="size-6" src="images/icons/linkedin.svg" alt="LinkedIn" />
                            </a>
                            <div class="h-6 w-px bg-stroke-1/20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 grid grid-cols-12 gap-x-0 gap-y-8 xl:col-span-8">
                    <div class="col-span-12 md:col-span-4">
                        <div data-ns-animate data-delay="0.6" class="space-y-8">
                            <p class="text-tagline-1 font-normal text-accent sm:text-heading-6">Legal Policies</p>
                            <ul class="space-y-3 sm:space-y-5">
                                <li>
                                    <a href="terms-conditions-page.html" class="footer-link">
                                        {{ footerTranslations.terms_conditions ?? 'Terms & Conditions' }}
                                    </a>
                                </li>
                                <li>
                                    <a href="privacy-page.html" class="footer-link">
                                        {{ footerTranslations.privacy ?? 'Privacy Policy' }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative pt-[26px] pb-[100px] text-center">
                <div
                    class="footer-divider absolute top-0 right-0 left-0 mx-auto h-px w-0 origin-center bg-accent/10 dark:bg-stroke-4/10"
                ></div>
                <p
                    data-ns-animate
                    data-delay="0.7"
                    data-offset="10"
                    data-start="top 105%"
                    class="text-tagline-1 font-normal text-accent/50"
                >
                    Copyright &copy;Pixrup – smart application for modern business
                </p>
            </div>
        </div>
    </footer>
</template>
