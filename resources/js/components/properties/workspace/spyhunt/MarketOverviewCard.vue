<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { computed } from 'vue';
type Props = {
    label: string;
    title: string;
    icon: string;
    percentage: number | null | string;
}
const {label='', title='', icon='', percentage=0} = defineProps<Props>()
const classes = computed(() => {
    return ( Number(percentage) > 0) ?  'text-primary' : 'text-gray-500';
})
</script>

<template>
    <div class="pa-2 md:col-span-3 lg:col-span-3">
        <h2 class="text-2xl font-semibold tracking-tight text-[#1f2937] sm:text-2xl md:text-2xl mb-2">Market Overview</h2>
        <div class="npo-form-shadow flex justify-between rounded-[12px] p-5">
            <div>
                <h3
                    class="text-sm font-semibold tracking-wide text-[#6b7280] uppercase"
                >
                    {{label}}
                </h3>
                <p class="font-bold">{{title}}</p>
            </div>
            <div class="flex flex-col justify-center items-center">
                <div class="pointer-events-none flex size-12 items-center justify-center rounded-full text-[#7C4DFF] shadow-neu-in">
                    <Icon
                        :icon="icon"
                        class="h-6 w-6 font-bold"
                    />
                </div>
                <span class="font-bold text-accent shadow-neu-in py-2 px-4 mt-3" :class="classes">{{percentage}}%</span>
            </div>

        </div>
    </div>
</template>

<style scoped></style>
