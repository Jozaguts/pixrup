<template>
    <div class="p-6 max-w-xl shadow-soft rounded-[12px]">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5 text-indigo-500"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                <polyline points="16 7 22 7 22 13" />
            </svg>
        </div>

        <div class="space-y-4 shadow-neu-in bg-gray-200 rounded-[12px] p-4 px-8 npo-form">
            <div
                v-for="(log, index) in logs"
                :key="index"
                class="shadow-sm bg-background rounded-lg p-4 border-l-4 border-gray-200 hover:border-primary-400 transition-colors duration-200"
            >
        <span class="text-xs font-medium tracking-widest text-gray-400 uppercase">
          {{ log.timestamp }}
        </span>
                <h3 class="text-sm font-semibold text-gray-900 mt-1">
                    {{ log.title }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ log.body }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const logs = ref( [
    {
        id: 'appraisal',
        title: 'Appraisal refreshed',
        timestamp: '2 days ago',
        body: 'PixrWorth pulled latest comps from HouseCanary and recalibrated the AVM.',
    },
    {
        id: 'glow',
        title: 'Glow-Up render ready',
        timestamp: '12 hours ago',
        body: 'AI renovation preview approved and staged for investor deck.',
    },
    {
        id: 'collab',
        title: 'Collab thread active',
        timestamp: '5 minutes ago',
        body: 'Sofia dropped notes on staging priorities for the walkthrough.',
    },
]);
</script>

<style scoped>
 .shadow-soft {
     box-shadow: -5px -5px 15px rgba(255, 255, 255, 0.8),
     5px 5px 15px rgba(0, 0, 0, 0.1);
 }
</style>
