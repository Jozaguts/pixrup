<script setup lang="ts">
    import { defineProps } from 'vue';
    import { cn } from '@/lib/utils';
    const props = defineProps< {
        title?: string;
        description? : string;
        class? : string;
    }>();

</script>

<template>
    <section
        class="flex flex-col"
    >
        <header v-if="props.title || props.description" class="flex flex-col gap-2">
            <h2 class="text-lg font-semibold md:text-xl">{{props.title}}</h2>
            <p class="text-sm text-[#6b7280]">
               {{props.description}}
            </p>
        </header>
        <div :class="cn('border-b-2 border-gray-200 pb-6', props.class)">
            <slot/>
        </div>
    </section>
</template>
