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
        class="max-w flex w-full flex-col items-center text-center text-white"
        v-auto-animate="{ duration: 400 }"
    >
        <div class="typewriter">
            <h1 class="hero-title">
                <span
                    class="hero-title__text mt-8 text-[4vw] whitespace-nowrap sm:text-[2vw] md:text-[2.5vw] lg:text-[4vw]"
                    @animationend="handleTypingComplete"
                >
                    Turn Any Property Into Potential.
                </span>
                <span v-if="caretActive" class="hero-title__caret" />
            </h1>
        </div>

        <p
            v-if="showSubtitle"
            class="hero-subtitle whitespace-wrap mb-3 text-base text-[12px] leading-relaxed font-medium text-black sm:text-[1vw] md:mb-10 md:text-[1.5vw] lg:mb-10 lg:text-[4vw]"
        >
            Upload. Appraise. Reimagine. Share.
        </p>

        <div
            class="relative mb-1 w-full max-w-xl px-6 py-4 text-slate-900 md:mb-10 lg:mb-10"
        >
            <Input
                id="welcome-query"
                v-model="query"
                type="search"
                placeholder=" Find your fixer in any city"
                class="is-pressed h-12 w-full text-base text-slate-900"
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
    align-items: baseline;
    gap: 0.15em;
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
    width: 4px;
    height: calc(1em - 8px);
    background: #6e33ff;
    animation: blink-caret 0.5s step-end infinite;
    animation-delay: 0.6s;
}

.hero-subtitle {
    transition:
        opacity 0.4s ease,
        transform 0.4s ease;
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
    .hero-title__caret {
        width: 2px;
        height: 15px !important;
    }
}
</style>
