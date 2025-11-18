<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import CardIcon from '@/components/ui/icons/card.vue';
import LayoutDashboard from '@/components/ui/icons/dashboard.vue';
import GraduationCap from '@/components/ui/icons/graduation-cap.vue';
import Home from '@/components/ui/icons/properties.vue';
import ReportsIcon from '@/components/ui/icons/reports.vue';
import SupportIcon from '@/components/ui/icons/support.vue';
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
import { User } from 'lucide-vue-next';
import { computed } from 'vue';
import { cn } from '@/lib/utils'
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
                    icon: 'fluent-color:people-home-16',
                },
                {
                    title: 'Properties',
                    href: '/properties',
                    icon: 'fluent-color:building-people-20',
                },
                {
                    title: 'Reports',
                    href: '/reports',
                    icon: 'fluent-color:people-list-20',
                },
            ]),
        },
        {
            key: 'operations',
            items: withState([
                {
                    title: 'Billing',
                    href: '/billing',
                    icon: 'fluent-color:receipt-16',
                },
                {
                    title: 'Tutorials',
                    href: '/tutorials',
                    icon: 'fluent-emoji-flat:graduation-cap',
                },
                {
                    title: 'Support',
                    href: '/support',
                    icon: 'fluent-color:chat-bubbles-question-20',
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

        <SidebarContent class="">
            <template v-for="(group, index) in navGroups" :key="group.key">
                <SidebarGroup>
                    <SidebarMenu>
                        <SidebarMenuItem
                            v-for="item in group.items"
                            :key="item.title"
                        >
                            <SidebarMenuButton
                                as-child
                                :is-active="item.isActive"
                                :tooltip="item.title"
                            >
                                <Link
                                    :href="item.href"
                                    :class="cn( item.isActive && 'neu-button')"
                                >
                                    <Icon :icon="item.icon" class="!w-8 !h-8" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroup>
                <SidebarSeparator
                    v-if="index < navGroups.length - 1"
                    class="my-1 opacity-60"
                />
            </template>
        </SidebarContent>

        <SidebarFooter class="">
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
