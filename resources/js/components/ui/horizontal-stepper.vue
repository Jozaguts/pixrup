<script setup lang="ts">
   import NButton from "@/components/neuphormic-button.vue";
   import { ref, computed } from "vue";
   import { Icon } from "@iconify/vue";

   const props = defineProps<{
       steps: Array<{
           title: string;
           description?: string;
           icon?: string;
           completed: boolean;
           index: string | number;
           component?: any;
           step?: number;
       }>;
       currentStep: string
   }>();

   const currentStep = ref<string>(props.currentStep);
   const hashmapSteps = ref<Record<string | number, any>>({});
   hashmapSteps.value = props.steps.reduce((acc, step, index) => {
       acc[step.index] ={
           ...step,
           step: index + 1,
       };
       return acc;
   }, {} as Record<string | number, any>);

   //

   const activeStep = computed(()=> hashmapSteps.value[currentStep.value]);
   //items.find(item => item.index === currentStep.value);
</script>

<template>
    <div class="grid md:grid-cols-[200px_auto] sm:grid-cols-1 sm:gap-0 h-full shadow-md rounded-[12px] overflow-hidden">
        <div class="relative bg-gray-200 md:gap-y-2 sm:gap-x-2 w-full h-full col-span-1 flex md:flex-col sm:flex-row sm:overflow-x-auto items-center justify-start md:py-4 sm:py-0">
                <NButton
                    v-for="(step, index) in props.steps" :key="index"
                    :label="step.title"
                    :icon="step.completed ? 'mdi:check-all' : step.icon"
                    button-class="text-xs px-4 py-2 w-full sm:text-[12px]"
                    :shadow="false"
                    icon-class="!w-5 !h-5 sm:!w-4 sm:!h-4"
                    :disabled="currentStep != step.index"
                />
        </div>
        <div class="col-span-1 h-full overflow-hidden grid grid-rows-[auto_80px]">
           <component :is="activeStep?.component" />
            <div class="flex flex-row items-center justify-center-safe gay-4 w-fit mx-auto npo-form-shadow mb-4 rounded-[12px] p-3">
                <button class="hover:shadow-neu-in px-4 py-3 rounded-[12px] cursor-pointer">
                    <Icon icon="mdi:chevron-double-left" class="w-6 h-6 ml-2 inline" />
                    Previous
                </button>
                <span class="mx-8">
                   Step {{ activeStep.step }} of {{ props.steps.length }}
                </span>
                <button
                    :disabled="!activeStep?.completed"
                    :class="[
                        activeStep?.completed ? 'hover:shadow-neu-in' : '',
                        'px-4 py-3 rounded-[12px] text-black disabled:text-black/20',
                        activeStep?.completed ? 'cursor-not-allowed' : 'cursor-pointer',
                        ]"
                >
                    Next
                    <Icon icon="mdi:chevron-double-right" class="w-6 h-6 ml-2 inline" />
                </button>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>
