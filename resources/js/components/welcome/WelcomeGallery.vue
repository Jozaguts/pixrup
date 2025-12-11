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
        class="pt-0 pb-[220px] sm:pb-14 md:pt-0 md:pb-16 lg:pt-[0px] lg:pb-[88px] xl:pt-[0px] xl:pb-[100px]"
    >
        <div class="main-container mt-[200px]">
            <div class="text-center space-y-5 mb-[70px]">
                <span data-ns-animate data-delay="0.2" class="badge badge-primary">Our Services</span>
                <div class="space-y-3">
                    <h2 data-ns-animate data-delay="0.3" class="max-w-[878px] mx-auto">
                        AI-powered creation, built for speed.
                    </h2>
                    <p data-ns-animate data-delay="0.4" class="max-w-[700px] mx-auto">
                        Pixrup helps you generate, transform, and deliver
                        on-brand content in minutes—secure, collaborative,
                        and PWA-ready for any team.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
