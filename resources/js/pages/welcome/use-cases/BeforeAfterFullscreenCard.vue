<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { gsap } from '@/lib/gsap'
import { useLandingTranslations } from '@/composables/useLandingTranslations';

const props = defineProps<{
    before: string
    after: string
}>()

const container = ref<HTMLDivElement | null>(null)
const beforeLayer = ref<HTMLDivElement | null>(null)
const landingTranslations = useLandingTranslations();
const useCasesTranslations = computed(
    () => landingTranslations.value.use_cases ?? {},
);

let setClip: (value: string) => void

const updateReveal = (clientX: number) => {
    if (!container.value) return

    const rect = container.value.getBoundingClientRect()
    const progress = Math.min(Math.max((clientX - rect.left) / rect.width, 0), 1)
    const right = 100 - progress * 100

    setClip(`inset(0 ${right}% 0 0)`)
}

onMounted(() => {
    if (!beforeLayer.value || !container.value) return

    gsap.set(beforeLayer.value, {
        clipPath: 'inset(0 100% 0 0)',
        willChange: 'clip-path',
    })

    setClip = gsap.quickSetter(beforeLayer.value, 'clipPath')

    // Desktop
    container.value.addEventListener('mousemove', (e) => {
        updateReveal(e.clientX)
    })

    // Mobile
    container.value.addEventListener('touchmove', (e) => {
        updateReveal(e.touches[0].clientX)
    })
})

onBeforeUnmount(() => {
    container.value?.replaceWith(container.value.cloneNode(true))
})
</script>

<template>
    <div
        ref="container"
        class="relative  w-full h-full overflow-hidden touch-none cursor-pointer"
    >
        <!-- AFTER -->
        <img
            :src="after"
            class="absolute inset-0 w-full h-full object-cover"
            :alt="useCasesTranslations.after_alt ?? 'After image'"
        />

        <!-- BEFORE -->
        <div ref="beforeLayer" class="absolute inset-0">
            <img
                :src="before"
                class="w-full h-full object-cover"
                :alt="useCasesTranslations.before_alt ?? 'Before image'"
            />
        </div>
    </div>
</template>
