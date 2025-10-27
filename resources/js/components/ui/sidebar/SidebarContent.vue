<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div
    data-slot="sidebar-content"
    data-sidebar="content"
    :class="cn('group-data-[collapsible=icon]:pl-0 group-data-[collapsible=icon]pr-3 shadow-neu-in flex min-h-0 flex-1 flex-col  overflow-x-hidden group-data-[collapsible=icon]:overflow-hidden', props.class)"
  >
    <slot />
  </div>
</template>
