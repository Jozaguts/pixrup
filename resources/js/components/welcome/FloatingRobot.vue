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
        { y: 0, opacity: 1, scale: 1, duration: 1.25, ease: 'power3.out' }
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
        class="hidden md:block lg:block bg-[url('../images/Robot.png')] top-0 right-0 bg-no-repeat bg-top mt-[5rem] bg-contain z-10 w-[30vw] h-full absolute pointer-events-none"
    />
</template>
