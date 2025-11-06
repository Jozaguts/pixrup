<script setup lang="ts">
import { dashboard } from '@/routes';
import auth from '@/routes/auth';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface NavItem {
    label: string;
    href: string;
    external?: boolean;
}

const props = defineProps<{
    open: boolean;
    navItems: NavItem[];
    isAuthenticated: boolean;
    canRegister: boolean;
    primaryLink?: NavItem;
}>();

const emit = defineEmits<{
    close: [];
}>();

const compactLogo = new URL('../../../images/pixrup-icon.svg', import.meta.url)
    .href;

const resolvePrimaryCta = computed<NavItem>(() => {
    if (props.isAuthenticated) {
        return {
            label: 'Dashboard',
            href: dashboard(),
            external: false,
        };
    }

    if (props.primaryLink) {
        return props.primaryLink;
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

const handleClose = () => {
    emit('close');
};
</script>

<template>
    <div
        class="xl:hidden"
        :class="[open ? 'pointer-events-auto' : 'pointer-events-none']"
    >
        <div
            class="fixed inset-0 z-[998] bg-slate-900/40 backdrop-blur-sm transition-opacity duration-100"
            :class="open ? 'opacity-100' : 'opacity-0'"
            @click="handleClose"
        ></div>
        <aside
            class="sidebar fixed top-0 right-0 z-[9999] h-screen w-full overflow-y-auto neu-bg-surface-color transition-transform duration-100 sm:w-1/2 dark:bg-slate-900"
            :class="open ? 'translate-x-0' : 'translate-x-full'"
        >
            <div class="space-y-4 p-5 sm:p-8 lg:p-9">
                <div class="flex items-center justify-between">
                    <Link href="/" @click="handleClose">
                        <span class="sr-only">Home</span>
                        <figure class="max-w-[44px]">
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
                    <button
                        class="flex size-10 items-center justify-center rounded-full bg-slate-100 text-slate-600 shadow transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200"
                        type="button"
                        @click="handleClose"
                    >
                        <span class="sr-only">Close menu</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18 18 6m0 12L6 6"
                            />
                        </svg>
                    </button>
                </div>
                <nav class="h-[85vh] overflow-y-auto pb-10">
                    <ul class="space-y-2">
                        <li>
                            <component
                                :is="resolvePrimaryCta.external ? 'a' : Link"
                                :href="resolvePrimaryCta.href"
                                class="flex items-center justify-between rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700"
                                @click="handleClose"
                            >
                                <span>{{ resolvePrimaryCta.label }}</span>
                            </component>
                        </li>
                        <li v-for="item in navItems" :key="item.label">
                            <component
                                :is="item.external ? 'a' : Link"
                                :href="item.href"
                                class="flex items-center justify-between rounded-[14px] border border-slate-200 px-4 py-3 text-base font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800"
                                @click="handleClose"
                            >
                                <span>{{ item.label }}</span>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="size-4"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m9 5 7 7-7 7"
                                    />
                                </svg>
                            </component>
                        </li>
                        <li v-if="!isAuthenticated">
                            <Link
                                :href="auth.login.show()"
                                class="flex items-center justify-between rounded-[14px] px-4 py-3 text-base font-medium text-slate-600 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
                                @click="handleClose"
                            >
                                <span>Log in</span>
                            </Link>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
    </div>
</template>
