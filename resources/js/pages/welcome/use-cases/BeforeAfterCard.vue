<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { gsap } from '@/lib/gsap';

const {before, after} = defineProps<{
    before: string;
    after: string;
}>();

const beforeLayer = ref<HTMLDivElement | null>(null);
let tl: gsap.core.Timeline;

onMounted(() => {
    if (!beforeLayer.value) return;
    gsap.set(beforeLayer.value, { width: '100%' });
    const duration = window.innerWidth < 768 ? 1.2 : 2;
    tl = gsap.timeline({
        repeat: -1,
        yoyo: true,
    });

    tl.fromTo(beforeLayer.value, { width: '0%' }, { width: '90%', duration, ease: 'power2.inOut' });

});

onBeforeUnmount(() => {
    tl?.kill();
});
</script>
<template>
    <div class="relative h-[300px] w-full min-w-[300px] overflow-hidden rounded-xl md:h-[420px]">
        <!-- AFTER -->
        <img :src="after" class="absolute inset-0 h-full w-full object-cover" alt="after image"/>

        <!-- BEFORE -->
        <div ref="beforeLayer" class="absolute inset-0 overflow-hidden">
            <img :src="before" class="h-full w-full object-cover" alt="before image"/>
        </div>
    </div>
</template>
