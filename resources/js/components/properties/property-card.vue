<template>
    <div class="md:w-[350px] sm:w-full h-[400px] p-4 relative overflow-hidden group">
        <div class="rounded-[24px] overflow-hidden relative bg-background p-2.5 property-card h-full w-full shadow-sm">
            <img
                :src="props?.item?.thumbnail"
                @error="setBackupThumbnail"
                alt="Property Image"
                class="w-full h-full object-cover rounded-[24px]"
            />

            <div class="group-hover:inline-block hidden bg-background absolute inset-2.5 top-1/2 transition-all ease-in-out">
                <h3 class="line-clamp-2 text-lg font-semibold font-kulim text-black px-2 pt-4" :title="props.item?.title">
                    {{ props?.item?.title }}
                </h3>

                <div class="flex flex-row justify-between items-center px-2 mt-3 relative">
                    <div class="flex flex-wrap items-center gap-4 text-sm text-[#475569]">
                        <div class="flex flex-col">
                            <span class="text-xs tracking-wide text-[#9CA3AF] uppercase"> Estimated value </span>
                            <span class="font-semibold">_ &nbsp;$108K</span>
                        </div><!---->
                    </div>

                    <span class="shadow-neu-in active inline-flex items-center rounded-sm px-2 py-1 text-xs font-medium text-black ring ring-white">
                        {{props.item?.status || 'pending'}}
                    </span>
                </div>

                <div class="flex flex-row justify-between items-center px-2 mt-3">
                    <a
                        :href="props.item?.links?.view"
                        class="text-sm neu-button rounded-[12px] text-center px-4 py-3 flex-1 font-semibold mr-2"
                    >
                        Explore property
                    </a>

                    <button  @click=" isLiked= !isLiked" class="cursor-pointer group relative w-8 h-8 shadow-neu-in rounded-full flex items-center justify-center">
                        <!-- Default icon -->
                        <Icon v-if="!isLiked"
                            icon="mdi:cards-heart-outline"
                            class="w-5 h-5 text-black"
                        />

                        <!-- Hover icon -->
                        <Icon v-else
                            icon="mdi:heart"
                            class="w-5 h-5 text-black hidden group-hover:inline-block"
                        />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Icon } from '@iconify/vue';
    import {Ref, ref} from "vue";
    const  props = defineProps({item:Object});

    const isLiked:Ref<boolean> =ref(props.item?.like );
    const setBackupThumbnail = (e:Event) => {
        (e.target as HTMLImageElement).src = 'https://picsum.photos/200/300?grayscale';
    }
</script>

<style scoped>
</style>
