<script lang="ts" setup>
import GuestLayout from '@/layouts/GuestLayout.vue';
import { nextTick, onMounted } from 'vue';
import { initHomeAnimations } from '@/lib/homeAnimations';
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
</script>
<template>
    <GuestLayout :can-register="props.canRegister">
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
        </template>
    </GuestLayout>
</template>
