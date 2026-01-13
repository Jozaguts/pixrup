<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarSeparator,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { dashboard } from '@/routes';
import auth from '@/routes/auth';
import { edit as editProfile } from '@/routes/profile';
import type { AppPageProps, NavItem } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { LogOut, User } from 'lucide-vue-next';
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import { Icon } from '@iconify/vue';
type ExtendedPageProps = AppPageProps<{
    appMeta?: {
        version?: string;
        plan?: string;
    };
}>;

interface SidebarNavItem extends NavItem {
    action?: 'logout';
}

const page = usePage<ExtendedPageProps>();
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const appMeta = computed(() => ({
    version: page.props.appMeta?.version ?? 'Pixrup Pro v1.0',
    plan: page.props.appMeta?.plan ?? 'Professional',
}));

const navGroups = computed(() => {
    const currentUrl = page.url;

    const withState = (items: NavItem[]): NavItem[] =>
        items.map((item) => ({
            ...item,
            isActive: urlIsActive(item.href, currentUrl),
        }));

    return [
        {
            key: 'global',
            items: withState([
                {
                    title: 'Home',
                    href: dashboard(),
                    icon: 'mdi-light:home',
                },
                {
                    title: 'Properties',
                    href: '/properties',
                    icon: 'mdi:office-building-outline',
                },
                {
                    title: 'PixrVision Reports',
                    href: '/reports',
                    icon: 'mdi:account-file-text-outline',
                },
            ]),
        },
        {
            key: 'operations',
            items: withState([
                {
                    title: 'Billing',
                    href: '/billing/account',
                    icon: 'mdi:file-document-arrow-right-outline',
                },
            ]),
        },
    ];
});
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const accountNavItems = computed<SidebarNavItem[]>(() => {
    const currentUrl = page.url;

    return [
        {
            title: 'Profile & Settings',
            href: editProfile(),
            icon: User,
            isActive: urlIsActive(editProfile(), currentUrl),
        },
        {
            title: 'Logout',
            href: auth.logout(),
            icon: LogOut,
            action: 'logout',
            isActive: false,
        },
    ];
});
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar">
        <SidebarHeader class="pb-2">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <template v-for="(group, index) in navGroups" :key="group.key">
                <SidebarGroup>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in group.items" :key="item.title">
                            <SidebarMenuButton as-child :is-active="item.isActive" :tooltip="item.title">
                                <Link
                                    :href="item.href"
                                    :data-active="item.isActive"
                                    :class="
                                        cn(
                                            'neu-button font-semibold text-accent data-[active=true]:!shadow-neu-in',
                                            'data-[active=true]:!bg-background',
                                        )
                                    "
                                >
                                    <Icon :icon="item.icon as string" class="!h-6 !w-6" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroup>
                <SidebarSeparator v-if="index < navGroups.length - 1" class="my-1 opacity-60" />
            </template>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
