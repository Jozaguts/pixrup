<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { ref } from 'vue';

const query = ref('');
const showSubtitle = ref(false);

const handleTypingComplete = (event: AnimationEvent) => {
    if (event.animationName === 'typing') {
        showSubtitle.value = true;
    }
};
</script>

<template>
    <section
        class="flex w-full  flex-col items-center text-center text-white"
    >
        <div class="typewriter">
            <h1 class="hero-title" @animationend="handleTypingComplete">
                Turn Old Homes.. into Dream Homes..with AI. Instantly.
            </h1>
        </div>

        <p
            v-if="showSubtitle"
            class="hero-subtitle mb-10 max-w-2xl font-medium text-base leading-relaxed text-white/80"
        >
            Pixrup helps real-estate pros reveal serious potentials, closing
            faster, and giving memorable experiences with true Ai Magic.
        </p>

        <div
            class="relative mb-16 w-full max-w-xl rounded-[12px] border border-white/20 bg-white/20 px-6 py-4 text-slate-900 shadow-[0_8px_40px_rgba(130,87,230,0.25)] backdrop-blur-lg"
        >
            <label
                for="welcome-query"
                class="mb-3 block text-sm font-medium text-white/80"
            >
                Find your fixer in a flash
            </label>
            <Input
                id="welcome-query"
                v-model="query"
                type="search"
                placeholder="Search by city, style, or listing"
                class="h-12 w-full rounded-[12px] border border-white/30 bg-white/95 text-base text-slate-900 placeholder:text-slate-500 focus-visible:ring-2 focus-visible:ring-purple-400"
            />
        </div>
    </section>
</template>

<style scoped>
.typewriter {
    display: flex;
    justify-content: center;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 1.75rem;
}

.hero-title {
    overflow: hidden;
    border-right: 0.15em solid #f97316;
    white-space: nowrap;
    margin: 0 auto;
    display: inline-block;
    padding-right: 0.15em;
    letter-spacing: 0.12em;
    font-family: 'Darker Grotesque', sans-serif;
    font-size: clamp(32px, 2vw, 70px);
    font-weight: 600;
    text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.1);
    animation: typing 3.5s steps(54, end), blink-caret 0.75s step-end infinite;
    animation-fill-mode: forwards;
    color: #0a0a0a;
}

.hero-subtitle {
    transition: opacity 0.4s ease, transform 0.4s ease;
    opacity: 1;
    transform: translateY(0);
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
    from,
    to {
        border-color: transparent;
    }
    50% {
        border-color: #f97316;
    }
}

@media (max-width: 640px) {
    .hero-title {
        font-size: clamp(28px, 7vw, 46px);
        letter-spacing: 0.08em;
    }
}
</style>
