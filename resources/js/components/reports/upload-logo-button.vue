<template>
    <div class="bg-gray-200 relative w-24 h-24 border-2 border-dashed hover:font-semibold rounded-[12px] flex flex-col items-center justify-center cursor-pointer border-black hover:bg-gray-300">
        <input
            type="file"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            @change="handleFileUpload"
        />
        <Icon icon="mdi:image-plus-outline" class="w-8 h-8" />
        <span class="text-sm">New logo</span>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
const emit = defineEmits<{
    (e: 'logo-uploaded', file: File): void;
}>();
const file = ref<File | null>(null);
function handleFileUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    file.value = target.files?.[0] || null;
    if (file.value) {
        emit('logo-uploaded', file.value);
    }
    target.value = '';
}
</script>
