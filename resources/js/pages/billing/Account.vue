<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import billingRoutes from '@/routes/billing';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billing',
        href: billingRoutes.account().url,
    },
];
</script>

<template>
    <Head title="Billing" />
    <AppLayout :breadcrumbs="breadcrumbs"> </AppLayout>
</template>

<style scoped></style>
