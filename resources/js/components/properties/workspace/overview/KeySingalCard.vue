<script setup lang="ts">
const { icon, value, details, title } = defineProps<{ icon: any; value?: string; details?: string; title: string }>();
</script>

<template>
    <div class="flex w-full min-w-0 flex-col gap-3 rounded-[16px] bg-background p-4 shadow-neu-in">
        <div class="flex flex-col items-start justify-between gap-4">
            <div
                class="flex w-full min-w-0 flex-wrap items-center gap-2 border-b-1 border-accent/20 pb-2 text-xs font-semibold tracking-[0.2em] text-accent uppercase"
            >
                <component :is="icon" class="h-8 w-8 text-primary" />
                <span class="break-words">{{ title }}</span>
            </div>
            <p class="text-2xl font-semibold text-accent">
                {{ value }}
            </p>
            <p v-if="details" class="text-xs text-accent/50">
                {{ details }}
            </p>
        </div>
        <slot name="expand">

        </slot>
    </div>
</template>

<style scoped></style>
