<script setup lang="ts">
    import { cn } from '@/lib/utils';

    const props = defineProps({
        class: {
            type: String,
            default: '',
        },
        disableShadow: {
            type: Boolean,
            default: false,
        },
    });
</script>
<template>
<div :class="cn(
    'grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 rounded-[12px] bg-gray-200',
    !props.disableShadow && 'shadow-neu-in',
    props.class
)">
    <slot></slot>
</div>
</template>
