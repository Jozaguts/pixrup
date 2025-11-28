<script setup lang="ts">
import {useStatus} from '@/composables/useStatus';
import {useBadgeClasses} from '@/composables/useBadgeClasses';
import { computed } from 'vue';
const {status} = defineProps<{
    status: string;
}>();
const {normalizeStatus} = useStatus();
const{getBadgeClasses} = useBadgeClasses();

const normalizedStatus = computed(() => normalizeStatus(status));
const badgeClasses = computed(() => getBadgeClasses(normalizedStatus.value))
</script>

<template>
    <span class="inline-flex items-center gap-2 rounded-[12px] px-3 py-1 font-semibold" :class="badgeClasses">
        {{ normalizedStatus }}
    </span>
</template>
