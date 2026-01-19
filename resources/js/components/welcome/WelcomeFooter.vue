<script setup lang="ts">
import Tiktok from '@/components/ui/icons/tiktok.vue';
import { Link } from '@inertiajs/vue3';
import { Facebook, Instagram, Linkedin, Twitter, Youtube } from 'lucide-vue-next';
import { computed } from 'vue';
import { useLandingTranslations } from '@/composables/useLandingTranslations';

interface SocialLink {
    name: string;
    href: string;
    icon: any;
}

const socialLinks: SocialLink[] = [
    { name: 'Facebook', href: 'https://facebook.com/pixrup', icon: Facebook },
    { name: 'LinkedIn', href: 'https://linkedin.com/pixrup', icon: Linkedin },
    { name: 'Twitter', href: 'https://x.com/pixrup', icon: Twitter },
    { name: 'TikTok', href: 'https://tiktok.com/pixrup', icon: Tiktok },
    {
        name: 'Instagram',
        href: 'https://instagram.com/pixrup',
        icon: Instagram,
    },
    { name: 'YouTube', href: 'https://youtube.com/pixrup', icon: Youtube },
];

const landingTranslations = useLandingTranslations();
const footerTranslations = computed(() => landingTranslations.value.footer ?? {});
const footerRights = computed(() => {
    const year = new Date().getFullYear().toString();
    const template = footerTranslations.value.rights ?? 'All Rights Reserved © :year Pixrup';

    return template.replace(':year', year);
});
</script>

<template>
    <footer
        class="absolute bottom-0 flex w-full flex-col items-center justify-between gap-6 border-t border-slate-300/40 pb-3 text-xs text-slate-700 md:flex-row"
    >
        <div class="flex items-center gap-4 text-slate-600">
            <a
                v-for="link in socialLinks"
                :key="link.name"
                :href="link.href"
                target="_blank"
                rel="noreferrer"
                class="pointer-events-none flex size-12 items-center justify-center rounded-full bg-surface text-accent shadow-neu-in"
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
