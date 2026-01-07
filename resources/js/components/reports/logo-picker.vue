<script setup lang="ts">
import LogoCollection from '@/components/reports/logo-collection.vue';
import AddLogoButton from '@/components/image-upload-button.vue';
import LogoSkeleton from '@/components/reports/logo-skeleton.vue';
import LogoSearchbar from '@/components/reports/logo-searchbar.vue';
import PiniaColadaError from "@/PiniaColadaError.vue";
import { computed, ref } from 'vue';
import { useStoreLogoMutation } from '@/queries/store-logo';
import useFetchLogos from '@/queries/fetch-logos';
import LogoSchema from '@/schemas/image/logos';
import Swal from 'sweetalert2';
import {AxiosError} from "axios";
import {VerticalStepItem} from "@/types";
import {PropType} from "vue";
type StepItem = VerticalStepItem & { id: number };

const props = defineProps({
    maxLogos:{
        type: Number,
        default:15
    },
    step: {
        type: Object as PropType<StepItem>,
        required: true,
    },
});
const shouldRenderAddLogoButton = computed(() => {
    return visibleLogos.value?.length < props.maxLogos;
});

const file = ref<File | null>(null);
const emit = defineEmits(['change', 'upload-success']);
const { data, isLoading, refetch, error } = useFetchLogos();
const { mutate, error:mutationError } = useStoreLogoMutation({
    file,
    onSucess: ()=> {
        refetch();
        Swal.fire({
            icon: 'success',
            title: 'Upload Successful',
            text: 'Your logo has been uploaded successfully.',
            toast: true,
            position: 'top-end',
            timer: 5000,
            timerProgressBar: true,
            showConfirmButton: false,
            showCloseButton:true,
            iconColor: 'rgba(0, 0, 0, 0.7)',
        });
        emit('upload-success');
    },
});
const selectedAvatar = ref('');
const searchQuery = ref('');
const visibleLogos = computed(() => {
    if (!data.value || !data.value.logos) return [];
    if (!searchQuery.value) return data.value.logos;

    const search = searchQuery.value.toLowerCase();

    return data.value.logos.filter((item) => {
        return item.name.toLowerCase().includes(search);
    });
});

function handleUpload(payload:any) {
    file.value = new File([payload.file], payload.name);
    mutate();
}
function handleImagePick(img: { uri: string; name: string }) {
    selectedAvatar.value = img.name;
    emit('change', img);
}
</script>

<template>
    <div class="w-full px-2 md:px-5">
        <LogoSearchbar v-model:value="searchQuery" />

        <p class="text-accent/60 px-4 text-xs mt-4" v-if="visibleLogos.length > 0">
            You can upload up to logos {{ maxLogos }} to your personal gallery ({{ visibleLogos.length }} uploaded).
        </p>

        <!-- Avatar Grid -->
        <div
            v-auto-animate
            class="align-center w-full md:w-10/12 mt-4 md:mt-6 flex flex-row flex-wrap justify-center-safe md:justify-start overflow-hidden sm:w-full gap-4 mx-0 md:mx-auto"
        >
            <AddLogoButton :schema="LogoSchema" @logo-uploaded="handleUpload"
                key="upload-button" label="new logo"
                v-if="shouldRenderAddLogoButton"
            />
            <!-- Images Skeleton -->
            <LogoSkeleton v-if="isLoading" :size="8" class="m-2" />
            <!-- Images Display -->
            <LogoCollection
                v-if="visibleLogos.length" :items="visibleLogos"
                :active-item="selectedAvatar" @onChange="handleImagePick"
            />
        </div>
    </div>

    <PiniaColadaError :error="mutationError as AxiosError" />
    <PiniaColadaError :error="error as AxiosError" />
</template>
