<script setup lang="ts">
import  { computed, HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
import { useSidebar } from './utils'
const {open} = useSidebar()

</script>
<template>
  <div
    data-slot="sidebar-group"
    data-sidebar="group"
    :class="cn(`relative  pt-4 flex w-full min-w-0 flex-col px-${open ? '3 ':'0'}`, props.class)"
  >
    <slot/>
  </div>
</template>
