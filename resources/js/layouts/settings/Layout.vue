<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { cn, toUrl, urlIsActive } from '@/lib/utils';
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
        icon: 'mdi:text-user',
        href: editProfile(),
    },
    {
        title: 'Password',
        href: editPassword(),
        icon: 'mdi-light:lock',
    },
    {
        title: 'Two-Factor Auth',
        href: show(),
        icon: 'carbon:two-factor-authentication',
    },
    {
        title: 'Appearance',
        href: editAppearance(),
        icon: 'mdi:slider',
    },
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Settings" description="Manage your profile and account settings" />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0 rounded-[12px] navbar p-4">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :data-active="urlIsActive(item.href, currentPath)"
                        :class="cn('w-full justify-start', 'neu-button my-2 bg-surface px-4 py-6 shadow-neu-in data-[active=true]:!shadow-neu-in')"
                        as-child
                    >
                        <Link :href="item.href">
                            <Icon v-if="item.icon" :icon="item.icon as string" :class="cn('!h-5 !w-5')" />
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
