<script setup lang="ts">
import {Icon} from "@iconify/vue";
import { cn } from "@/lib/utils";
const props = defineProps<{
    size?: number;
    disablePulse?:boolean;
    class?: string;
}>();
const size = props.size || 0;
</script>
<template>
    <div v-for="i in size" :key="i"
         :class="cn(
            'pointer-events-none relative flex animate-pulse items-center justify-center',
             'h-24 w-24 overflow-hidden rounded-[12px]',
              'border-8 border-white bg-gray-300',
              !props.disablePulse && 'animate-pulse',
              props.class
        )"
    >
        <slot>
            <Icon icon="line-md:downloading-loop" class="h-8 w-8 z-20" mode="svg"/>
        </slot>
    </div>
</template>
