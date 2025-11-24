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
    'flex flex-row flex-wrap items-start justify-center-safe rounded-[12px] gap-4 py-10 bg-gray-200',
    !props.disableShadow && 'shadow-neu-in',
    props.class
)">
    <slot></slot>
</div>
</template>
