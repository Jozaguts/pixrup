<script setup lang="ts">
import NeuInput from '@/components/NeuInput.vue';
import { Icon } from '@iconify/vue';

const modelValue = defineModel('value', {
    type: String,
    required: true,
    default: '',
});
</script>

<template>
    <NeuInput
        v-model="modelValue"
        icon="ph:file-search-light"
        placeholder="Search logos..."
        @keydown.esc="$emit('update:value','')"
    >
        <button class="text-black" v-if="modelValue !== ''">
            <Icon
                icon="mdi:times"
                class="h-5 w-5 text-gray-400"
                @click="modelValue = ''"
            />
        </button>
    </NeuInput>
</template>

<style scoped></style>
