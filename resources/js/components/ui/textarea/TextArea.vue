<script lang="ts" setup>
defineOptions({ inheritAttrs: false });

const props = withDefaults(
    defineProps<{
        modelValue: string;
        label?: string;
        placeholder?: string;
        rows?: number;
        disabled?: boolean;
        readonly?: boolean;
    }>(),
    {
        modelValue: '',
        rows: 6,
        placeholder: '',
        label: '',
        disabled: false,
        readonly: false,
    },
);

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void;
}>();

const handleInput = (event: Event) => {
    emit('update:modelValue', (event.target as HTMLTextAreaElement).value);
};
</script>

<template>
    <label class="flex w-full flex-col gap-2">
        <span
            v-if="props.label"
            class="text-sm font-semibold text-[#6b7280]"
        >
            {{ props.label }}
        </span>
        <textarea
            class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm text-[#1f2937] shadow-inner outline-none transition focus:border-[#7c4dff] disabled:cursor-not-allowed disabled:opacity-60"
            :rows="props.rows"
            :placeholder="props.placeholder"
            :value="props.modelValue"
            :disabled="props.disabled"
            :readonly="props.readonly"
            @input="handleInput"
        />
    </label>
</template>
