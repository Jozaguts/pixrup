<script setup lang="ts">
// import CheckIcon from '@/components/icons/CheckIcon.vue';

defineProps<{ block: any }>();

const animationAttrs = (item: any, index: number) => {
    if (!item.animate) return {};

    return {
        'data-ns-animate': '',
        'data-delay': item.delay ?? `${(index + 1) * 0.1}`,
    };
};
</script>
<template>
    <ul class="space-y-4 pt-2 mb-4">
        <li
            v-for="(item, i) in block.data.items"
            :key="i"
            class="flex items-center gap-3"
            v-bind="animationAttrs(item, i)"
        >
                        <span class="flex size-5 items-center justify-center rounded-full bg-secondary dark:bg-accent">
                             <svg
                                 xmlns="http://www.w3.org/2000/svg"
                                 width="10"
                                 height="7"
                                 viewBox="0 0 10 7"
                                 fill="none"
                                 aria-hidden="true"
                                 class="fill-white dark:fill-secondary"
                             >
        <path
            d="M4.31661 6.75605L9.74905 1.42144C10.0836 1.0959 10.0836 0.569702 9.74905 0.244158C9.41446 -0.081386 8.87363 -0.081386 8.53904 0.244158L3.7116 4.99012L1.46096 2.78807C1.12636 2.46253 0.585538 2.46253 0.250945 2.78807C-0.0836483 3.11362 -0.0836483 3.63982 0.250945 3.96536L3.1066 6.75605C3.27347 6.91841 3.49253 7 3.7116 7C3.93067 7 4.14974 6.91841 4.31661 6.75605Z"
        ></path>
      </svg>
                        </span>
            <span class="text-tagline-1 font-normal text-secondary dark:text-accent">
                {{ item?.text }}
            </span>
        </li>
    </ul>
</template>
