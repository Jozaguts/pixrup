<script setup lang="ts">
import { dashboard } from '@/routes';
import auth from '@/routes/auth';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import WelcomeMobileMenu from './WelcomeMobileMenu.vue';

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

const menuItems = computed(() => props.navItems ?? []);
const isMobileMenuOpen = ref(false);

const largeLogo = new URL('../../../images/pixrup-2.svg', import.meta.url)
    .href;
const compactLogo = new URL('../../../images/pixrup-2.svg', import.meta.url)
    .href;

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
</script>

<template>
    <header>
        <div
            class="header-one fixed top-6 left-1/2 z-20 flex w-full max-w-6xl -translate-x-1/2 items-center justify-between rounded-full neu-bg-surface-color px-3 py-2 shadow-lg backdrop-blur dark:bg-slate-900/70"
        >
            <div>
                <Link href="/">
                    <span class="sr-only">Home</span>
                    <figure class="hidden lg:block lg:max-w-[50px]">
                        <img
                            :src="largeLogo"
                            alt="Pixrup"
                            class="dark:invert"
                        />
                    </figure>
                    <figure class="block max-w-[44px] lg:hidden">
                        <img
                            :src="compactLogo"
                            alt="Pixrup"
                            class="block w-full dark:hidden"
                        />
                        <img
                            :src="compactLogo"
                            alt="Pixrup"
                            class="hidden w-full invert dark:block"
                        />
                    </figure>
                </Link>
            </div>

            <nav class="hidden items-center xl:flex">
                <ul class="flex items-center gap-1">
                    <li
                        v-for="item in menuItems"
                        :key="item.label"
                        class="relative cursor-pointer py-2.5"
                    >
                        <component
                            :is="item.external ? 'a' : Link"
                            :href="item.href"
                            class="flex items-center gap-1 rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:text-slate-900"
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
                    class="inline-flex items-center justify-center rounded-full bg-slate-900 px-6 py-2 text-sm font-semibold text-white transition hover:bg-slate-700"
                >
                    <span>{{ resolvePrimaryCta.label }}</span>
                </component>
            </div>

            <div class="block xl:hidden">
                <button
                    class="flex size-12 flex-col items-center justify-center gap-[5px] rounded-full bg-white/80 text-slate-900 shadow-md transition hover:bg-white"
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
