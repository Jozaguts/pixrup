<template>
    <div class="relative h-[400px] overflow-hidden p-4 sm:w-full">
        <div class="property-card relative h-full w-full overflow-hidden rounded-[12px] bg-background p-2.5 shadow-sm">
            <img
                :src="props?.item?.thumbnail"
                @error="setBackupThumbnail"
                alt="Property Image"
                class="h-full w-full rounded-[12px] object-cover"
            />

            <div class="absolute inset-2.5 top-1/2 bg-background transition-all ease-in-out">
                <h3
                    class="line-clamp-2 px-2 pt-4 font-kulim text-lg font-semibold text-accent"
                    :title="props.item?.title"
                >
                    {{ props?.item?.title }}
                </h3>

                <div class="relative mt-3 flex flex-row items-center justify-between px-2">
                    <div class="flex flex-wrap items-center gap-4 text-sm text-accent/50">
                        <div class="flex flex-col">
                            <span class="text-xs tracking-wide text-accent/50 uppercase"> Estimated value </span>
                            <span class="font-semibold">_ &nbsp;{{ props.item?.estimatedValue }}</span>
                        </div>
                        <!---->
                    </div>

                    <span
                        class="active inline-flex items-center rounded-sm px-4 py-2 text-xs font-medium text-accent/50 ring shadow-neu-in ring-white"
                    >
                        {{ props.item?.status || 'pending' }}
                    </span>
                </div>

                <div class="mt-3 flex flex-row items-center justify-between px-2">
                    <a
                        :href="props.item?.links?.view"
                        class="neu-button mr-2 flex-1 rounded-[12px] px-4 py-3 text-center text-sm font-semibold"
                    >
                        Explore property
                    </a>

                    <button
                        @click="isLiked = !isLiked"
                        class="group relative flex h-8 w-8 cursor-pointer items-center justify-center rounded-full shadow-neu-in"
                    >
                        <!-- Default icon -->
                        <Icon v-if="!isLiked" icon="mdi:cards-heart-outline" class="h-5 w-5 text-accent" />

                        <!-- Hover icon -->
                        <Icon v-else icon="mdi:heart" class="hidden h-5 w-5 text-accent group-hover:inline-block" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { Ref, ref } from 'vue';
const props = defineProps({ item: Object });

const isLiked: Ref<boolean> = ref(props.item?.like);
const setBackupThumbnail = (e: Event) => {
    (e.target as HTMLImageElement).src = 'https://picsum.photos/200/300?grayscale';
};
</script>

<style scoped></style>
