<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { Monitor, Moon, Sun } from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Monitor, label: 'System' },
] as const;
</script>

<template>
    <div
        class="inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800 npo-form-shadow"
    >
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            @click="updateAppearance(value)"
            :class="[
                'relative flex items-center rounded-md px-6 py-3 transition-colors tab',
                appearance === value
                    ? 'shadow-xs active dark:bg-neutral-700 dark:text-neutral-100! text-black!'
                    : 'hover:bg-neutral-200/60 hover:text-black dark:!text-neutral-400 dark:hover:bg-neutral-700/60',

            ]"
        >
            <component :is="Icon" class="-ml-1 h-4 w-4" />
            <span class="ml-1.5 text-sm">{{ label }}</span>
        </button>
    </div>
</template>


<style scoped>
 .tab::after {
    content: '';
    display: block;
     inset:3px;
    position: absolute;
     border-radius:8px;
 }
 .tab.active:after {
     box-shadow: inset -2px -2px 5px rgba(255, 255, 255, 1),
     inset 3px 3px 5px rgba(0, 0, 0, 0.1);
 }
</style>
