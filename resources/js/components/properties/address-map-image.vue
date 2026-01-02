<script setup lang="ts">
import { computed, ComputedRef } from 'vue';
import { Icon } from '@iconify/vue';
const apiKey = import.meta.env.VITE_GOOGLE_MAPS_KEY;
const props = defineProps<{
    lat: number | null;
    lng: number | null;
    formattedAddress?: string;
}>();

const imageUrl: ComputedRef<string> = computed(
    () =>
        `https://maps.googleapis.com/maps/api/staticmap?center=${props.lat},${props.lng}&zoom=20&size=400x100&markers=${props.lat},${props.lng}&key=${apiKey}`,
);
</script>

<template>
    <div
        v-if="props.lat && props.lng"
        class="relative mt-8 overflow-hidden rounded-[12px] bg-background p-4 shadow-neu-in"
    >
        <img :src="imageUrl" :alt="imageUrl" class="h-full min-h-[200px] w-full rounded-[12px] object-cover" />
        <div
            class="bg-surface shadow-neu-in  absolute top-6 left-6 hidden rounded-[12px] border-dashed p-4 text-accent md:block lg:block"
        >
            <p class="my-1 text-sm text-accent"><strong>Latitude:</strong> {{ props.lat }}</p>
            <p class="my-2 text-sm text-accent"><strong>Longitude:</strong> {{ props.lng }}</p>
        </div>
        <div class="absolute bg-surface top-6 mt-4 right-6 hidden md:block lg:block">
            <a
                target="_blank"
                :href="`https://www.google.com/maps?q=${props.lat},${props.lng}`"
                class="neu-button rounded-[12px] px-3 py-4"
            >
                Open in Google Maps
                <Icon icon="logos:google-maps" class="ml-1 inline-block !h-4 !w-4" />
            </a>
        </div>
    </div>
</template>

<style scoped></style>
