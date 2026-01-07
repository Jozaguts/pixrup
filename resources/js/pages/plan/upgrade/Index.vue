<script setup lang="ts">
import NeuphormicButton from "@/components/neuphormic-button.vue";
</script>

<template>
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center max-w-md p-6">
            <img
                src="/images/under-construction.svg"
                alt="Under construction"
                class="mx-auto mb-6 w-96 h-auto"
            />

            <h1 class="text-2xl font-semibold text-gray-800 mb-2">
                Page Under Construction
            </h1>

            <p class="text-gray-600 mb-6">
                We’re working hard to bring this feature to you. Please check back soon.
            </p>

            <NeuphormicButton
                to="/dashboard"
                label="Go back home"
                class="text-accent"
            />
        </div>
    </div>
</template>
