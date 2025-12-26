<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { computed, nextTick, onMounted } from 'vue';
import { initHomeAnimations } from '@/lib/homeAnimations';
import { usePage } from '@inertiajs/vue3';
import HeadingBlock from '@/pages/features/HeadingBlock.vue';
import FeatureListBlock from '@/pages/features/FeatureListBlock.vue';
import TextBlock from '@/pages/features/TextBlock.vue';
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
const { props: pageProps } = usePage();
const content = computed(() => {
    return pageProps?.feature?.content ?? [];
});
const resolveBlock = (type: string) => {
    switch (type) {
        case 'h2':
        case 'heading':
            return HeadingBlock;

        case 'p':
        case 'text':
            return TextBlock;

        case 'feature_list':
        case 'list':
            return FeatureListBlock;

        default:
            return null;
    }
};
</script>

<template>
    <GuestLayout :can-register="props.canRegister" title="Service details">
        <template #main>
            <section class="z-[9999] pt-32 sm:pt-36 md:pt-42 xl:pt-[180px]" aria-label="Page hero section">
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
                                >Features</a
                            >
                        </span>
                        <h1 data-ns-animate data-delay="0.2" class="font-normal !text-primary lg:text-heading-2">
                            {{ pageProps?.feature?.title }}
                        </h1>
                    </div>
                </div>
            </section>
            <section class="pb-24 md:pb-36 lg:pb-44 xl:pb-[200px]">
                <div class="main-container">
                    <div class="flex items-start lg:gap-[72px]">
                        <div class="w-full max-w-full lg:max-w-[767px]">
                            <div class="services-details-content mb-[72px]">
                                <component
                                    v-for="(block, idx) in content"
                                    :key="idx"
                                    :is="resolveBlock(block.type)"
                                    :block="block"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>
    </GuestLayout>
</template>
