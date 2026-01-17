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
import { computed } from 'vue';
import { useLandingTranslations } from '@/composables/useLandingTranslations';

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

const landingTranslations = useLandingTranslations();
const footerTranslations = computed(
    () => landingTranslations.value.footer ?? {},
);
const footerRights = computed(() => {
    const year = new Date().getFullYear().toString();
    const template =
        footerTranslations.value.rights ??
        'All Rights Reserved © :year Pixrup';

    return template.replace(':year', year);
});
</script>

<template>
    <footer
        class="flex w-full flex-col items-center justify-between gap-6 border-t border-slate-300/40 pb-3 text-xs text-slate-700 md:flex-row absolute bottom-0"
    >
        <div class="flex items-center gap-4 text-slate-600">
            <a
                v-for="link in socialLinks"
                :key="link.name"
                :href="link.href"
                target="_blank"
                rel="noreferrer"
                class="flex size-12 items-center justify-center rounded-full text-accent pointer-events-none shadow-neu-in bg-surface"
            >
                <component :is="link.icon" class="h-4 w-4" />
            </a>
        </div>

        <p class="text-center text-slate-600">
            {{ footerRights }}
            <span class="mx-1">|</span>
            <Link href="/terms" class="underline decoration-dotted">
                {{ footerTranslations.terms ?? 'Terms' }}
            </Link>
            <span class="mx-1">{{ footerTranslations.and ?? 'and' }}</span>
            <Link href="/privacy" class="underline decoration-dotted">
                {{ footerTranslations.privacy ?? 'Privacy Policy' }}
            </Link>
        </p>
    </footer>
</template>
