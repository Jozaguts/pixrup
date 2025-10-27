<script setup lang="ts">
import { importLibrary, setOptions } from '@googlemaps/js-api-loader';
import { Input } from '@/components/ui/input';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

type AddressLocation = {
    lat: number;
    lng: number;
};

export type AddressSelection = {
    formattedAddress: string;
    placeId: string;
    location: AddressLocation;
};

const emit = defineEmits<{
    (e: 'place-selected', value: AddressSelection): void;
    (e: 'error', value: string): void;
}>();

const modelValue = defineModel<string>({ default: '' });

const inputRef = ref<InstanceType<typeof Input> | HTMLInputElement | null>(null);
const isLoading = ref(false);
const errorMessage = ref('');

let autocomplete: google.maps.places.Autocomplete | null = null;
let placeListener: google.maps.MapsEventListener | undefined;

const placeholder = computed(() =>
    isLoading.value ? 'Loading Google Places…' : 'Search an address…',
);

const initializeAutocomplete = async () => {
    if (typeof window === 'undefined' || import.meta.env.SSR) {
        return;
    }

    const apiKey = import.meta.env.VITE_GOOGLE_MAPS_KEY;

    if (!apiKey) {
        const message =
            'Google Maps API key is missing. Set VITE_GOOGLE_MAPS_KEY to enable address search.';
        errorMessage.value = message;
        emit('error', message);
        return;
    }

    const targetElement =
        inputRef.value instanceof HTMLInputElement
            ? inputRef.value
            : // eslint-disable-next-line @typescript-eslint/no-explicit-any
              ((inputRef.value as any)?.$el as HTMLInputElement | undefined);

    if (!targetElement) {
        return;
    }

    try {
        isLoading.value = true;
        setOptions({
            key: apiKey,
            libraries: ['places'],
        });

        const { Autocomplete } = await importLibrary('places');

        autocomplete = new Autocomplete(targetElement, {
            types: ['address'],
            fields: ['formatted_address', 'geometry', 'place_id'],
        });

        placeListener = autocomplete.addListener('place_changed', () => {
            const place = autocomplete?.getPlace();

            if (!place) {
                return;
            }

            const formattedAddress =
                place.formatted_address ?? modelValue.value ?? '';
            const placeId = place.place_id ?? '';
            const location = place.geometry?.location;

            if (!location || !formattedAddress || !placeId) {
                const message =
                    'Unable to fetch address details from Google Places.';
                errorMessage.value = message;
                emit('error', message);
                return;
            }

            errorMessage.value = '';

            emit('place-selected', {
                formattedAddress,
                placeId,
                location: {
                    lat: location.lat(),
                    lng: location.lng(),
                },
            });
        });
    } catch (error) {
        const message =
            error instanceof Error
                ? error.message
                : 'Unexpected error loading Google Places.';
        errorMessage.value = message;
        emit('error', message);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    void initializeAutocomplete();
});

onBeforeUnmount(() => {
    placeListener?.remove();
    placeListener = undefined;
    autocomplete = null;
});
</script>

<template>
    <div class="flex w-full max-w-xl flex-col gap-2">
        <label class="sr-only" for="address-search">Search for an address</label>
        <Input
            id="address-search"
            ref="inputRef"
            v-model="modelValue"
            type="search"
            :placeholder="placeholder"
            :disabled="isLoading"
            autocomplete="off"
            autocapitalize="off"
            spellcheck="false"
            class="h-14 w-full rounded-xl border border-slate-200 bg-white text-base text-slate-900 shadow-sm focus-visible:ring-2 focus-visible:ring-purple-500"
        />
        <p v-if="errorMessage" class="text-sm text-red-500">
            {{ errorMessage }}
        </p>
    </div>
</template>
