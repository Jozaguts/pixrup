<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{ block: any }>();

const attrs = computed(() => {
    if (!props.block.data.animate) return {};
    return {
        'data-ns-animate': '',
        'data-delay': props.block.data.delay,
    };
});
</script>

<template>
    <h2 v-bind="attrs">
        {{ block.data.value }}
    </h2>
</template>

<style scoped></style>
