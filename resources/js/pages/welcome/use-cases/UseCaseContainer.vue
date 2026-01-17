<script setup lang="ts">
import UseCaseCard from '@/pages/welcome/use-cases/UseCaseCard.vue';
import { computed } from 'vue';
import { useLandingTranslations } from '@/composables/useLandingTranslations';

const landingTranslations = useLandingTranslations();
const useCasesTranslations = computed(
    () => landingTranslations.value.use_cases ?? {},
);
</script>

<template>
    <section
        id="use-cases"
        class="pt-7 pb-14 md:pb-16 lg:pb-20 xl:pb-[100px]"
        :aria-label="useCasesTranslations.aria_label ?? 'Use cases'"
    >
        <div class="main-container" >
            <div class="space-y-[70px]">
                <div class="max-w-[780px] mx-auto text-center space-y-3 mb-[70px] ">
                    <span data-ns-animate class="badge badge-primary">{{
                        useCasesTranslations.badge ?? 'Use cases'
                    }}</span>
                    <h2 data-ns-animate data-delay="0.2">
                        {{
                            useCasesTranslations.title ??
                            'Real-world scenarios, real AI results.'
                        }}
                    </h2>
                    <p data-ns-animate data-delay="0.3" >
                        {{
                            useCasesTranslations.description ??
                            'Explore how Pixrup is used across different workflows—each case shows real outputs, from AI transformations to market intelligence and reports, displayed through visual galleries of actual results.'
                        }}
                    </p>
                </div>
            </div>
            <use-case-card />
        </div>
    </section>
</template>

<style scoped></style>
