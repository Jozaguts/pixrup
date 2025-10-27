<script setup lang="ts">
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetTrigger,
} from '@/components/ui/sheet';
import auth from '@/routes/auth';
import { dashboard } from '@/routes';
import { Link } from '@inertiajs/vue3';
import { ChevronDown, Menu } from 'lucide-vue-next';
import { computed } from 'vue';

interface NavItem {
    label: string;
    href: string;
    external?: boolean;
}

const props = defineProps<{
    isAuthenticated: boolean;
    canRegister: boolean;
    navItems?: NavItem[];
    primaryLink?: NavItem;
}>();

const menuItems = computed(() => props.navItems ?? []);
</script>

<template>
    <header class=" relative z-20 w-full px-0 pt-4 lg:px-4 lg:pt-6">
        <!-- Mobile -->
        <div
            class=" mx-auto flex w-full max-w-5xl items-center justify-between lg:hidden"
        >
            <Sheet>
                <SheetTrigger
                    class="neu-surface shadow-neu-out inline-flex h-11 w-11 items-center justify-center text-slate-900"
                >
                    <Menu class="h-5 w-5" />
                    <span class="sr-only">Toggle navigation</span>
                </SheetTrigger>
                <SheetContent
                    side="top"
                    class="mx-auto mt-4 w-[92%] max-w-sm rounded-[12px] border-none bg-white/95 p-6 text-slate-900 shadow-2xl"
                >
                    <nav class="space-y-4">
                        <div v-if="primaryLink">
                            <SheetClose as-child>
                                <Link
                                    :href="primaryLink.href"
                                    class="block text-base font-semibold text-indigo-600"
                                >
                                    {{ primaryLink.label }}
                                </Link>
                            </SheetClose>
                        </div>

                        <div
                            v-for="item in menuItems"
                            :key="item.label"
                            class="flex items-center justify-between rounded-[12px] px-1 py-2 text-base font-medium text-slate-900"
                        >
                            <SheetClose as-child>
                                <Link :href="item.href">{{ item.label }}</Link>
                            </SheetClose>
                            <ChevronDown class="h-4 w-4 text-slate-500" />
                        </div>

                        <SheetClose v-if="!isAuthenticated" as-child>
                            <Link
                                :href="auth.login.show()"
                                class="block rounded-[12px] px-1 py-2 text-base font-medium text-slate-900"
                            >
                                Log in
                            </Link>
                        </SheetClose>
                    </nav>
                </SheetContent>
            </Sheet>

            <Link
                href="/"
                class="text-xl font-extrabold tracking-tight text-slate-900"
            >
                Pixrup
            </Link>

            <div class="flex items-center gap-2 px-0">
                <Link
                    v-if="props.canRegister && !props.isAuthenticated"
                    :href="auth.register.show()"
                    class=" neu-surface shadow-neu-int dark text-white  bg-slate-900 px-4 py-2 text-sm font-medium "
                >
                    Sign up
                </Link>
                <Link
                    v-else
                    :href="dashboard()"
                    class="rounded-[12px] bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-lg transition hover:bg-slate-700"
                >
                    Dashboard
                </Link>
            </div>
        </div>

        <!-- Desktop -->
        <div class="hidden w-full justify-end px-4 lg:flex">
            <nav
                class="flex items-center gap-3 rounded-[12px]   px-3 py-2 text-sm "
            >
                <div
                    class="neu-surface shadow-neu-in flex items-center px-4 py-2"
                >
                    <div class=" flex items-center gap-1">
                        <button
                            v-for="item in menuItems"
                            :key="item.label"
                            type="button"
                            class="neu-btn  px-4 py-2  inline-flex items-center gap-1"
                        >
                            {{ item.label }}
                        </button>
                    </div>
                </div>

                <div
                    class="neu-surface shadow-neu-in flex items-center gap-2 rounded-[12px]  px-3 py-2 shadow-sm"
                >
                    <Link
                        v-if="isAuthenticated"
                        :href="dashboard()"
                        class="rounded-[12px] border border-slate-900/60 px-4 py-2 text-sm font-medium text-slate-900 transition hover:bg-slate-900 hover:text-white"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="auth.login.show()"
                            class="neu-btn  px-4 py-2"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="auth.register.show()"
                            class="neu-btn px-4 py-2 text-sm font-medium "
                        >
                            Sign up
                        </Link>
                    </template>
                </div>
            </nav>
        </div>
    </header>
</template>
