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
    <section :class="cn('flex flex-col md:px-5 sm:px-0 w-full p-2', props.sectionClass)">
        <header v-if="props.title || props.description" class="flex flex-col gap-2">
            <h2 :class="cn('text-lg font-semibold md:text-xl text-accent',props.titleClass)">{{props.title}}</h2>
            <p :class="cn('text-sm text-accent/50',props.descriptionClass)">
               {{props.description}}
            </p>
        </header>
        <div :class="cn('', props.class)">
            <slot/>
        </div>
    </section>
</template>
