<script setup lang="ts">
    import { defineProps } from 'vue';
    import { cn } from '@/lib/utils';
    const props = defineProps< {
        title?: string;
        description? : string;
        class? : string;
        titleClass? : string;
        descriptionClass? : string;
        sectionClass? : string;
    }>();

</script>

<template>
    <section :class="cn('flex flex-col md:px-5 sm:px-0 w-full flex-1 p-2 overflow-scroll', props.sectionClass)">
        <header v-if="props.title || props.description" class="flex flex-col gap-2">
            <h2 :class="cn('text-lg font-semibold md:text-xl text-black',props.titleClass)">{{props.title}}</h2>
            <p :class="cn('text-sm text-[#6b7280]',props.descriptionClass)">
               {{props.description}}
            </p>
        </header>
        <div :class="cn('border-b-2 border-gray-200 pb-6', props.class)">
            <slot/>
        </div>
    </section>
</template>
