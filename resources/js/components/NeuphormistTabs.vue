<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Icon } from '@iconify/vue';
import { Ref, ref } from 'vue';
type TabItem = {
    label: string;
    icon?: string;
    id: string | number;
    [key: string]: any;
};
type IndexType = string | number;
const props = defineProps({
    items: {
        default: () => [],
        type: Array as () => TabItem[],
    },
    class: {
        type: String,
        default: '',
    },
    value: {
        type: [String, Number],
        default: 0,
    },
    parentClasses: {
        type: String,
        default: '',
    },
    togglable: {
        type: Boolean,
        default: true,
    },
        isDisabled: {
            type: Boolean,
            default: false,
        },
});
const emit = defineEmits(['onchange']);

const activeTab: Ref<IndexType> = ref('address');

function setActiveTab(item: TabItem) {
        // if(props.isDisabled) return;
    emit('onchange', item);
    if (props.togglable) {
        activeTab.value = item.id;
        return;
    }
}
</script>

<template>
    <div
        :class="
            cn(
                'grid grid-cols-2 gap-1 md:flex md:flex-row lg:flex lg:flex-row',
                'rounded-lg p-2',
                'npo-form-shadow text-accent',
                'w-full',
                props.parentClasses,
            )
        "
    >
        <button
            v-for="item in props.items"
            :key="item.id"
            @click="setActiveTab(item)"
            :class="
                cn(
                    'tab relative flex items-center rounded-md px-6 py-3 transition-colors duration-200',
                    value === item.id && '!text-accent active',
                    props.class,
                )
            "
        >
            <Icon v-if="item.icon" :icon="item.icon" class="-ml-1 !h-5 w-5 text-accent" />
            <span class="ml-1.5 text-sm text-accent">{{ item.label }}</span>
        </button>
    </div>
</template>

<style scoped>
.tab::after {
    content: '';
    display: block;
    inset: 3px;
    position: absolute;
    border-radius: 8px;
}
.tab.active:after {
    box-shadow:
        inset -2px -2px 5px rgba(255, 255, 255, 1),
        inset 3px 3px 5px rgba(0, 0, 0, 0.1);
}
.dark .tab.active::after {
    box-shadow:
        inset -2px -2px 5px rgba(255, 255, 255, 0.06),
        inset 3px 3px 6px rgba(0, 0, 0, 0.65);
}
</style>
