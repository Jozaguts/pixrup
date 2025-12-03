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
    <div class="npo-form-shadow flex justify-between rounded-[12px] p-5 min-h-[140px]">
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
                <span v-if="percentage" class="font-bold text-accent shadow-neu-in py-2 px-4 mt-3 min-w-[80px] text-center" :class="classes">{{percentage}}</span>
            </div>

    </div>
</template>

<style scoped></style>
