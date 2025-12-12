<script setup lang="ts">
import type { ServiceCard } from '@/components/template/services/types';


const {title, details, link, cta,shape = 'ns-shape-47'} = defineProps<ServiceCard>()
</script>

<template>
    <div data-ns-animate data-delay="0.5" class="col-span-12 md:col-span-12 xl:col-span-6">
        <div
            class="neu-card  px-12 py-4 rounded-[12px]  space-y-4 text-center grid items-center justify-center hover:translate-y-[-10px] transition-transform duration-500 ease-in-out"
        >
            <div class="flex items-center justify-center">
                <span :class="shape" class="text-[52px] text-secondary dark:text-accent"></span>
            </div>
            <div class="space-y-2">
                <h3 class="text-heading-5">{{title}}</h3>
                <p class="max-w-[361px] mx-auto">
                    {{details}}
                </p>
            </div>
            <div>
                <a
                    :href="link"
                    class="btn btn-gray-200 border-accent border-1 dark:btn-transparent dark:hover:btn-accent hover:btn-secondary btn-md"
                ><span>{{cta}}</span></a
                >
            </div>
        </div>
    </div>
</template>

<style scoped></style>
