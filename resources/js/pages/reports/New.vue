<script setup lang="ts">
 import AppLayout from '@/layouts/AppLayout.vue';
 import DashboardSection from "@/components/DashboardSection.vue";
 import HorizontalStepper from "@/components/ui/horizontal-stepper.vue";
 import {ref, defineAsyncComponent, nextTick, computed} from 'vue';
 const currentStep = ref<string>('logo');
 const completedSteps = ref<string[]>([]);
 function isStepCompleted(index: string) {
     return completedSteps.value.includes(index);
 }
 const items = ref([
     { title: 'Choose a logo for your report.',
         completed: isStepCompleted('logo'),
         index: 'logo',
         icon: 'mdi:image-filter-center-focus-weak',
         description: 'Select a logo to be displayed on the report cover page.',
         component: defineAsyncComponent(() =>
             import('@/components/reports/logo-picker.vue')
         ),

     },
     {
         title: 'Select Report Type',
         index:'type',
         completed: isStepCompleted('type'),
         icon: 'mdi:format-list-bulleted-type',
     },
     { title: 'Configure Report Settings', completed: false, index: 2 },
     { title: 'Review & Generate', completed: false, index: 3 },
 ]);

 const stepIndexes = computed(() => items.value.map(item => item.index));
</script>

<template>
    <AppLayout :breadcrumbs="[ {title:'Reports', href: '/reports'}, {title:'New Report', href: '/reports/new'}]">
        <HorizontalStepper :steps="items" :step="currentStep"/>
    </AppLayout>
</template>
