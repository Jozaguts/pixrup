<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watchEffect } from 'vue';
import { gsap } from '@/lib/gsap';
import { useMediaQuery } from '@vueuse/core';
import { useLandingTranslations } from '@/composables/useLandingTranslations';

const { before, after, fullscreen, animation } = defineProps<{
    before: string;
    after: string;
    fullscreen: boolean;
    animation: boolean;
}>();

const beforeLayer = ref<HTMLDivElement | null>(null);
let tl: gsap.core.Timeline;
const isMobile = useMediaQuery('(max-width: 600px)');
const landingTranslations = useLandingTranslations();
const useCasesTranslations = computed(
    () => landingTranslations.value.use_cases ?? {},
);
const testimonial = computed(
    () => useCasesTranslations.value.testimonial ?? {},
);

onMounted(() => {
    if (!beforeLayer.value && animation) return;
    const duration = isMobile.value ? 6 : 4
    gsap.set(beforeLayer.value, {
        clipPath: 'inset(0 100% 0 0)',
        willChange: 'clip-path',
    })
    tl = gsap.timeline({
        repeat: -1,
        yoyo: true,
    })

    tl.to(beforeLayer.value, {
        clipPath: 'inset(0 10% 0 0)', // 90% visible
        duration,
        ease: 'power2.inOut',
    })
});

onBeforeUnmount(() => {
    tl?.kill();
});

watchEffect(() => {
    if (!animation) {
        tl?.kill()
        tl = null as  unknown as gsap.core.Timeline;

        // reset limpio
        if (beforeLayer.value) {
            gsap.set(beforeLayer.value, {
                clipPath: 'inset(0 100% 0 0)',
            })
        }
    }
})
</script>
<template>
    <div
        class="relative w-full min-w-[300px] overflow-hidden rounded-[12px]"
        :class="fullscreen ? 'h-full' : 'h-[420px]'"
    >
        <!-- AFTER -->
        <img
            :src="after"
            class="absolute inset-0 h-full w-full object-cover"
            :alt="useCasesTranslations.after_alt ?? 'After image'"
        />

        <!-- BEFORE -->
        <div ref="beforeLayer" class="absolute inset-0">
            <img
                :src="before"
                class="h-full w-full object-cover"
                :alt="useCasesTranslations.before_alt ?? 'Before image'"
            />
        </div>

        <!-- Footer -->
        <div
            v-if="!fullscreen"
            class="absolute bottom-0 left-0 z-[10] h-[20%] w-full bg-surface dark:bg-[#1c2230] px-4"
        >
            <div class="mt-2 flex items-center gap-3">
                <div class="flex size-11 items-center justify-center rounded-full bg-surface">
                    <span>{{ testimonial.initials ?? 'DS' }}</span>
                </div>
                <div class="text-left">
                    <p class="text-lg font-medium text-secondary dark:text-accent">
                        {{ testimonial.name ?? 'Darrell Steward' }}
                    </p>
                    <p class="text-tagline-2">
                        "{{ testimonial.role ?? 'Head of Operations, Finlytics' }}"
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
