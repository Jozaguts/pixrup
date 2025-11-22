<script setup lang="ts">
    import { cn } from '@/lib/utils';
    import { Icon } from '@iconify/vue';
    import {Ref, ref} from "vue";
    type TabItem = {
        label: string;
        icon?: string
        index: string | number;
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
    })

    const activeTab:Ref<IndexType> = ref(0);

    function setActiveTab(index: IndexType) {
        activeTab.value = index
    }

</script>

<template>
    <div :class="cn(
    'inline-flex gap-1',
     'rounded-lg p-1',
      'bg-neutral-100 dark:bg-neutral-800 npo-form-shadow'
     )">
        <button
            v-for="{ label, icon, index } in props.items"
            :key="index"
            @click="setActiveTab(index)"
            :class="cn(
                'tab relative flex items-center rounded-md px-6 py-3 transition-colors',
                'text-neutral-500 hover:bg-neutral-200/60 hover:text-black',
                 'dark:text-neutral-400 dark:hover:bg-neutral-700/60',
                activeTab === index && 'shadow-xs active dark:bg-neutral-700 dark:text-neutral-100',
                props.class
            )"
        >
            <Icon v-if="icon" :icon="icon" class="-ml-1 !h-6 w-6" />
            <span class="ml-1.5 text-sm">{{ label }}</span>
        </button>
    </div>

</template>

<style scoped>
.tab::after {
    content: '';
    display: block;
    inset:3px;
    position: absolute;
    border-radius:8px;
}
.tab.active:after {
    box-shadow: inset -2px -2px 5px rgba(255, 255, 255, 1),
    inset 3px 3px 5px rgba(0, 0, 0, 0.1);
}
</style>
