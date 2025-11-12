<script setup lang="ts">
import AddressSearch, {
    type AddressSelection,
} from '@/components/welcome/AddressSearch.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import propertiesRoutes from '@/routes/properties';
import type { BreadcrumbItemType } from '@/types';
import { Capacitor } from '@capacitor/core';
import { importLibrary, setOptions } from '@googlemaps/js-api-loader';
import { Head, useForm } from '@inertiajs/vue3';
import {
    CalendarDays,
    Camera as CameraIcon,
    Globe,
    Image as ImageIcon,
    Loader2,
    MapPin,
    Navigation,
    Trash2,
    Upload,
} from 'lucide-vue-next';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    reactive,
    ref,
    type Ref,
} from 'vue';

type WizardStep = 'address' | 'photos' | 'summary';

type PhotoItem = {
    id: string;
    file: File;
    previewUrl: string;
    name: string;
    size: number;
};

const successToastStorageKey = 'pixrup:new-property-toast';
const form = useForm({});

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Properties', href: '/properties' },
    { title: 'New Property', href: '/properties/new' },
];

const steps: Array<{
    id: WizardStep;
    title: string;
    description: string;
}> = [
    {
        id: 'address',
        title: 'Address',
        description: 'Confirm the exact property location.',
    },
    {
        id: 'photos',
        title: 'Photos',
        description: 'Upload or capture compelling property imagery.',
    },
    {
        id: 'summary',
        title: 'Review',
        description: 'Verify every detail before creating the property.',
    },
];

const currentStepIndex = ref(0);
const currentStep = computed(() => steps[currentStepIndex.value]);

const progressPercent = computed(
    () => ((currentStepIndex.value + 1) / steps.length) * 100,
);

const addressSearchRef = ref<InstanceType<typeof AddressSearch> | null>(null);

const selectedAddress = ref<AddressSelection | null>(null);

const addressForm = reactive({
    query: '',
    error: '',
    isLocating: false,
});

const addressDetails = reactive({
    formattedAddress: '',
    city: '',
    state: '',
    postalCode: '',
    country: '',
    lat: null as number | null,
    lng: null as number | null,
    placeId: '',
});

let geocoder: google.maps.Geocoder | null = null;

const ensureGeocoder = async () => {
    if (typeof window === 'undefined' || import.meta.env.SSR) {
        throw new Error('Geocoding is unavailable in this environment.');
    }

    if (geocoder) {
        return geocoder;
    }

    const apiKey = import.meta.env.VITE_GOOGLE_MAPS_KEY;

    if (!apiKey) {
        throw new Error(
            'Google Maps API key is missing. Set VITE_GOOGLE_MAPS_KEY to enable geolocation.',
        );
    }

    setOptions({
        key: apiKey,
        libraries: ['places'],
    });

    const { Geocoder } = await importLibrary('geocoding');
    geocoder = new Geocoder();

    return geocoder;
};

const findAddressComponent = (
    components: google.maps.GeocoderAddressComponent[],
    types: string[],
) =>
    components.find((component) =>
        types.every((type) => component.types.includes(type)),
    );

const extractAddressParts = (
    components: google.maps.GeocoderAddressComponent[] = [],
) => {
    const cityComponent =
        findAddressComponent(components, ['locality']) ??
        findAddressComponent(components, ['postal_town']) ??
        findAddressComponent(components, ['administrative_area_level_2']);
    const stateComponent =
        findAddressComponent(components, ['administrative_area_level_1']) ??
        findAddressComponent(components, ['administrative_area_level_2']);
    const postalComponent = findAddressComponent(components, ['postal_code']);
    const countryComponent = findAddressComponent(components, ['country']);

    return {
        city: cityComponent?.long_name ?? '',
        state: stateComponent?.short_name ?? stateComponent?.long_name ?? '',
        postalCode: postalComponent?.long_name ?? '',
        country: countryComponent?.long_name ?? '',
    };
};

const applyAddressData = (data: {
    formattedAddress: string;
    placeId: string;
    lat: number;
    lng: number;
    city?: string;
    state?: string;
    postalCode?: string;
    country?: string;
}) => {
    addressForm.query = data.formattedAddress;
    addressDetails.formattedAddress = data.formattedAddress;
    addressDetails.placeId = data.placeId;
    addressDetails.lat = data.lat;
    addressDetails.lng = data.lng;
    addressDetails.city = data.city ?? '';
    addressDetails.state = data.state ?? '';
    addressDetails.postalCode = data.postalCode ?? '';
    addressDetails.country = data.country ?? '';
};

const handlePlaceSelected = (value: AddressSelection) => {
    selectedAddress.value = value;
    applyAddressData({
        formattedAddress: value.formattedAddress,
        placeId: value.placeId,
        lat: value.location.lat,
        lng: value.location.lng,
        city: value.city,
        state: value.state,
        postalCode: value.postalCode,
        country: value.country,
    });
    addressForm.error = '';
};

const handleAddressError = (message: string) => {
    addressForm.error = message;
};

const useCurrentLocation = async () => {
    if (!('geolocation' in navigator)) {
        addressForm.error =
            'Geolocation is not supported on this device. Please search for the address manually.';
        return;
    }

    try {
        addressForm.isLocating = true;
        addressForm.error = '';

        const position = await new Promise<GeolocationPosition>(
            (resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 12000,
                });
            },
        );

        const { latitude, longitude } = position.coords;
        const geocoderInstance = await ensureGeocoder();

        const result = await new Promise<google.maps.GeocoderResult>(
            (resolve, reject) => {
                geocoderInstance.geocode(
                    { location: { lat: latitude, lng: longitude } },
                    (results, status) => {
                        if (status === 'OK' && results && results.length > 0) {
                            resolve(results[0]);
                            return;
                        }

                        reject(
                            new Error(
                                status === 'ZERO_RESULTS'
                                    ? 'No address was found for your location.'
                                    : 'We could not confirm your location. Please try again.',
                            ),
                        );
                    },
                );
            },
        );

        const parts = extractAddressParts(result.address_components ?? []);

        const placeId =
            result.place_id ??
            `geo-${latitude.toFixed(5)}-${longitude.toFixed(5)}`;

        const selection: AddressSelection = {
            formattedAddress: result.formatted_address ?? addressForm.query,
            placeId,
            location: { lat: latitude, lng: longitude },
            city: parts.city,
            state: parts.state,
            postalCode: parts.postalCode,
            country: parts.country,
        };

        selectedAddress.value = selection;
        applyAddressData({
            formattedAddress: selection.formattedAddress,
            placeId: selection.placeId,
            lat: selection.location.lat,
            lng: selection.location.lng,
            city: selection.city,
            state: selection.state,
            postalCode: selection.postalCode,
            country: selection.country,
        });
    } catch (error) {
        addressForm.error =
            error instanceof Error
                ? error.message
                : 'We could not retrieve your current location.';
    } finally {
        addressForm.isLocating = false;
    }
};

const photoItems = ref<PhotoItem[]>([]);
const photoError = ref('');
const isProcessingPhotos = ref(false);

const fileInputRef: Ref<HTMLInputElement | null> = ref(null);
const captureInputRef: Ref<HTMLInputElement | null> = ref(null);

const createId = () =>
    typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function'
        ? crypto.randomUUID()
        : `photo-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;

const compressImage = async (file: File) => {
    if (!file.type.startsWith('image/')) {
        throw new Error('Only image formats are supported.');
    }

    if (file.size <= 1_200_000) {
        return file;
    }

    return new Promise<File>((resolve, reject) => {
        const image = new Image();
        const objectUrl = URL.createObjectURL(file);

        image.onload = () => {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');

            if (!context) {
                URL.revokeObjectURL(objectUrl);
                resolve(file);
                return;
            }

            const maxWidth = 1920;
            const maxHeight = 1280;

            let { width, height } = image;

            if (width > maxWidth || height > maxHeight) {
                const ratio = Math.min(maxWidth / width, maxHeight / height);
                width = Math.round(width * ratio);
                height = Math.round(height * ratio);
            }

            canvas.width = width;
            canvas.height = height;

            context.drawImage(image, 0, 0, width, height);

            canvas.toBlob(
                (blob) => {
                    URL.revokeObjectURL(objectUrl);

                    if (!blob) {
                        resolve(file);
                        return;
                    }

                    const compressed = new File(
                        [blob],
                        file.name.replace(/\.(png|jpe?g|webp|heic)$/i, '.jpg'),
                        {
                            type: blob.type,
                            lastModified: Date.now(),
                        },
                    );

                    resolve(compressed);
                },
                'image/jpeg',
                0.82,
            );
        };

        image.onerror = () => {
            URL.revokeObjectURL(objectUrl);
            reject(new Error('Unable to process the selected image.'));
        };

        image.src = objectUrl;
    });
};

const preparePhotoItem = async (file: File): Promise<PhotoItem> => {
    const processed = await compressImage(file);
    const previewUrl = URL.createObjectURL(processed);

    return {
        id: createId(),
        file: processed,
        previewUrl,
        name: processed.name,
        size: processed.size,
    };
};

const addPhotos = async (files: File[]) => {
    if (!files.length) {
        return;
    }

    const newItems: PhotoItem[] = [];

    for (const file of files) {
        try {
            const prepared = await preparePhotoItem(file);

            const isDuplicate = photoItems.value.some(
                (item) =>
                    item.name === prepared.name && item.size === prepared.size,
            );

            if (!isDuplicate) {
                newItems.push(prepared);
            }
        } catch (error) {
            photoError.value =
                error instanceof Error
                    ? error.message
                    : 'There was a problem processing one of the images.';
        }
    }

    if (newItems.length) {
        photoItems.value = [...photoItems.value, ...newItems];
        photoError.value = '';
    } else if (!photoItems.value.length) {
        photoError.value = 'Add at least one property photo to continue.';
    }
};

const handleFileChange = async (event: Event) => {
    const target = event.target as HTMLInputElement | null;

    if (!target?.files?.length) {
        return;
    }

    isProcessingPhotos.value = true;

    await addPhotos(Array.from(target.files));

    isProcessingPhotos.value = false;
    target.value = '';
};

const openUploadDialog = () => {
    fileInputRef.value?.click();
};

const triggerCaptureDialog = () => {
    captureInputRef.value?.click();
};

const takePhoto = async () => {
    photoError.value = '';

    if (Capacitor.isNativePlatform()) {
        try {
            isProcessingPhotos.value = true;
            const cameraModule = await import('@capacitor/camera');

            const photo = await cameraModule.Camera.getPhoto({
                resultType: cameraModule.CameraResultType.Uri,
                source: cameraModule.CameraSource.Camera,
                quality: 85,
                direction: cameraModule.CameraDirection.Rear,
                saveToGallery: false,
                allowEditing: false,
            });

            if (!photo.webPath) {
                throw new Error('Photo capture was cancelled.');
            }

            const response = await fetch(photo.webPath);
            const blob = await response.blob();

            const extension = blob.type.split('/')[1] ?? 'jpeg';

            const file = new File(
                [blob],
                `property-${Date.now()}.${extension}`,
                {
                    type: blob.type,
                    lastModified: Date.now(),
                },
            );

            await addPhotos([file]);
        } catch (error) {
            photoError.value =
                error instanceof Error
                    ? error.message
                    : 'Unable to capture a new photo right now.';
        } finally {
            isProcessingPhotos.value = false;
        }

        return;
    }

    triggerCaptureDialog();
};

const removePhoto = (id: string) => {
    const index = photoItems.value.findIndex((item) => item.id === id);

    if (index === -1) {
        return;
    }

    URL.revokeObjectURL(photoItems.value[index].previewUrl);
    photoItems.value.splice(index, 1);

    if (!photoItems.value.length) {
        photoError.value = 'Add at least one property photo to continue.';
    }
};

onBeforeUnmount(() => {
    photoItems.value.forEach((item) => URL.revokeObjectURL(item.previewUrl));
});

const isAddressStepValid = computed(
    () =>
        Boolean(addressDetails.placeId) &&
        addressDetails.lat !== null &&
        addressDetails.lng !== null,
);

const isPhotosStepValid = computed(
    () => photoItems.value.length > 0 && !isProcessingPhotos.value,
);

const isSummaryValid = computed(
    () => isAddressStepValid.value && photoItems.value.length > 0,
);

const creationPreview = computed(() =>
    new Date().toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }),
);

const formatCoordinate = (value: number | null) =>
    value === null ? '—' : value.toFixed(5);

const formatFileSize = (size: number) => {
    const mb = size / (1024 * 1024);
    if (mb >= 1) {
        return `${mb.toFixed(1)} MB`;
    }

    return `${Math.ceil(size / 1024)} KB`;
};

const goToStep = async (index: number) => {
    if (index < 0 || index > steps.length - 1) {
        return;
    }

    currentStepIndex.value = index;

    if (steps[index].id === 'address') {
        await nextTick();
        addressSearchRef.value?.focus();
    }
};

const submissionError = ref<string | null>(null);
const isSubmitting = computed(() => form.processing);

const submitProperty = async () => {
    if (!isSummaryValid.value) {
        submissionError.value =
            'Complete the previous steps before creating the property.';
        return;
    }

    const lat = addressDetails.lat;
    const lng = addressDetails.lng;

    if (lat === null || lng === null) {
        submissionError.value =
            'We were unable to read the property coordinates. Please confirm the address again.';
        await goToStep(0);
        return;
    }

    submissionError.value = null;

    const formData = new FormData();
    formData.append('address', addressDetails.formattedAddress);
    formData.append('city', addressDetails.city);
    formData.append('state', addressDetails.state);
    formData.append('postal_code', addressDetails.postalCode);
    formData.append('country', addressDetails.country);
    formData.append('lat', String(lat));
    formData.append('lng', String(lng));
    formData.append('place_id', addressDetails.placeId);

    photoItems.value.forEach((photo, index) => {
        formData.append(`photos[${index}]`, photo.file, photo.file.name);
    });

    const storeRoute = propertiesRoutes.store.post();

    form.clearErrors();
    form.transform(() => formData).submit(storeRoute.method, storeRoute.url, {
        preserveScroll: false,
        onSuccess: () => {
            submissionError.value = null;
            sessionStorage.setItem(
                successToastStorageKey,
                'Property successfully created 🎉',
            );
        },
        onError: (errors) => {
            const firstError = Object.values(errors ?? {})[0];
            submissionError.value =
                typeof firstError === 'string'
                    ? firstError
                    : 'Unable to create the property.';
        },
        onFinish: () => {
            form.transform((data) => data);
        },
    });
};

const handleBack = async () => {
    if (isSubmitting.value || currentStepIndex.value === 0) {
        return;
    }

    await goToStep(currentStepIndex.value - 1);
};

const handleNext = async () => {
    switch (currentStep.value.id) {
        case 'address': {
            if (!isAddressStepValid.value) {
                addressForm.error = 'Select a valid address before continuing.';
                return;
            }

            await goToStep(currentStepIndex.value + 1);
            break;
        }
        case 'photos': {
            if (!isPhotosStepValid.value) {
                photoError.value =
                    'Add at least one property photo to continue.';
                return;
            }

            await goToStep(currentStepIndex.value + 1);
            break;
        }
        case 'summary': {
            await submitProperty();
            break;
        }
    }
};

const nextLabel = computed(() =>
    currentStep.value.id === 'summary' ? 'Create Property' : 'Next',
);

const isNextDisabled = computed(() => {
    if (isSubmitting.value) {
        return true;
    }

    switch (currentStep.value.id) {
        case 'address':
            return !isAddressStepValid.value;
        case 'photos':
            return !isPhotosStepValid.value;
        case 'summary':
            return !isSummaryValid.value;
    }

    return true;
});
</script>

<template>
    <Head title="New Property" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <section
            class="relative flex min-h-[calc(100vh-8rem)] w-full flex-1 flex-col gap-6 rounded-none px-5 pt-6 pb-28 shadow-none shadow-neu-in md:mx-auto md:max-w-5xl md:rounded-[32px] md:px-10 md:pt-10 md:pb-10 md:shadow-[24px_24px_48px_rgba(207,213,235,0.6),-24px_-24px_48px_rgba(255,255,255,0.9)] lg:max-w-6xl"
        >
            <header class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <p
                        class="text-xs tracking-wide text-[#9ca3af] uppercase md:text-sm"
                    >
                        Guided wizard
                    </p>
                    <h1
                        class="text-3xl font-semibold tracking-tight text-[#1f2933] md:text-4xl"
                    >
                        Create a new property
                    </h1>
                    <p class="text-sm text-[#6b7280] md:text-base">
                        Complete the three-step flow to add a property with
                        accurate location, rich media, and validated data.
                    </p>
                </div>

                <div class="flex flex-col gap-4">
                    <div
                        class="neu-surface relative h-2 w-full rounded-full shadow-neu-in"
                    >
                        <div
                            class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#7C4DFF] via-[#a68dff] to-[#7C4DFF] transition-[width]"
                            :style="{ width: `${progressPercent}%` }"
                        />
                    </div>
                    <div
                        class="grid gap-3 text-sm text-[#6b7280] md:grid-cols-3 md:gap-4"
                    >
                        <article
                            v-for="(step, index) in steps"
                            :key="step.id"
                            class="neu-surface flex flex-col gap-1 rounded-3xl p-4 shadow-neu-out transition-all duration-200"
                            :class="{
                                'text-[#1f2933] shadow-[inset_12px_12px_24px_rgba(200,206,224,0.35),inset_-12px_-12px_24px_rgba(255,255,255,0.9)]':
                                    index === currentStepIndex,
                            }"
                        >
                            <span
                                class="text-xs font-semibold tracking-wide text-[#9ca3af] uppercase"
                            >
                                Step {{ index + 1 }}
                            </span>
                            <span class="text-base font-semibold">
                                {{ step.title }}
                            </span>
                            <span class="text-xs text-[#9ca3af] md:text-sm">
                                {{ step.description }}
                            </span>
                        </article>
                    </div>
                </div>
            </header>

            <Transition name="fade-slide" mode="out-in">
                <section
                    v-if="currentStep.id === 'address'"
                    key="address-step"
                    class="neu-surface flex flex-1 flex-col gap-6 rounded-[28px] bg-[#f4f5fa] p-6 shadow-neu-out md:p-10"
                >
                    <div class="flex flex-col gap-2">
                        <h2
                            class="text-2xl font-semibold text-[#1f2933] md:text-3xl"
                        >
                            Step 1 — Address
                        </h2>
                        <p class="text-sm text-[#6b7280] md:text-base">
                            Search the property via Google Places or use your current location to auto-fill every field.
                        </p>
                    </div>

                    <div class="flex flex-col gap-4">
                        <AddressSearch
                            ref="addressSearchRef"
                            v-model="addressForm.query"
                            variant="neumorphic"
                            @place-selected="handlePlaceSelected"
                            @error="handleAddressError"
                        />
                        <div class="flex flex-col gap-3 md:flex-row">
                            <button
                                type="button"
                                class="neu-btn flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3 font-semibold text-[#6b7280] transition-all duration-200 hover:text-[#1f2933] md:w-auto"
                                :disabled="addressForm.isLocating"
                                @click="useCurrentLocation"
                            >
                                <Navigation class="size-4" />
                                {{
                                    addressForm.isLocating
                                        ? 'Locating…'
                                        : 'Use current location'
                                }}
                            </button>
                        </div>
                        <p
                            v-if="addressForm.error"
                            class="text-sm text-[#B91C1C]"
                        >
                            {{ addressForm.error }}
                        </p>
                    </div>

                    <div
                        v-if="isAddressStepValid"
                        class="grid gap-4 md:grid-cols-2"
                    >
                        <div
                            class="neu-surface flex flex-col gap-1 rounded-3xl bg-[#f4f5fa] p-5 shadow-neu-in"
                        >
                            <span
                                class="text-xs tracking-wide text-[#9ca3af] uppercase"
                            >
                                Address
                            </span>
                            <p class="font-semibold text-[#1f2933]">
                                {{ addressDetails.formattedAddress }}
                            </p>
                        </div>
                        <div
                            class="neu-surface flex flex-col gap-2 rounded-3xl bg-[#f4f5fa] p-5 shadow-neu-in"
                        >
                            <span
                                class="text-xs tracking-wide text-[#9ca3af] uppercase"
                            >
                                City & State
                            </span>
                            <p class="font-semibold text-[#1f2933]">
                                {{ addressDetails.city || '—' }},
                                {{ addressDetails.state || '—' }}
                            </p>
                            <p class="text-xs text-[#9ca3af]">
                                {{
                                    addressDetails.country ||
                                    'Country not available'
                                }}
                            </p>
                        </div>
                        <div
                            class="neu-surface flex flex-col gap-3 rounded-3xl bg-[#f4f5fa] p-5 shadow-neu-in md:col-span-2"
                        >
                            <div class="flex items-center gap-2 text-[#6b7280]">
                                <Globe class="size-4" />
                                <span class="text-xs tracking-wide uppercase">
                                    Coordinates
                                </span>
                            </div>
                            <div
                                class="flex flex-wrap gap-4 text-sm text-[#1f2933]"
                            >
                                <span class="neu-surface rounded-2xl px-3 py-2">
                                    Lat:
                                    {{ formatCoordinate(addressDetails.lat) }}
                                </span>
                                <span class="neu-surface rounded-2xl px-3 py-2">
                                    Lng:
                                    {{ formatCoordinate(addressDetails.lng) }}
                                </span>
                                <span
                                    class="neu-surface rounded-2xl px-3 py-2 text-xs truncate text-ellipsis tracking-wide text-[#9ca3af] uppercase"
                                >
                                    Place ID: {{ addressDetails.placeId }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    v-else-if="currentStep.id === 'photos'"
                    key="photos-step"
                    class="neu-surface flex flex-1 flex-col gap-6 rounded-[28px] bg-[#f4f5fa] p-6 shadow-neu-out md:p-10"
                >
                    <div class="flex flex-col gap-2">
                        <h2
                            class="text-2xl font-semibold text-[#1f2933] md:text-3xl"
                        >
                            Step 2 — Photos
                        </h2>
                        <p class="text-sm text-[#6b7280] md:text-base">
                            Upload crisp images or snap new photos from your device. We automatically compress every file to keep uploads light.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 md:flex-row">
                        <button
                            type="button"
                            class="neu-btn flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3 font-semibold text-[#6b7280] transition-all duration-200 hover:text-[#1f2933] md:w-auto"
                            :disabled="isProcessingPhotos"
                            @click="openUploadDialog"
                        >
                            <Upload class="size-4" />
                            Upload from device
                        </button>
                        <button
                            type="button"
                            class="neu-btn flex w-full items-center justify-center gap-2 rounded-2xl bg-[#7C4DFF] px-4 py-3 font-semibold text-white shadow-[12px_12px_24px_rgba(78,47,155,0.35),-12px_-12px_24px_rgba(152,117,255,0.45)] transition-all duration-200 hover:shadow-[inset_8px_8px_18px_rgba(78,47,155,0.35),inset_-8px_-8px_18px_rgba(152,117,255,0.35)] md:w-auto"
                            :disabled="isProcessingPhotos"
                            @click="takePhoto"
                        >
                            <CameraIcon class="size-4" />
                            Take photo
                        </button>
                    </div>

                    <input
                        ref="fileInputRef"
                        type="file"
                        accept="image/*"
                        multiple
                        class="hidden"
                        @change="handleFileChange"
                    />
                    <input
                        ref="captureInputRef"
                        type="file"
                        accept="image/*"
                        capture="environment"
                        class="hidden"
                        @change="handleFileChange"
                    />

                    <div
                        class="neu-surface flex flex-1 flex-col gap-4 rounded-[24px] bg-[#f4f5fa] p-6 shadow-neu-in"
                    >
                        <div
                            v-if="!photoItems.length && !isProcessingPhotos"
                            class="flex flex-1 flex-col items-center justify-center gap-3 text-center text-[#9ca3af]"
                        >
                            <ImageIcon class="size-8" />
                            <p class="text-sm md:text-base">
                                Add photos to showcase your property. Wide angles with good lighting work best.
                            </p>
                        </div>

                        <div
                            v-else
                            class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                        >
                            <div
                                v-for="photo in photoItems"
                                :key="photo.id"
                                class="relative overflow-hidden rounded-[24px] shadow-[12px_12px_24px_rgba(200,206,224,0.5),-12px_-12px_24px_rgba(255,255,255,0.9)]"
                            >
                                <img
                                    :src="photo.previewUrl"
                                    :alt="photo.name"
                                    class="h-48 w-full rounded-t-[24px] object-cover"
                                />
                                <div
                                    class="flex items-center justify-between gap-2 px-4 py-3 text-xs text-[#6b7280]"
                                >
                                    <div class="flex flex-col">
                                        <span
                                            class="font-semibold text-[#1f2933]"
                                        >
                                            {{ photo.name }}
                                        </span>
                                        <span>{{
                                            formatFileSize(photo.size)
                                        }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        class="neu-btn inline-flex items-center justify-center rounded-xl bg-[#fde2e1] p-2 text-[#B91C1C] shadow-[6px_6px_16px_rgba(252,226,225,0.6),-6px_-6px_16px_rgba(255,255,255,0.95)] transition-all hover:shadow-[inset_6px_6px_16px_rgba(252,226,225,0.6),inset_-6px_-6px_16px_rgba(255,255,255,0.95)]"
                                        @click="removePhoto(photo.id)"
                                    >
                                        <Trash2 class="size-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="isProcessingPhotos"
                            class="flex items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm text-[#6b7280] shadow-[inset_12px_12px_24px_rgba(200,206,224,0.35),inset_-12px_-12px_24px_rgba(255,255,255,0.9)]"
                        >
                            <Loader2
                                class="size-4 animate-spin text-[#7C4DFF]"
                            />
                            Processing images…
                        </div>
                    </div>

                    <p v-if="photoError" class="text-sm text-[#B91C1C]">
                        {{ photoError }}
                    </p>
                </section>

                <section
                    v-else
                    key="summary-step"
                    class="neu-surface flex flex-1 flex-col gap-6 rounded-[28px] bg-[#f4f5fa] p-6 shadow-neu-out md:p-10"
                >
                    <div class="flex flex-col gap-2">
                        <h2
                            class="text-2xl font-semibold text-[#1f2933] md:text-3xl"
                        >
                            Step 3 — Confirmation
                        </h2>
                        <p class="text-sm text-[#6b7280] md:text-base">
                            Review the details before creating the property. If you need to adjust anything, return to previous steps without losing data.
                        </p>
                    </div>

                    <div
                        class="neu-surface flex flex-col gap-5 rounded-[28px] bg-[#f4f5fa] p-6 shadow-neu-in md:p-8"
                    >
                        <div class="flex flex-col gap-3">
                            <span
                                class="text-xs tracking-wide text-[#9ca3af] uppercase"
                            >
                                Address
                            </span>
                            <p class="text-lg font-semibold text-[#1f2933]">
                                {{ addressDetails.formattedAddress || '—' }}
                            </p>
                            <p class="text-sm text-[#6b7280]">
                                {{ addressDetails.city || '—' }},
                                {{ addressDetails.state || '—' }}
                                {{
                                    addressDetails.postalCode
                                        ? `· ${addressDetails.postalCode}`
                                        : ''
                                }}
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div
                                class="flex flex-col gap-2 px-4 py-3 text-sm text-[#6b7280] shadow-neu-in"
                            >
                                <div
                                    class="flex items-center gap-2 text-[#1f2933]"
                                >
                                    <MapPin class="size-4" />
                                    <span class="font-semibold"
                                        >Coordinates</span
                                    >
                                </div>
                                <span
                                    >Lat:
                                    {{
                                        formatCoordinate(addressDetails.lat)
                                    }}</span
                                >
                                <span
                                    >Lng:
                                    {{
                                        formatCoordinate(addressDetails.lng)
                                    }}</span
                                >
                            </div>
                            <div
                                class="s flex flex-col gap-2 px-4 py-3 text-sm text-[#6b7280] shadow-neu-in"
                            >
                                <div
                                    class="flex items-center gap-2 text-[#1f2933]"
                                >
                                    <CalendarDays class="size-4" />
                                    <span class="font-semibold">Created</span>
                                </div>
                                <span>{{ creationPreview }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3">
                            <span
                                class="text-xs tracking-wide text-[#9ca3af] uppercase"
                            >
                                Photos
                            </span>
                            <div
                                class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3"
                            >
                                <div
                                    v-for="photo in photoItems"
                                    :key="photo.id"
                                    class="overflow-hidden shadow-neu-in"
                                >
                                    <img
                                        :src="photo.previewUrl"
                                        :alt="photo.name"
                                        class="h-36 w-full object-cover"
                                    />
                                    <div
                                        class="flex items-center justify-between gap-2 px-4 py-3 text-xs text-[#6b7280]"
                                    >
                                        <span
                                            class="font-semibold text-[#1f2933]"
                                        >
                                            {{ photo.name }}
                                        </span>
                                        <span>{{
                                            formatFileSize(photo.size)
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p
                            v-if="submissionError"
                            class="rounded-2xl border border-[#fca5a5] bg-[#fee2e2] px-4 py-3 text-sm text-[#b91c1c]"
                        >
                            {{ submissionError }}
                        </p>
                    </div>
                </section>
            </Transition>

            <div
                class="fixed right-6 bottom-6 left-6 z-20 flex flex-col gap-3 rounded-[28px] bg-[#f4f5fa]/95 p-4 backdrop-blur md:static md:flex-row md:items-center md:justify-between md:bg-transparent md:p-0 md:backdrop-blur-none"
            >
                <button
                    type="button"
                    class="neu-btn flex items-center justify-center rounded-2xl px-6 py-3 font-semibold text-[#6b7280] transition-all duration-200 hover:text-[#1f2933]"
                    :class="{
                        'pointer-events-none opacity-40':
                            currentStepIndex === 0 || isSubmitting,
                    }"
                    @click="handleBack"
                >
                    Back
                </button>
                <button
                    type="button"
                    class="neu-btn relative flex items-center justify-center gap-2 px-6 py-3 font-semibold text-white"
                    :class="{
                        'pointer-events-none opacity-60': isNextDisabled,
                    }"
                    :disabled="isNextDisabled"
                    @click="handleNext"
                >
                    <Loader2
                        v-if="isSubmitting"
                        class="size-4 animate-spin text-white"
                    />
                    {{ isSubmitting ? 'Creating…' : nextLabel }}
                </button>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition:
        opacity 0.25s ease,
        transform 0.25s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(12px);
}
</style>
