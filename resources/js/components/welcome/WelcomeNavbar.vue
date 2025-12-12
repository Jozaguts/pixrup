<script setup lang="ts">
import { dashboard } from '@/routes';
import auth from '@/routes/auth';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import WelcomeMobileMenu from './WelcomeMobileMenu.vue';
import { gsap } from '@/lib/gsap';
import { useHideNavbarOnScroll } from '@/lib/utils';

interface NavItem {
    label: string;
    href: string;
    external?: boolean;
}

const props = withDefaults(
    defineProps<{
        isAuthenticated: boolean;
        canRegister: boolean;
        navItems?: NavItem[];
        primaryLink?: NavItem;
    }>(),
    {
        navItems: () => [],
    },
);
let gsap: gsap | null = null;
const menuItems = computed(() => props.navItems ?? []);
const isMobileMenuOpen = ref(false);

const largeLogo = new URL('../../../images/pixrup.png', import.meta.url).href;
const compactLogo = new URL('../../../images/pixrup.png', import.meta.url).href;

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

watch(isMobileMenuOpen, (isOpen) => {
    if (typeof document === 'undefined') {
        return;
    }

    document.body.classList.toggle('overflow-hidden', isOpen);
});

onBeforeUnmount(() => {
    if (typeof document !== 'undefined') {
        document.body.classList.remove('overflow-hidden');
    }
});

const page = usePage();
watch(
    () => page.url,
    () => {
        closeMobileMenu();
    },
);
const navRef = ref<HTMLElement | null>(null)
const resolvePrimaryCta = computed<NavItem>(() => {
    if (props.isAuthenticated) {
        return {
            label: 'Dashboard',
            href: dashboard(),
            external: false,
        };
    }

    if (props.primaryLink) {
        return props.primaryLink as NavItem;
    }

    if (props.canRegister) {
        return {
            label: 'Get started',
            href: auth.register.show(),
            external: false,
        };
    }

    return {
        label: 'Log in',
        href: auth.login.show(),
        external: false,
    };
});
useHideNavbarOnScroll(navRef)
</script>

<template>
    <header>
        <div
            ref="navRef"
            class="navbar fixed top-6 left-0 z-[200] flex w-2/2 items-center justify-between rounded-full p-4 shadow-lg shadow-neu-in backdrop-blur md:left-1/2 md:w-full md:max-w-6xl md:-translate-x-1/2 lg:left-1/2 lg:w-full lg:max-w-6xl lg:-translate-x-1/2 dark:bg-[#1f252f]"
        >
            <div>
                <Link href="/">
                    <span class="sr-only">Home</span>
                    <figure class="ml-2 hidden lg:block lg:max-w-[50px]">
                        <img :src="largeLogo" alt="Pixrup" class="dark" />
                    </figure>
                    <figure class="block max-w-[44px] lg:hidden">
                        <img :src="compactLogo" alt="Pixrup" class="block w-full dark:hidden" />
                        <img :src="compactLogo" alt="Pixrup" class="hidden w-full invert dark:block" />
                    </figure>
                </Link>
            </div>

            <nav class="hidden items-center xl:flex">
                <ul class="flex items-center gap-1">
                    <li v-for="item in menuItems" :key="item.label" class="relative cursor-pointer px-2 py-2.5">
                        <component
                            :is="item.external ? 'a' : Link"
                            :href="item.href"
                            class="neu-button flex items-center gap-2 rounded-full px-6 py-2 text-sm font-medium text-slate-600 dark:!text-[#fcfcfc]/60"
                            @click="closeMobileMenu"
                        >
                            <span>{{ item.label }}</span>
                        </component>
                    </li>
                </ul>
            </nav>

            <div class="hidden items-center justify-center xl:flex">
                <component
                    :is="resolvePrimaryCta.external ? 'a' : Link"
                    :href="resolvePrimaryCta.href"
                    class="neu-button inline-flex items-center justify-center rounded-full px-6 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-700 dark:!text-[#fcfcfc]/60"
                >
                    <span>{{ resolvePrimaryCta.label }}</span>
                </component>
            </div>

            <div class="block bg-gray-200 xl:hidden">
                <button
                    class="neu-button flex size-12 flex-col items-center justify-center gap-[5px] rounded-[12px] text-slate-900 shadow-md transition"
                    type="button"
                    @click="toggleMobileMenu"
                >
                    <span class="sr-only">Toggle navigation</span>
                    <span class="block h-0.5 w-6 bg-slate-900"></span>
                    <span class="block h-0.5 w-6 bg-slate-900"></span>
                    <span class="block h-0.5 w-6 bg-slate-900"></span>
                </button>
            </div>
        </div>

        <WelcomeMobileMenu
            :open="isMobileMenuOpen"
            :nav-items="menuItems"
            :is-authenticated="props.isAuthenticated"
            :can-register="props.canRegister"
            :primary-link="props.primaryLink"
            @close="closeMobileMenu"
        />
    </header>
</template>
