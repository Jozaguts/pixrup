<template>
    <AppLayout :breadcrumbs="[
        {title:'Reports', href: '/reports'},
        {title:'New Report', href: '/reports/new'}
        ]">
        <DashboardSection
            title="Create New Report"
            description="Complete the steps in order to generate your report."
            title-class="!text-3xl font-semibold"
            description-class="text-md font-light text-black/60 mb-6"
            class="w-full h-full p-0"
        >
            <HorizontalStepper :steps="items" :current-step="currentStep" />
        </DashboardSection>
    </AppLayout>
</template>

<script setup lang="ts">
 import AppLayout from '@/layouts/AppLayout.vue';
 import DashboardSection from "@/components/DashboardSection.vue";
 import HorizontalStepper from "@/components/ui/horizontal-stepper.vue";
 import  { ref, defineAsyncComponent } from 'vue';
 const currentStep = ref<string>('logo');
 const completedSteps = ref<string[]>([]);
 function isStepCompleted(index: string) {
     return completedSteps.value.includes(index);
 }
 const items = ref([
     { title: 'Choose a logo',
         completed: isStepCompleted('logo'),
         index: 'logo',
         icon: 'mdi:image-filter-center-focus-weak',
         component: defineAsyncComponent(() =>
             import('@/components/reports/logo-picker.vue')
         )
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
</script>
