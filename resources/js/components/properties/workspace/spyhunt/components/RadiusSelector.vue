<script setup lang="ts">
const {radius, defaultRadius} = defineProps<{radius: number[], defaultRadius: number}>()
const emits = defineEmits<{
    (e: 'radius:changed', payload: string | number): void
}>()
</script>

<template>
    <div class="pointer-events-auto flex flex-wrap gap-3 rounded-[12px] bg-white/90 px-5 py-4 text-sm font-semibold shadow-lg"
    >
        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">
            Radius
        </p>
        <div class="flex gap-2">
            <button
                v-for="r in radius"
                :key="r"
                type="button"
                class="rounded-full px-3 py-1 text-xs font-semibold transition"
                :class="r === defaultRadius
                        ? 'bg-[#7c4dff] text-white shadow'
                        : 'bg-gray-100 text-gray-600'
                        "
                @click="emits('radius:changed',r)"
            >
                {{ r }} mi
            </button>
        </div>
    </div>
</template>

<style scoped></style>
