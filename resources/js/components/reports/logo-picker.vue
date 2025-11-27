<template>
    <div class="mx-auto w-full p-8 sm:w-full sm:p-2 md:max-w-3xl">
        <!-- Title -->
        <h1 class="sm:text-md mb-6 font-bold md:text-3xl">
            Choose a logo for your report
        </h1>

        <NeuInput
            v-model="searchQuery"
            icon="mdi:image-search-outline"
            placeholder="Search logos..."
        />

        <div
            v-if="error"
            class="border border-black rounded-xl p-4 flex items-start gap-3
           bg-black/20 text-black shadow-md my-2"
        >
            <span class="font-semibold">Error loading logos</span>
            <p class="text-sm opacity-90">{{ error.response.data.message}}</p>

            <button
                class="ml-auto text-black hover:text-black/60"
                @click="$emit('close')"
            >
                ✕
            </button>
        </div>
        <div
            v-if="uploadErorr"
            class="border border-black rounded-xl p-4 flex items-start gap-3
           bg-black/20 text-black shadow-md my-2"
        >
            <span class="font-semibold">Error uploading image</span>
            <p class="text-sm opacity-90">{{ uploadErorr.response.data.message}}</p>

            <button
                class="ml-auto text-black hover:text-black/60"
                @click="$emit('close')"
            >
                ✕
            </button>
        </div>


<!-- Avatar Grid -->
        <div
            v-auto-animate
            class="align-center mt-6 flex flex-row flex-wrap justify-start overflow-hidden sm:w-full sm:gap-0.5 md:mx-auto md:w-10/12 md:gap-4"
        >
            <UploadButton @logo-uploaded="handleUpload" key="upload-button" />
            <template v-if="isLoading">
                <div
                    v-for="i in 4"
                    :key="i"
                    class="pointer-events-none relative flex h-24 w-24 animate-pulse items-center justify-center overflow-hidden rounded-[12px] border-8 border-white bg-gray-300"
                >
                    <Icon
                        icon="line-md:downloading-loop"
                        class="h-8 w-8 z-20"
                        mode="svg"
                    />
                </div>
            </template>
            <template v-else>
                <div
                    v-for="(avatar, i) in filteredAvatars"
                    :key="avatar.uri"
                    class="relative flex h-24 w-24 cursor-pointer items-center justify-center-safe overflow-hidden rounded-[12px] border-8 border-white transition-all"
                >
                    <input
                        class="absolute inset-0 z-20 opacity-0"
                        type="radio"
                        name="avatar"
                        :value="avatar.uri"
                        @change.prevent="selectedAvatar = avatar.uri"
                    />
                    <img
                        :alt="`img-${i}`"
                        :src="avatar.uri"
                        class="h-full w-full object-cover z-10"
                    />
                    <span v-if="selectedAvatar == avatar.uri" class="bg-white/20 p-3 absolute top-1 left-1 h-10 w-10 z-20">
                        <Icon
                            icon="mdi:check-circle-outline"
                            class="h-6 w-6 text-black/50"
                        />
                    </span>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Icon } from '@iconify/vue';
import UploadButton from '@/components/reports/upload-logo-button.vue';
import NeuInput from '@/components/NeuInput.vue';
import { useStoreLogoMutation } from '@/queries/store-logo';
import { useFetchLogos } from '@/queries/fetch-logos';

const file = ref<File | null>(null);
const { data, error, isLoading, refetch } = useFetchLogos();
const { mutate, error: uploadErorr } = useStoreLogoMutation({
    file,
    onSucess: refetch
});
const selectedAvatar = ref('');

function handleUpload(img: File) {
    file.value = img;
    mutate();
}

const searchQuery = ref('');

const filteredAvatars = computed(() => {
    if (!data) {
        return [];
    }
    if (!searchQuery.value) {
        return data.value?.logos;
    }
    return data.value?.logos?.filter((avatar) =>
        avatar.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
});
</script>
