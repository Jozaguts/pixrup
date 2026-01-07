<template>
    <div class="flex gap-6 w-full h-full">
        <!-- LEFT SIDE – Steps -->
        <div class="w-64 border-r pr-4">
            <ul class="space-y-4">
                <li v-for="(step, i) in steps" :key="i" class="flex items-center gap-2">
                    <div
                        class="w-6 h-6 rounded-full flex items-center justify-center text-sm font-semibold"
                        :class="{
              'bg-blue-600 text-white': currentStep === i,
              'bg-surface text-gray-600': currentStep !== i,
            }"
                    >
                        {{ i + 1 }}
                    </div>

                    <button
                        class="text-left w-full"
                        :class="{
              'font-semibold text-gray-900': currentStep === i,
              'text-gray-600': currentStep !== i,
            }"
                        @click="$emit('update:currentStep', i)"
                    >
                        {{ step.label }}
                    </button>
                </li>
            </ul>
        </div>

        <!-- RIGHT SIDE – Content -->
        <div class="flex-1">
            <slot :step="currentStep" />
        </div>
    </div>

    <!-- FOOTER -->
    <div class="mt-6 flex justify-between">
        <button
            class="px-4 py-2 border rounded-md"
            :disabled="currentStep === 0"
            @click="$emit('update:currentStep', currentStep - 1)"
        >
            Prev
        </button>

        <button
            class="px-4 py-2 bg-blue-600 text-white rounded-md"
            :disabled="currentStep === steps.length - 1"
            @click="$emit('update:currentStep', currentStep + 1)"
        >
            Next
        </button>
    </div>
</template>

<script setup lang="ts">
interface Step {
    label: string;
}

defineProps<{
    steps: Step[];
    currentStep: number;
}>();

defineEmits(["update:currentStep"]);
</script>

<style scoped>
</style>
