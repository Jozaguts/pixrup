<script setup lang="ts">
  import { cn } from '@/lib/utils'
  import { useAttrs } from 'vue';
  const model = defineModel();
  const attributes = useAttrs();
  const props = defineProps<{
    label?: string
    error?: string;
    class?: string;
    tabIndex?: number;
  }>();
</script>

<template>
    <div class="gap-2 npo-form-control">
        <label v-if="label" class="npo-form-label">{{ label }}</label>

        <div :class="cn(
            'npo-input-wrapper',
             'group',
            error && 'bg-red-100'
            )">
            <slot name="icon" />
            <input
                v-bind="attributes"
                :class="cn(
                    'npo-input',
                     props.class,
                     'placeholder:text-[#9da3b0]'
                 )"
                v-model="model"
                :tabindex="props.tabIndex"
            />
        </div>

        <p v-show="error" class="text-[0.8rem] text-red-500 pl-3">{{ error }}</p>
    </div>
</template>

