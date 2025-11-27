<script setup lang="ts">

import {Icon} from "@iconify/vue";
import { cn } from "@/lib/utils";
import {useAttrs} from "vue";
const props = defineProps({
    icon: String,
    label: String,
    iconClass: String,
    buttonClass: String,
    shake: Boolean,
    to: String,
    shadow: {
        type: Boolean,
        default: true,
    }
});

const attrs = useAttrs();
</script>

<template>
    <component
        :is="props.to ? 'a' : 'button'"
        :href="props.to"
        v-bind="attrs"
        :class="cn(
        'group px-4 py-5 rounded-[8px] flex items-center justify-center transform',
        'text-black disabled:text-black/40',
        props.shadow && 'neu-button',
        props.shake && 'transition-transform duration-200 ease-out hover:scale-110',
        props.buttonClass
    )"
    >
        <Icon v-if="props.icon" :icon="props.icon" :class="cn('w-5 h-5 inline mr-2',props.iconClass)" />
        {{label}}
    </component>
</template>
