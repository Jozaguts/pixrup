<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useAttrs } from 'vue';
import { Icon } from '@iconify/vue';
const model = defineModel();
const attributes = useAttrs();
const props = defineProps<{
    label?: string;
    error?: string;
    class?: string;
    tabIndex?: number;
    icon?: string;
    defaultValue?: string;
}>();
</script>

<template>
    <div class="npo-form-control gap-2">
        <label v-if="label" class="npo-form-label">{{ label }}</label>

        <div :class="cn('npo-input-wrapper', 'group', error && 'bg-red-100')">

            <Icon v-if="props.icon"  :icon="props.icon" class="w-8 h-8 text-slate-500" />
            <input
                v-bind="attributes"
                :class="
                    cn('npo-input', props.class, 'placeholder:text-[#9da3b0]')
                "
                v-model="model"
                :tabindex="props.tabIndex"
            />
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
</style>
