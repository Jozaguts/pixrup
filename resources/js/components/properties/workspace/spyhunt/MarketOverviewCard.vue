<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { computed } from 'vue';
type Props = {
    label: string;
    title: string | number;
    icon: string;
    percentage?: number | string;
}
const {label='', title='', icon='', percentage=0} = defineProps<Props>()
const classes = computed(() => {
    return ( Number(percentage) > 0) ?  'text-primary' : 'text-gray-500';
})
</script>

<template>
    <div class="npo-form-shadow flex justify-between rounded-[12px] p-2 min-h-[140px]">
            <div class="flex flex-col  items-start">
                <h3
                    class="text-sm font-semibold tracking-wide text-[#6b7280] uppercase"
                >
                    {{label}}
                </h3>
                <p class="font-bold">{{title}}</p>
            </div>
            <div class="flex flex-col justify-between items-center">
                <div class="pointer-events-none flex size-10 items-center justify-center rounded-full text-[#7C4DFF] shadow-neu-in">
                    <Icon
                        :icon="icon"
                        class="h-4 w-4 font-bold"
                    />
                </div>
                <span v-if="percentage" class="font-bold px-2 py-1 text-accent shadow-neu-in min-w-[50px] text-center" :class="classes">{{percentage}}</span>
            </div>

    </div>
</template>

<style scoped></style>
