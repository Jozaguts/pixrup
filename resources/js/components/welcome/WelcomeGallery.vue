<script setup lang="ts">
import { initHomeAnimations } from '@/lib/homeAnimations';
import { nextTick, onMounted } from 'vue';
import type { ServiceCard } from '@/components/template/services/types';
import Card from '@/components/template/services/Card.vue';

defineProps<{
    listings: ServiceCard[];
}>();
onMounted(async () => {
    await nextTick();
    await initHomeAnimations();
});
</script>

<template>
    <section
        id="features"
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
            <div class="grid grid-cols-12 xl:gap-8 md:gap-8 gap-y-5">
                <Card v-for="(card, idx) in listings" :key="idx" :title="card.title" :details="card.details" :link="card.link" :cta="card.cta" :shape="card.shape"/>
            </div>
        </div>
    </section>
</template>
