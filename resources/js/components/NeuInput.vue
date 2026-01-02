<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useAttrs, ref} from 'vue';
import { Icon } from '@iconify/vue';
import {ClassValue} from "clsx";
const model = defineModel();
defineOptions({
    inheritAttrs: false,
});
const attributes = useAttrs();
const props = defineProps<{
    label?: string;
    error?: string;
    class?: string;
    wrapperClass?: ClassValue;
    tabIndex?: number;
    icon?: string;
    defaultValue?: string;
}>();
const { ref: forwardedRef, ...attrs } = attributes;
const inputEl = ref<HTMLInputElement | null>(null);
if (typeof forwardedRef === 'function') {
    forwardedRef(inputEl.value);
}
defineExpose({
    el: inputEl,
});
</script>

<template>
    <div class="npo-form-control gap-2">
        <label v-if="label" class="npo-form-label">{{ label }}</label>

        <div :class="cn('npo-input-wrapper py-3 px-2', 'group', error && 'bg-red-100', props.wrapperClass)">

            <Icon v-if="props.icon"  :icon="props.icon" class="w-8 h-8 text-accent" />
            <input
                v-bind="attrs"
                ref="inputEl"
                :class="
                    cn('npo-input', 'flex-1', props.class, 'placeholder:text-accent text-accent/80')
                "
                v-model="model"
                :tabindex="props.tabIndex"
            />
            <slot/>
        </div>

        <p v-show="error" class="pl-3 text-[0.8rem] text-red-500">
            {{ error }}
        </p>
    </div>
</template>


<style scoped>
.neumo-icon {
    position: relative;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset -2px -2px 5px rgba(255, 255, 255, 1),
    inset 3px 3px 5px rgba(0, 0, 0, 0.1);
}
.npo-form-control{
    background-color: var(--neu-surface);
    border-radius: 12px;
}
</style>
