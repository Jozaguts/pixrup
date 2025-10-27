<script setup lang="ts">
import type { HTMLAttributes, MouseEvent } from 'vue'
import { cn } from '@/lib/utils'
import { Primitive, type PrimitiveProps } from 'reka-ui'
import { type SidebarMenuButtonVariants, sidebarMenuButtonVariants } from '.'
import { useSidebar } from './utils'

export interface SidebarMenuButtonProps extends PrimitiveProps {
  variant?: SidebarMenuButtonVariants['variant']
  size?: SidebarMenuButtonVariants['size']
  isActive?: boolean
  class?: HTMLAttributes['class']
}

const props = withDefaults(defineProps<SidebarMenuButtonProps>(), {
  as: 'button',
  variant: 'default',
  size: 'default',
})

const { isMobile, setOpenMobile } = useSidebar()

const handleClick = (_event: MouseEvent) => {
  if (isMobile.value) {
    setOpenMobile(false)
  }
}
</script>

<template>
  <Primitive
    data-slot="sidebar-menu-button"
    data-sidebar="menu-button"
    :data-size="size"
    :data-active="isActive"
    :class="cn(sidebarMenuButtonVariants({ variant, size }), props.class)"
    :as="as"
    :as-child="asChild"
    v-bind="$attrs"
    @click.capture="handleClick"
  >
    <slot />
  </Primitive>
</template>
