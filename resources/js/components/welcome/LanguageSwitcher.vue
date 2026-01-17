<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useLandingTranslations } from '@/composables/useLandingTranslations';
import { cn } from '@/lib/utils';
import type { AppPageProps } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type LocaleOption = {
    code: string;
    name: string;
    native: string;
    url: string;
};

const page = usePage<AppPageProps>();
const landingTranslations = useLandingTranslations();
const srTranslations = computed(() => landingTranslations.value.nav?.sr ?? {});
const localization = computed(() => page.props.localization ?? { current: 'en', options: [] });
const currentLocale = computed(() => localization.value.current ?? page.props.locale ?? 'en');
const localeOptions = computed<LocaleOption[]>(() => localization.value.options ?? []);
const hasLocales = computed(() => localeOptions.value.length > 0);

const flagByLocale: Record<string, string> = {
    en: '/images/flags/us.svg',
    es: '/images/flags/mx.svg',
};

const currentFlag = computed(() => flagByLocale[currentLocale.value] ?? flagByLocale.en);
</script>

<template>
    <div v-if="hasLocales" data-slot="language-switcher">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <button
                    type="button"
                    class="neu-button flex items-center gap-2 px-3 py-2 text-xs font-semibold uppercase text-accent rounded-full"
                    :aria-label="srTranslations.language ?? 'Language'"
                >
                    <img :src="currentFlag" class="size-4 rounded-full" :alt="currentLocale" />
                    <span>{{ currentLocale }}</span>
                </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="neu-button w-40 !bg-surface text-accent rounded-xl">
                <DropdownMenuItem v-for="option in localeOptions" :key="option.code" :as-child="true">
                    <Link
                        :href="option.url"
                        class="flex w-full items-center gap-2 px-2 py-1.5 text-sm"
                        :class="
                            cn(
                                option.code === currentLocale
                                    ? 'text-accent'
                                    : 'text-accent/70',
                            )
                        "
                    >
                        <img
                            :src="flagByLocale[option.code] ?? currentFlag"
                            class="size-4 rounded-full"
                            :alt="option.native ?? option.name ?? option.code"
                        />
                        <span class="flex-1">
                            {{ option.native ?? option.name ?? option.code }}
                        </span>
                        <span class="text-xs font-semibold uppercase text-slate-400">
                            {{ option.code }}
                        </span>
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
