<template>
    <div :class="resolvedWrapperClass">
        <button :class="resolvedButtonClass" @click="showModal = true"/>
        <slot>
            <Icon icon="ph:camera-plus-light" :class="resolvedIconClass" />
        </slot>
        <span class="text-sm">{{props.label || 'New Logo'}}</span>
    </div>

    <div
        v-if="showModal"
        v-auto-animate
        class="fixed inset-0 z-50 flex items-center justify-center"
    >
        <!-- Backdrop -->
        <div
            class="fixed inset-0 bg-black/50"
            @click="closeModal"
        ></div>

        <!-- Modal Content -->
        <div
            class="relative z-50 w-11/12 max-w-md p-6 bg-white rounded-xl shadow-lg"
        >
            <h3 class="text-lg font-semibold mb-4">Select a file</h3>
            <button class="absolute top-4 right-4 text-accent hover:text-accent/60" @click="closeModal">
                ✕
            </button>

            <div class="relative mb-2 flex flex-col items-center justify-center bg-gray-100 border-2 border-black border-dashed w-full h-48 rounded-[12px]">
                <p class="text-center mb-2 text-accent font-medium text-sm">
                    Upload a new logo. <br>
                    <strong>Max file size:</strong> 2mb <br>
                    <strong>Formats accepted:</strong> png, jpg, jpeg<br>
                    <span v-if="file">
                        <strong>File Name:</strong>  {{fileName +'.'+ fileExtension }}
                    </span>
                </p>
                <input
                    name="logo"
                    type="file"
                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                    @change="handleFileUpload"/>
                <button class="px-3 mb-3 py-1 text-white bg-black rounded-[8px] shadow-md">
                    <Icon icon="ph:file-plus-duotone" class="w-6 h-6 inline mr-1" />
                    {{ file ? 'Change file' : 'Choose file' }}
                </button>
            </div>
            <button :disabled="!file || errors.length > 0" :class="[
                        'px-5 py-4 w-full bg-black text-white rounded-[8px] shadow-md hover:bg-gray-800 mt-2',
                        'disabled:shadow-none disabled:bg-black/10 disabled:text-accent/50 disabled:cursor-not-allowed'
                       ]"
                    @click="handleFileSave">
                Save logo
            </button>

            <ul class="mt-2 text-accent/80 text-xs list-item list-inside text-center">
                <li v-for="(error, index) in errors" :key="index">{{ error }}.</li>
            </ul>
        </div>
    </div>
</template>

<script setup lang="ts">
import {reactive, ref, computed} from 'vue';
import { Icon } from '@iconify/vue';
import { ZodObject, ZodString, ZodType } from 'zod';
import { cn } from '@/lib/utils'
import { ClassValue } from "clsx";
const props = defineProps<{
    schema: ZodObject<{
        name: ZodString;
        image: ZodType<File>;
    }>;
    label?: string;
    wrapperClass?: ClassValue;
    buttonClass?: ClassValue;
    iconClass?: ClassValue;
}>();
const emit = defineEmits<{
    (e: 'logo-uploaded', payload: { file: File; name: string }): void;
}>();
const file = ref<File | null>(null);
const fileName = ref<string>('');
const fileExtension= computed(() => file.value?.name?.split('.')?.pop() || '');
const errors = reactive<string[]>([]);
const showModal = ref(false);

const resolvedWrapperClass=cn([
    'relative flex h-24 w-24 cursor-pointer flex-col items-center justify-center'
    ,'rounded-[12px] border-2 border-dashed border-black bg-surface',
    'hover:bg-gray-300 hover:font-semibold',
    props.wrapperClass
]);
const resolvedButtonClass=cn([
    'absolute inset-0 h-full w-full cursor-pointer opacity-0',
    props.buttonClass
]);
const resolvedIconClass=cn('w-8 h-8', props.iconClass);

function handleFileUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    errors.splice(0);
    file.value = target.files?.[0] || null;
    if(!file.value) {
        errors.splice(0, errors.length, 'No file selected');
        return;
    }
    fileName.value = prompt('Provide a name for the file:', file.value?.name.split('.')[0] || '') as string;

   const result = props.schema.safeParse({
        name: fileName.value,
        image: file.value
   });

    if(!result.success) {
        errors.splice(0, errors.length, ...result.error.issues.map(e => e.message));
        return;
    }
}

function closeModal() {
    showModal.value = false;
    file.value = null;
    fileName.value = '';
    errors.splice(0);
}

function handleFileSave() {
    if(file.value && errors.length === 0) {
        emit('logo-uploaded', {
            file: file.value,
            name: fileName.value
        });
        closeModal();
    }
}
</script>
