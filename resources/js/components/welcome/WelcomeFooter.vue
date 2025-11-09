<script setup lang="ts">
import Tiktok from '@/components/ui/icons/tiktok.vue';
import { Link } from '@inertiajs/vue3';
import {
    Facebook,
    Instagram,
    Linkedin,
    Twitter,
    Youtube,
} from 'lucide-vue-next';

interface SocialLink {
    name: string;
    href: string;
    icon: any;
}

const socialLinks: SocialLink[] = [
    { name: 'Facebook', href: 'https://facebook.com/fixrup', icon: Facebook },
    { name: 'LinkedIn', href: 'https://linkedin.com/fixrup', icon: Linkedin },
    { name: 'Twitter', href: 'https://x.com/fixrup', icon: Twitter },
    { name: 'TikTok', href: 'https://tiktok.com/fixrup', icon: Tiktok },
    {
        name: 'Instagram',
        href: 'https://instagram.com/fixrup',
        icon: Instagram,
    },
    { name: 'YouTube', href: 'https://youtube.com/fixrup', icon: Youtube },
];
</script>

<template>
    <footer
        class="flex w-full flex-col items-center justify-between gap-6 border-t border-slate-300/40 pb-3 text-xs text-slate-700 md:flex-row"
    >
        <div class="flex items-center gap-4 text-slate-600">
            <a
                v-for="link in socialLinks"
                :key="link.name"
                :href="link.href"
                target="_blank"
                rel="noreferrer"
                class="neu-icon "
            >
                <component :is="link.icon" class="h-4 w-4" />
            </a>
        </div>

        <p class="text-center text-slate-600">
            All Rights Reserved © {{ new Date().getFullYear() }} Pixrup |
            <Link href="/terms" class="underline decoration-dotted">
                Terms
            </Link>
            and
            <Link href="/privacy" class="underline decoration-dotted">
                Privacy Policy
            </Link>
        </p>
    </footer>
</template>
