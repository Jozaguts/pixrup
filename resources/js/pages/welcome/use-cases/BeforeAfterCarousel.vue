<script setup lang="ts">
import { computed, ref } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import BeforeAfterCard from '@/pages/welcome/use-cases/BeforeAfterCard.vue';
import { Pagination, EffectCoverflow, Autoplay, Navigation } from 'swiper/modules';
import BeforeAfterFullscreenCard from '@/pages/welcome/use-cases/BeforeAfterFullscreenCard.vue';
import { Icon } from '@iconify/vue';
import { useMediaQuery } from '@vueuse/core';

const images = [
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/after.png', after: '/images/useCases/before.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/after.png', after: '/images/useCases/before.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
];
const activeIndex = ref(0);
const fullscreen = ref(false);

const onSlideChange = (swiper: any) => {
    activeIndex.value = swiper.realIndex;
};
const isMobile = useMediaQuery('(max-width: 600px)');
const openFullscreen = (index: number) => {
    activeIndex.value = index;
    fullscreen.value = true;
};
const modules = computed(() => {
    return window.innerWidth < 768
        ? [Pagination, Navigation]
        :[Pagination, EffectCoverflow, Autoplay]
});
</script>
<template>
    <Swiper
        :modules="modules"
        :effect="'coverflow'"
        :grabCursor="true"
        :navigation="isMobile"
        :centeredSlides="false"
        :slidesPerView="isMobile ? 1 : 'auto'"
        :coverflowEffect="{ rotate: 50, stretch: 0, depth: 100, modifier: 1, slideShadows: true }"
        :pagination="{ clickable: true }"
        :space-between="30"
        :autoplay="{ delay: 2500, disableOnInteraction: false }"
        :breakpoints="{ 0: { slidesPerView: 1 }, 1024: { slidesPerView: 3 } }"
        @slideChange="onSlideChange"
        class="w-full"
    >
        <SwiperSlide v-for="(item, index) in images" :key="index" @click="openFullscreen(index)">
            <BeforeAfterCard :before="item.before" :after="item.after" :fullscreen="false" :animation="true" />
        </SwiperSlide>
    </Swiper>

    <!-- Fullscreen -->
    <Teleport to="body">
        <div v-if="fullscreen" class="fixed inset-0 z-[9999] bg-black">
            <Swiper
                slidesPerView="auto"
                :spaceBetween="30"
                :navigation="true"
                :modules="[Navigation]"
                class="custom-swiper"
            >
                <SwiperSlide v-for="(item, index) in images" :key="index" class="custom-slide h-full">
                    <BeforeAfterFullscreenCard :before="item.before" :after="item.after" />
                </SwiperSlide>
            </Swiper>
            <div class="absolute top-[8px] right-0 z-[9999] rounded-[100%] p-4 text-primary/80">
                <Icon
                    icon="material-symbols:cancel-outline-rounded"
                    class="!h-10 !w-10 cursor-pointer"
                    @click="fullscreen = false"
                />
            </div>
        </div>
    </Teleport>
</template>
<style>
.custom-swiper > .swiper-button-prev,
.custom-swiper > .swiper-button-next {
    color: var(--primary);
}
.custom-slide.swiper-slide img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.custom-slide.swiper-slide {
    width: 90%;
    height: 100vh;
}
</style>
