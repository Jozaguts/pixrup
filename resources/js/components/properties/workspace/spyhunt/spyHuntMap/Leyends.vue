<script setup lang="ts">
const legend = [
    { label: 'Sold', color: 'bg-[#16a34a]' },
    { label: 'Active', color: 'bg-[#2563eb]' },
    { label: 'Rent', color: 'bg-[#7c4dff]' },
];
</script>

<template>
    <div
        class="pointer-events-none absolute bottom-6 left-6 flex flex-wrap items-center gap-3 rounded-2xl bg-white/90 px-4 py-3 text-xs font-semibold text-gray-500 shadow-lg"
    >
        <div
            v-for="item in legend"
            :key="item.label"
            class="flex items-center gap-2"
        >
            <span class="size-2 rounded-full" :class="item.color" />
            {{ item.label }}
        </div>
    </div>
</template>