<script setup lang="ts">
import { onMounted } from 'vue';
type Elements = {
    darkIcon: HTMLElement | null;
    lightIcon: HTMLElement | null;
    toggleBtn: HTMLElement | null;
    html: HTMLElement | null;
};
type AnimationConfig = {
    duration: number;
    delay: number;
    ease: string;
};
type ThemeSwitcher = {
    elements: Elements;
    animationConfig: AnimationConfig;
    init: () => void;
    cacheElements: () => void;
    setInitialTheme: () => void;
    bindEvents: () => void;
    setTheme: (theme: string) => void;
    updateIcons: (isDark: boolean) => void;
};
const themeSwitcher: ThemeSwitcher = {
    elements: {
        darkIcon: null,
        lightIcon: null,
        toggleBtn: null,
        html: null,
    },
    animationConfig: {
        duration: 0.6,
        delay: 0.2,
        ease: 'power2.out',
    },
    init() {
        try {
            this.cacheElements();
            this.setInitialTheme();
            this.bindEvents();
        } catch (error) {
            console.error('Theme switcher initialization failed:', error);
        }
    },
    cacheElements() {
        this.elements = {
            darkIcon: document.getElementById('dark-theme-icon'),
            lightIcon: document.getElementById('light-theme-icon'),
            toggleBtn: document.getElementById('theme-toggle'),
            html: document.documentElement,
        };
    },
    setInitialTheme() {
        const prefersDark = window.matchMedia(
            '(prefers-color-scheme: light)',
        ).matches;
        const storedTheme = localStorage.getItem('color-theme');
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
        const isDark = storedTheme === 'dark' || (!storedTheme && prefersDark);
        // this.setTheme(isDark ? 'dark' : 'light');
        this.setTheme('light');
    },
    bindEvents() {
        const { toggleBtn } = this.elements;
        if (!toggleBtn) return;

        toggleBtn.addEventListener('click', () => {
            const currentTheme = this.elements.html?.classList.contains('dark')
                ? 'dark'
                : 'light';
            this.setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });
    },

    setTheme(theme) {
        if (!['dark', 'light'].includes(theme)) return;

        const { html } = this.elements;
        html?.classList.remove('dark', 'light');
        html?.classList.add(theme);

        localStorage.setItem('color-theme', theme);
        this.updateIcons(theme === 'dark');
    },

    updateIcons(isDark) {
        const { darkIcon, lightIcon } = this.elements;
        if (!darkIcon || !lightIcon) return;

        const showIcon = isDark ? darkIcon : lightIcon;
        const hideIcon = isDark ? lightIcon : darkIcon;

        // Hide the current icon
        hideIcon.classList.add('hidden');

        // Show the new icon
        showIcon.classList.remove('hidden');

        // Use GSAP for the animation
        gsap.fromTo(
            showIcon,
            {
                x: 100,
                opacity: 0,
            },
            {
                x: 0,
                opacity: 1,
                duration: this.animationConfig.duration,
                delay: this.animationConfig.delay,
                ease: this.animationConfig.ease,
            },
        );
    },
};
onMounted(() => {
    themeSwitcher.init();
});
</script>
<template>
    <button
        id="theme-toggle"
        aria-label="Theme toggle button"
        class="fixed right-0 bottom-5 !z-[9999] flex size-12 cursor-pointer items-center justify-center rounded-l-2xl bg-background-8 dark:bg-white"
    >
        <span id="dark-theme-icon">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="size-6 stroke-black"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                />
            </svg>
        </span>
        <span id="light-theme-icon">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                class="size-6 stroke-white"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
                />
            </svg>
        </span>
    </button>
</template>
