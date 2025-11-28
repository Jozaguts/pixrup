<script setup lang="ts">
   import NButton from "@/components/neuphormic-button.vue";
   import {ref, computed} from "vue";
   import { cn } from '@/lib/utils';
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
       controlsPosition?: 'top' | 'bottom';
   }>();
   const  { wrapperClasses, contentClasses, menuWrapperClasses }  = useResolvedClasses(props);

   const currentStep = ref<number>(0);
   const hashmapSteps = computed<StepMap>(() => {
     const out: StepMap = {};

     props.steps.forEach((step, index) => {
       //step[logo]
       out[index] = {
         ...step,
         id: index
       };

       if (step.index === props.step) {
         currentStep.value = index;
       }
     });

     return out;
   });
   const activeStep = computed(()=> hashmapSteps.value[currentStep.value]);
   const stepsCount = computed(()=> props.steps.length);

   const nextStep = ():void => {
       if(currentStep.value > props.steps.length -1) {
           currentStep.value = 0//first step;
           return;
       }

       currentStep.value += 1;
   };

   const previousStep = ():void => {
       if(currentStep.value <= 0) {
           currentStep.value = props.steps.length -1 //last step;
           return;
       }
       currentStep.value -= 1;
   };

   const goToStep = (stepIndex:string):void => {
     currentStep.value= Object.values(hashmapSteps.value).find(s => s.index === stepIndex)?.id || 0;
   };


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
                    @click="()=> goToStep(step.id)"
                />
        </div>
        <div :class="contentClasses">
            <header class="mb-4 space-y-2 px-5 row-start-1 h-[50px]">
                <h1 class="md:text-3xl sm:text-xl font-semibold">{{ activeStep.title }}</h1>
                <p class="text-xs md:text-sm font-light text-muted-foreground"
                >
                    {{ activeStep.description }}
                </p>
            </header>
            <div :class="cn('flex-1 w-full start-row-2 h-full', props.controlsPosition  === 'top' ? 'row-start-3' : 'row-start-2')">
              <KeepAlive>
                <component @image-selected="handleSelectImage" :is="activeStep?.component" :step="activeStep" />
              </KeepAlive>
            </div>
            <div :class="cn('w-full overflow-hidden', props.controlsPosition  === 'top' ? 'row-start-2' : 'row-start-3')">
                <slot name="footer" :goToStep="goToStep" :nextStep="nextStep" :previousStep="previousStep" :activeStep="activeStep" :steps="stepsCount">
                    <div class="h-[50px] flex flex-row items-center justify-center-safe gap-4 w-full md:w-fit md:mx-auto rounded-[12px]">
                        <button @click="previousStep" class="disabled:text-black/20 disabled:pointer-events-none hover:shadow-neu-in px-2 md:px-4 py-2 md:py-3 rounded-[12px] cursor-pointer">
                            <Icon icon="mdi:chevron-double-left" class="w-6 h-6 ml-2 inline" />
                        </button>
                        <p class="flex flex-row">
                            step {{(currentStep + 1) + ' of ' + props.steps.length }}
                        </p>
                        <button @click="nextStep"
                            :disabled="!activeStep?.completed && false"
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
