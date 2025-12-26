<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import LogOutIcon from '@/components/ui/icons/logout.vue';
import SettingsIcon from '@/components/ui/icons/settings.vue';
import auth from '@/routes/auth';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
interface Props {
    user: User;
}

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-kulim">
        <div
            class="neu-btn flex items-center gap-2 px-1 py-1.5 text-left text-sm"
        >
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link
                class="neu-btn text-accent block w-full"
                :href="edit()"
                prefetch
                as="button"
            >
                <Icon icon="mdi:settings-outline" class="mr-2 !w-6 !h-6" />
                Settings
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Link
            class="neu-btn text-accent block w-full"
            :href="auth.logout()"
            @click="handleLogout"
            as="button"
            data-test="logout-button"
        >
            <Icon icon="mdi:logout" class="mr-2 !h-6 !w-6" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
