<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { ref } from 'vue';

const query = ref('');
const showSubtitle = ref(false);
const caretActive = ref(true);

const handleTypingComplete = (event: AnimationEvent) => {
    console.log(event.animationName);
    if (event.animationName === 'typing') {
        showSubtitle.value = true;
        caretActive.value = false;
    }
};
</script>

<template>
    <section
        class="flex w-full  flex-col items-center text-center max-w text-white"
        v-auto-animate="{duration: 400}"
    >
        <div class="typewriter">
            <h1 class="hero-title">
                <span
                    class="hero-title__text"
                    @animationend="handleTypingComplete"
                >
                    Turn Any Property Into Potential.
                </span>
                <span v-if="caretActive" class="hero-title__caret" />
            </h1>
        </div>

        <p
            v-if="showSubtitle"
            class="hero-subtitle mb-10 max-w-2xl font-medium text-base leading-relaxed text-black"
        >
            Upload. Appraise. Reimagine. Share.
        </p>

        <div
            class="relative mb-16 w-full max-w-xl  px-6 py-4 text-slate-900"
        >
            <label
                for="welcome-query"
                class="mb-3 block text-sm font-medium text-white/80"
            >

            </label>
            <Input
                id="welcome-query"
                v-model="query"
                type="search"
                placeholder=" Find your fixer in any city"
                class="h-12 w-full rounded-[12px] border border-white/30 bg-white/95 text-base text-slate-900 placeholder:text-slate-500 focus-visible:ring-0.5 focus-visible:black-400"
            />
        </div>
    </section>
</template>

<style>
.typewriter {
    display: flex;
    justify-content: center;
    width: 100%;
    max-width: fit-content;
    margin: 0 auto 1.75rem;
}

.hero-title {
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 0.15em;
    font-family: 'Mona Sans', sans-serif;
    font-size: clamp(62px, 4vw, 140px);
    font-weight: 600;
    letter-spacing: 4px;
    color: #0a0a0a;
    text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.1);
}

.hero-title__text {
    display: inline-block;
    overflow: hidden;
    white-space: nowrap;
    animation: typing 3.5s steps(54, end) forwards;
    animation-delay: 0.6s;
}

.hero-title__caret {
    display: inline-block;
    width: 6px;
    height: 0.82em;
    background: #6e33ff;
    animation: blink-caret 0.5s step-end infinite;
    animation-delay: 0.6s;
}

.hero-subtitle {
    transition: opacity 0.4s ease, transform 0.4s ease;
    opacity: 1;
    transform: translateY(0);
    font-size: 35px;
}

@keyframes typing {
    from {
        width: 0;
    }
    to {
        width: 100%;
    }
}

@keyframes blink-caret {
    0%,
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}

@media (max-width: 640px) {
    .hero-title {
        font-size: clamp(32px, 8vw, 60px);
        letter-spacing: 3px;
    }
}
</style>
