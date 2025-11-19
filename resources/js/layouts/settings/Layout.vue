<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import {cn, toUrl, urlIsActive} from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        icon:'fluent-color:apps-list-detail-20',
        href: editProfile(),
    },
    {
        title: 'Password',
        href: editPassword(),
        icon:'fluent-color:lock-shield-16',
    },
    {
        title: 'Two-Factor Auth',
        href: show(),
        icon:'fluent-color:phone-laptop-16'
    },
    {
        title: 'Appearance',
        href: editAppearance(),
        icon:'fluent-color:options-32',
    },
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Settings"
            description="Manage your profile and account settings"
        />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="cn(
                            'w-full justify-start',
                             'px-4 py-6 bg-transparent shadow-none',
                             urlIsActive(item.href, currentPath) && 'neu-button active' ,
                        )"
                        as-child
                    >
                        <Link :href="item.href">
                            <Icon v-if="item.icon" :icon="item.icon as string" :class="cn(
                                  '!h-5 !w-5',
                                  // 'opacity-0 group-hover:opacity-100',
                                  // urlIsActive(item.href, currentPath) ? 'opacity-100' : 'opacity-0'
                            )" />
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
