<script setup lang="ts">
import GlowUpPage from './glowup/GlowUpPage.vue';
import type { PropertyWorkspaceProperty, WorkspaceModuleMeta } from './types';

interface Props {
    property: PropertyWorkspaceProperty;
    meta?: WorkspaceModuleMeta | null;
    moduleId: string;
}

const props = defineProps<Props>();
</script>

<template>
    <GlowUpPage
        :property-id="props.property.id"
        :glow-up="props.property.glowUp ?? null"
    />
</template>
