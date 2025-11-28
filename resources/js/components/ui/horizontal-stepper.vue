<script setup lang="ts">
   import NButton from "@/components/neuphormic-button.vue";
   import { ref, computed } from "vue";
   import { Icon } from "@iconify/vue";
   import { ClassValue } from "clsx";
   import type { VerticalStepItem } from "@/types";
   import useResolvedClasses from "@/composables/reports/use-resolve-classes";
   type StepMap = Record<number, VerticalStepItem & { id: number }>;

   const props = defineProps<{
       steps: VerticalStepItem[];
       step?: string | number;
       wrapperClass?: ClassValue;
       menusClass?: ClassValue;
       contentClass?: ClassValue;
       menusWrapperClass?: ClassValue;
   }>();
   const  { wrapperClasses, contentClasses, menuWrapperClasses }  = useResolvedClasses(props);

   const currentStep = ref<number>(0);
   const hashmapSteps = ref<StepMap>({});
   hashmapSteps.value= props.steps.reduce((acc, step, index) => {
       acc[index] = {
           ...step,
           id:index
       };
       if(step.index == props.step) {
           //set current step to index
           currentStep.value = index;
       }
       return acc;
   }, {} as StepMap);
   const activeStep = computed(()=> hashmapSteps.value[currentStep.value]);

   function handleSelectImage(payload:any) {
       // console.log(payload);
       // // activeStep.value.completed = true;
       // // currentStep.value += 1;
       // // store.setImage();
   }
</script>

<template>
    <div :class="wrapperClasses">
        <div :class="menuWrapperClasses">
            <NButton
                    v-for="(step, index) in hashmapSteps" :key="index"
                    :label="step.title"
                    :icon="step.completed ? 'mdi:check-all' : step.icon"
                    button-class="text-xs py-1 items-start justify-start w-full sm:text-[12px] text-black/80 disabled:text-black/50 disabled:font-light font-medium sm:whitespace-nowrap"
                    :shadow="false"
                    icon-class="!w-5 !h-5 sm:!w-4 sm:!h-4"
                    :disabled="currentStep != step.id"
                />
        </div>
        <div :class="contentClasses">
            <div class="mb-4 space-y-2 px-5">
                <h1 class="md:text-3xl sm:text-xl font-semibold">{{ activeStep.title }}</h1>
                <p class="text-xs md:text-sm font-light text-muted-foreground"
                >
                    {{ activeStep.description }}
                </p>
            </div>
            <div class="flex-1 w-full">
                <component @image-selected="handleSelectImage" :is="activeStep?.component" :step="activeStep" />
            </div>
            <div class="w-full h-[50px] md:h-[75px] overflow-hidden">
                <slot name="footer">
                    <div class="h-full flex flex-row items-center justify-center-safe gap-4 w-full md:w-fit md:mx-auto rounded-[12px]">
                        <button :disabled="activeStep.id == 0" class="disabled:text-black/20 disabled:pointer-events-none hover:shadow-neu-in px-2 md:px-4 py-2 md:py-3 rounded-[12px] cursor-pointer">
                            <Icon icon="mdi:chevron-double-left" class="w-6 h-6 ml-2 inline" />
                        </button>
                        <p class="flex flex-row">
                            step {{(currentStep + 1) + ' of ' + props.steps.length }}
                        </p>
                        <button
                            :disabled="!activeStep?.completed"
                            :class="[
                            activeStep?.completed ? 'hover:shadow-neu-in' : '',
                            'px-4 py-3 rounded-[12px] text-black ursor-pointer disabled:text-black/20 disabled:pointer-events-none',
                            ]"
                        >
                            <Icon icon="mdi:chevron-double-right" class="w-6 h-6 ml-2 inline" />
                        </button>
                    </div>
                </slot>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
