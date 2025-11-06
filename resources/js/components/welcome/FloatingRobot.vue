<script setup lang="ts">
import { gsap } from '@/lib/gsap';
import { onBeforeUnmount, onMounted, ref } from 'vue';

const bgRef = ref<HTMLElement | null>(null);
let loopTimeline: gsap.core.Timeline | null = null;

onMounted(() => {
    if (!bgRef.value) {
        return;
    }
    gsap.fromTo(
        bgRef.value,
        { y: 40, opacity: 0, scale: 0.95 },
        { y: 0, opacity: 1, scale: 1, duration: 1.25, ease: 'power3.out' },
    );
});

onBeforeUnmount(() => {
    loopTimeline?.kill();
    loopTimeline = null;
});
</script>

<template>
    <div
        ref="bgRef"
        class="pointer-events-none absolute top-0 right-0 z-10 mt-[5rem] hidden h-full w-[30vw] bg-[url('../images/Robot.png')] bg-contain bg-top bg-no-repeat md:block lg:block"
    />
</template>
