<script setup lang="ts">
interface Props {
    eyebrow?: string;
    title: string;
    description?: string;
}

defineProps<Props>();
</script>

<template>
    <header class="npo-form-shadow flex flex-col gap-4 rounded-[22px] p-4 md:flex-row md:items-center md:justify-between">
        <div class="space-y-2">
            <p v-if="eyebrow" class="text-xs font-semibold tracking-[0.35em] text-accent/40 uppercase">
                {{ eyebrow }}
            </p>
            <h2 class="text-2xl font-semibold text-accent">{{ title }}</h2>
            <p v-if="description" class="text-sm text-accent/50">{{ description }}</p>
            <slot name="meta" />
        </div>
        <div v-if="$slots.actions" class="flex flex-col items-end gap-4">
            <slot name="actions" />
        </div>
    </header>
</template>
