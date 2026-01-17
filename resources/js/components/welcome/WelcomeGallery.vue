<script setup lang="ts">
import { initHomeAnimations } from '@/lib/homeAnimations';
import { computed, nextTick, onMounted } from 'vue';
import type { ServiceCard } from '@/components/template/services/types';
import Card from '@/components/template/services/Card.vue';
import { useLandingTranslations } from '@/composables/useLandingTranslations';

const props = defineProps<{
    listings: ServiceCard[];
}>();
const landingTranslations = useLandingTranslations();
const featuresTranslations = computed(
    () => landingTranslations.value.features ?? {},
);
onMounted(async () => {
    await nextTick();
    await initHomeAnimations();
});
</script>

<template>
    <section id="features" class="pt-0 pb-20 md:pt-0 md:pb-[220px] lg:pt-[0px] xl:pt-[0px] xl:pb-[100px]">
        <div class="main-container mt-20 md:mt-[200px]">
            <div class="mb-[70px] space-y-5 text-center">
                <span data-ns-animate data-delay="0.2" class="badge badge-primary">{{
                    featuresTranslations.badge ?? 'Features'
                }}</span>
                <div class="space-y-3">
                    <h2 data-ns-animate data-delay="0.3" class="mx-auto max-w-[878px]">
                        {{
                            featuresTranslations.title ??
                            'AI-powered creation, built for speed.'
                        }}
                    </h2>
                    <p data-ns-animate data-delay="0.4" class="mx-auto max-w-[700px]">
                        {{
                            featuresTranslations.description ??
                            'Pixrup helps you generate, transform, and deliver on-brand content in minutes—secure, collaborative, and PWA-ready for any team.'
                        }}
                    </p>
                </div>
            </div>
            <p class="text-primary">
                {{
                    featuresTranslations.note ??
                    'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci, eveniet.'
                }}
            </p>
            <div class="grid grid-cols-12 gap-y-5 md:gap-8 xl:gap-8">
                <Card
                    v-for="(card, idx) in listings"
                    :key="idx"
                    :title="card.title"
                    :excerpt="card?.excerpt"
                    :slug="card?.slug"
                    :cta="featuresTranslations.cta ?? 'Read more'"
                    :icon="card?.icon"
                />
            </div>
        </div>
    </section>
</template>
