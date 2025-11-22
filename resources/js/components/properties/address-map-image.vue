<script setup lang="ts">
import {computed, ComputedRef} from 'vue';
import { Icon } from '@iconify/vue';
 const apiKey = import.meta.env.VITE_GOOGLE_MAPS_KEY;
 const props = defineProps<{
  lat: number|null;
  lng: number|null;
  formattedAddress?: string;
 }>();

const imageUrl:ComputedRef<string> = computed(() => `https://maps.googleapis.com/maps/api/staticmap?center=${props.lat},${props.lng}&zoom=15&size=400x200&markers=${props.lat},${props.lng}&key=${apiKey}`);
</script>

<template>
    <div v-if="props.lat && props.lng" class="relative p-2
      overflow-hidden rounded-[12px] npo-form-shadow">
        <img :src="imageUrl" :alt="imageUrl" class="w-full h-full"/>
        <div class="absolute left-3 top-3 p-2 npo-form-shadow border-dashed rounded-[12px]">
            <p class="text-sm">
                <strong>Latitude:</strong> {{ props.lat }}
            </p>
            <p class="text-sm">
                <strong>Longitude:</strong> {{ props.lng }}
            </p>
        </div>
        <div class="absolute top-6 right-4">
            <a
                target="_blank"
                :href="`https://www.google.com/maps?q=${props.lat},${props.lng}`"
                class="npo-form-shadow px-3 py-4 rounded-[12px]">
                Open in Google Maps
                <Icon icon="logos:google-maps" class="inline-block !w-4 !h-4 ml-1"
                />
            </a>
        </div>
    </div>
</template>

<style scoped></style>
