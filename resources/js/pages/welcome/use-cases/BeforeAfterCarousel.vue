<script setup lang="ts">
import { ref } from 'vue'
import { Swiper, SwiperSlide} from 'swiper/vue'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination';
import BeforeAfterCard from '@/pages/welcome/use-cases/BeforeAfterCard.vue';

const images = [
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
    { before: '/images/useCases/before.png', after: '/images/useCases/after.png' },
]
const activeIndex = ref(0)
const fullscreen = ref(false)

const onSlideChange = (swiper: any) => {
    activeIndex.value = swiper.realIndex
}

const openFullscreen = (index: number) => {
    activeIndex.value = index
    fullscreen.value = true
}
</script>
<template>
    <Swiper
        :slides-per-view="3"
        :space-between="24"
        :centered-slides="false"
        :pagination="{ el: '.pagination-bullets', clickable: true, type: 'bullets', }"
        :breakpoints="{
      0: { slidesPerView: 1 },
      1024: { slidesPerView: 3 }
    }"
        @slideChange="onSlideChange"
        class="w-full h-[420px]"
    >
        <SwiperSlide
            v-for="(item, index) in images"
            :key="index"
            @click="openFullscreen(index)"
        >
            <BeforeAfterCard
                :before="item.before"
                :after="item.after"
                :active="activeIndex === index"
            />
        </SwiperSlide>
    </Swiper>

    <!-- Fullscreen -->
    <Teleport to="body">
        <div
            v-if="fullscreen"
            class="fixed inset-0 bg-black z-[9999]"
        >
            <Swiper
                :initial-slide="activeIndex"
                :slides-per-view="1"
                navigation
                class="w-full h-full"
            >
                <SwiperSlide
                    v-for="(item, index) in images"
                    :key="index"
                >
                    <BeforeAfterCard
                        :before="item.before"
                        :after="item.after"
                        :active="true"
                    />
                </SwiperSlide>
            </Swiper>

            <!-- Close -->
            <button
                class="absolute top-6 right-6 text-white text-3xl"
                @click="fullscreen = false"
            >
                ✕
            </button>
        </div>
    </Teleport>
</template>
