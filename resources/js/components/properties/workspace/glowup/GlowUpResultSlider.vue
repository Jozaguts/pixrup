<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
    before: string;
    after: string;
    label?: string;
}

const props = defineProps<Props>();

const slider = ref(52);

const beforeStyle = computed(() => ({
    width: `${slider.value}%`,
}));
</script>

<template>
    <div class="space-y-3">
        <p v-if="props.label" class="text-sm font-semibold text-gray-600">
            {{ props.label }}
        </p>
        <div
            class="relative aspect-video w-full overflow-hidden rounded-2xl bg-gray-200 shadow-inner"
        >
            <img
                :src="props.after"
                alt="GlowUp result"
                class="absolute inset-0 h-full w-full object-cover"
                draggable="false"
            />
            <div
                class="absolute inset-0 overflow-hidden transition-all duration-200 ease-out"
                :style="beforeStyle"
            >
                <img
                    :src="props.before"
                    alt="GlowUp original"
                    class="h-full w-full object-cover"
                    draggable="false"
                />
            </div>
            <div
                class="absolute inset-x-0 bottom-3 flex items-center justify-center px-6"
            >
                <div
                    class="flex w-full items-center gap-3 rounded-full bg-white/70 px-4 py-2 backdrop-blur"
                >
                    <span class="text-xs font-semibold text-gray-500"
                        >Before</span
                    >
                    <input
                        v-model="slider"
                        type="range"
                        min="0"
                        max="100"
                        class="h-1 w-full accent-[#7c4dff]"
                        aria-label="Before/After slider control"
                    />
                    <span class="text-xs font-semibold text-gray-800"
                        >After</span
                    >
                </div>
            </div>
        </div>
    </div>
</template>
