<script setup lang="ts">
import { gsap } from '@/lib/gsap';
import { onBeforeUnmount, onMounted, ref } from 'vue';

const showSubtitle = ref(false);
const caretVisible = ref(true);

const heroText = 'Turn Any Property Into Potential.';

const textEl = ref<HTMLElement | null>(null);
const caretEl = ref<HTMLElement | null>(null);

let typingTween: gsap.core.Tween | null = null;
let caretTween: gsap.core.Tween | null = null;

onMounted(() => {
    const textNode = textEl.value;
    const caretNode = caretEl.value;

    if (!textNode || !caretNode) {
        return;
    }

    showSubtitle.value = false;
    caretVisible.value = true;

    gsap.set(textNode, { text: '' });
    gsap.set(caretNode, { autoAlpha: 0, x: -8 });

    caretTween = gsap.fromTo(
        caretNode,
        { autoAlpha: 0, x: -8 },
        {
            autoAlpha: 1,
            duration: 0.5,
            repeat: -1,
            ease: 'steps(1)',
        },
    );

    typingTween = gsap.to(textNode, {
        text: { value: heroText },
        duration: heroText.length * 0.065,
        delay: 0.4,
        ease: 'none',
        onComplete: () => {
            caretTween?.kill();
            caretTween = null;
            caretVisible.value = false;
            showSubtitle.value = true;
        },
    });
});

onBeforeUnmount(() => {
    typingTween?.kill();
    typingTween = null;
    caretTween?.kill();
    caretTween = null;
});
</script>

<template>
    <section
        class="flex w-full flex-col items-center gap-4 text-center text-slate-900"
    >
        <h1
            class="mt-40 flex max-w-3xl items-baseline text-4xl leading-tight font-semibold sm:text-5xl lg:text-5xl"
        >
            <span ref="textEl" class="hero-title__text">{{ heroText }}</span>
            <span
                ref="caretEl"
                aria-hidden="true"
                class="hero-title__caret"
                v-show="caretVisible"
                >|</span
            >
        </h1>
        <div v-auto-animate="{ duration: 400 }">
            <p
                v-if="showSubtitle"
                class="max-w-2xl text-base text-slate-800 sm:text-lg lg:text-3xl"
            >
                Upload. Appraise. Reimagine. Share.
            </p>
        </div>
    </section>
</template>

<style scoped>
.hero-title__text {
    display: inline-block;
    white-space: nowrap;
}

.hero-title__caret {
    display: inline-block;
    margin-left: 0.1em;
    font-weight: 500;
    color: #6e33ff;
}
</style>
