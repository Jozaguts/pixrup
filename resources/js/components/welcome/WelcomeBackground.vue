<template>
    <div class="gradient-bg" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg">
            <defs>
                <filter id="goo">
                    <feGaussianBlur
                        in="SourceGraphic"
                        stdDeviation="10"
                        result="blur"
                    />
                    <feColorMatrix
                        in="blur"
                        mode="matrix"
                        values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -8"
                        result="goo"
                    />
                    <feBlend in="SourceGraphic" in2="goo" />
                </filter>
            </defs>
        </svg>
        <div class="gradients-container">
            <div class="g1" />
            <div class="g2" />
            <div class="g3" />
            <div class="g4" />
            <div class="g5" />
            <div class="interactive" :style="interactiveStyle" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { useMouse, useRafFn } from '@vueuse/core';
import { computed, onMounted, ref } from 'vue';

const { x, y } = useMouse();

const targetX = ref(0);
const targetY = ref(0);
const currentX = ref(0);
const currentY = ref(0);

onMounted(() => {
    targetX.value = window.innerWidth / 2;
    targetY.value = window.innerHeight / 2;
    currentX.value = targetX.value;
    currentY.value = targetY.value;
});

useRafFn(() => {
    targetX.value = x.value;
    targetY.value = y.value;

    currentX.value += (targetX.value - currentX.value) / 18;
    currentY.value += (targetY.value - currentY.value) / 18;
});

const interactiveStyle = computed(() => ({
    transform: `translate(${Math.round(currentX.value)}px, ${Math.round(currentY.value)}px)`,
}));
</script>

<style>
html,
body {
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
    overscroll-behavior-x: none;
}

#__nuxt {
    min-height: 100%;
}

.text-container {
    z-index: 100;
    width: 100vw;
    height: 100vh;
    display: flex;
    position: absolute;
    top: 0;
    left: 0;
    justify-content: center;
    align-items: center;
    font-size: 96px;
    color: #e8e8e8;
    opacity: 0.8;
    user-select: none;
    text-shadow: 1px 1px rgba(0, 0, 0, 0.1);
}

:root {
    --color-bg1: #e8e8e8;
    --color-bg2: #e8e8e8;
    --color1: 18, 113, 255;
    --color2: 221, 74, 255;
    --color3: 100, 220, 255;
    --color4: 200, 50, 50;
    --color5: 180, 180, 50;
    --color-interactive: 140, 100, 255;

    --circle-size: clamp(280px, 70vw, 820px);
    --orbit-x-large: min(400px, 35vw);
    --orbit-x-extra: min(360px, 32vw);
    --orbit-x-wide: min(640px, 55vw);
    --orbit-y-large: min(220px, 30vh);
    --blending: hard-light;
}

@keyframes moveInCircle {
    0% {
        transform: rotate(0deg);
    }
    50% {
        transform: rotate(180deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

@keyframes moveVertical {
    0% {
        transform: translateY(-50%);
    }
    50% {
        transform: translateY(50%);
    }
    100% {
        transform: translateY(-50%);
    }
}

@keyframes moveHorizontal {
    0% {
        transform: translateX(-50%) translateY(-10%);
    }
    50% {
        transform: translateX(50%) translateY(10%);
    }
    100% {
        transform: translateX(-50%) translateY(-10%);
    }
}

.gradient-bg {
    position: fixed;
    inset: 0;
    width: 100%;
    min-height: 100vh;
    overflow: hidden;
    background: linear-gradient(40deg, var(--color-bg1), var(--color-bg2));
    z-index: 0;
    pointer-events: none;

    svg {
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 0;
    }

    .gradients-container {
        filter: url(#goo) blur(90px);
        width: 100%;
        height: 100%;
    }

    .g1 {
        position: absolute;
        background: radial-gradient(
                circle at center,
                rgba(var(--color1), 0.8) 0,
                rgba(var(--color1), 0) 50%
            )
            no-repeat;
        mix-blend-mode: var(--blending);

        width: var(--circle-size);
        height: var(--circle-size);
        top: calc(50% - var(--circle-size) / 2);
        left: calc(50% - var(--circle-size) / 2);

        transform-origin: center center;
        animation: moveVertical 30s ease infinite alternate;

        opacity: 1;
    }

    .g2 {
        position: absolute;
        background: radial-gradient(
                circle at center,
                rgba(var(--color2), 0.8) 0,
                rgba(var(--color2), 0) 50%
            )
            no-repeat;
        mix-blend-mode: var(--blending);

        width: var(--circle-size);
        height: var(--circle-size);
        top: calc(50% - var(--circle-size) / 2);
        left: calc(50% - var(--circle-size) / 2);

        transform-origin: calc(50% - var(--orbit-x-large));
        animation: moveInCircle 20s reverse infinite;

        opacity: 1;
    }

    .g3 {
        position: absolute;
        background: radial-gradient(
                circle at center,
                rgba(var(--color3), 0.8) 0,
                rgba(var(--color3), 0) 50%
            )
            no-repeat;
        mix-blend-mode: var(--blending);

        width: var(--circle-size);
        height: var(--circle-size);
        top: calc(50% - var(--circle-size) / 2 + var(--orbit-y-large));
        left: calc(50% - var(--circle-size) / 2 - var(--orbit-x-extra));

        transform-origin: calc(50% + var(--orbit-x-large));
        animation: moveInCircle 36s linear infinite;

        opacity: 1;
    }

    .g4 {
        position: absolute;
        background: radial-gradient(
                circle at center,
                rgba(var(--color4), 0.8) 0,
                rgba(var(--color4), 0) 50%
            )
            no-repeat;
        mix-blend-mode: var(--blending);

        width: var(--circle-size);
        height: var(--circle-size);
        top: calc(50% - var(--circle-size) / 2);
        left: calc(50% - var(--circle-size) / 2);

        transform-origin: calc(50% - min(200px, 20vw));
        animation: moveHorizontal 40s ease infinite;

        opacity: 0.7;
    }

    .g5 {
        position: absolute;
        background: radial-gradient(
                circle at center,
                rgba(var(--color5), 0.8) 0,
                rgba(var(--color5), 0) 50%
            )
            no-repeat;
        mix-blend-mode: var(--blending);

        width: calc(var(--circle-size) * 1.6);
        height: calc(var(--circle-size) * 1.6);
        top: calc(50% - (var(--circle-size) * 0.8));
        left: calc(50% - (var(--circle-size) * 0.8));

        transform-origin: calc(50% - var(--orbit-x-wide))
            calc(50% + var(--orbit-y-large));
        animation: moveInCircle 24s ease-in-out infinite;

        opacity: 1;
    }

    .interactive {
        position: absolute;
        background: radial-gradient(
                circle at center,
                rgba(var(--color-interactive), 0.8) 0,
                rgba(var(--color-interactive), 0) 50%
            )
            no-repeat;
        mix-blend-mode: var(--blending);

        width: 100%;
        height: 100%;
        top: -50%;
        left: -50%;

        opacity: 0.7;
    }
}

@supports (height: 100dvh) {
    .gradient-bg {
        min-height: 100dvh;
    }
}

@media (max-width: 1024px) {
    :root {
        --circle-size: clamp(240px, 80vw, 720px);
        --orbit-x-large: min(320px, 32vw);
        --orbit-x-extra: min(260px, 34vw);
        --orbit-x-wide: min(520px, 65vw);
        --orbit-y-large: min(180px, 26vh);
    }
}

@media (max-width: 640px) {
    :root {
        --circle-size: clamp(220px, 92vw, 600px);
        --orbit-x-large: min(220px, 28vw);
        --orbit-x-extra: min(200px, 30vw);
        --orbit-x-wide: min(420px, 75vw);
        --orbit-y-large: min(140px, 22vh);
    }

    .gradient-bg {
        .gradients-container {
            filter: url(#goo) blur(32px);
        }

        .g1,
        .g2,
        .g4 {
            animation-duration: 28s;
        }

        .g3 {
            animation-duration: 32s;
        }

        .g5 {
            width: calc(var(--circle-size) * 1.4);
            height: calc(var(--circle-size) * 1.4);
            top: calc(50% - (var(--circle-size) * 0.7));
            left: calc(50% - (var(--circle-size) * 0.7));
        }
    }
}
</style>
