<script setup lang="ts">
import {Icon} from "@iconify/vue"
import {ref, defineEmits, watch} from "vue";
import { Image } from "@/types";
import { cn } from "@/lib/utils";
import { ClassValue } from "clsx";
const props = defineProps<{
    items: Image[],
    activeItem?: string
    wrapperClass?: ClassValue,
    iconClass?: ClassValue
}>();
const emit = defineEmits<{
    (e: 'onChange', value: Image): void
}>();
const selectedAvatar = ref(props.activeItem);

const resolvedWrapperClass =cn([
    'relative flex h-24 w-24 cursor-pointer',
    'items-center justify-center-safe overflow-hidden',
    'rounded-[12px] border-8 border-white transition-all',
    props.wrapperClass
]);
const resolvedCheckIconClass = cn([
    'bg-black/60 p-3 absolute inset-0 z-20 flex items-center justify-center',
    props.iconClass
]);

function isItemSelected(name: string) {
    return selectedAvatar.value === name;
}
function setSelectedAvatar(img: Image) {
    selectedAvatar.value = img.name;
    emit('onChange', img );
}

watch(() => props.activeItem, v => {
    selectedAvatar.value = v;
});
</script>

<template>
    <template v-if="props.items.length">
        <div
            v-for="(x) in props.items"
            :key="x.name"
            :class="resolvedWrapperClass"
            :title="x.name"
        >
            <input
                class="absolute inset-0 z-20 opacity-0"
                type="radio"
                name="avatar"
                :value="x.name"
                @change.prevent="setSelectedAvatar(x)"
            />
            <img
                :alt="x.name"
                :src="x.uri"
                class="h-full w-full object-cover z-10"
            />
            <span v-if="isItemSelected(x.name)" :class="resolvedCheckIconClass">
                <slot>
                    <Icon icon="mdi:check-circle-outline" class="h-6 w-6 text-white/60"/>
                </slot>
            </span>
        </div>
    </template>
</template>
