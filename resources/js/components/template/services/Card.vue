<script setup lang="ts">
import type { ServiceCard } from '@/components/template/services/types';

const { title, excerpt, slug, cta, icon = 'ns-shape-47' } = defineProps<ServiceCard>();
</script>

<template>
    <div data-ns-animate data-delay="0.5" class="col-span-12 md:col-span-12 xl:col-span-6">
        <div
            class="neu-card grid items-center justify-center space-y-4 rounded-[12px] px-12 py-4 text-center transition-transform duration-500 ease-in-out hover:translate-y-[-10px]"
        >
            <div class="flex items-center justify-center">
                <span :class="icon" class="text-[52px] text-secondary dark:text-accent"></span>
            </div>
            <div class="space-y-2">
                <h3 class="text-heading-5">{{ title }}</h3>
                <p class="mx-auto max-w-[361px]">
                    {{ excerpt }}
                </p>
            </div>
            <div>
                <a
                    :href="'features/' + slug"
                    class="btn-gray-200 btn btn-md border-1 border-accent hover:btn-secondary dark:btn-transparent dark:hover:btn-accent"
                    ><span>{{ cta }}</span></a
                >
            </div>
        </div>
    </div>
</template>

<style scoped></style>
