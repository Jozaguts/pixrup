<script lang="ts" setup>
import GuestLayout from '@/layouts/GuestLayout.vue';
const props = withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>
<template>
    <GuestLayout :can-register="props.canRegister">
        <template #main>
            <h2 class="text-primary">services</h2>
        </template>
    </GuestLayout>
</template>
