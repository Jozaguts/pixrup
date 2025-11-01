<script setup lang="ts">
import { computed } from 'vue';
import {
    MessageCircle,
    Send,
    Users,
    Wifi,
} from 'lucide-vue-next';
import type {
    PropertyWorkspaceProperty,
    WorkspaceModuleMeta,
} from './types';

interface Props {
    property: PropertyWorkspaceProperty;
    meta?: WorkspaceModuleMeta | null;
    moduleId: string;
}

const props = defineProps<Props>();

const endpointBadge = computed(
    () => props.meta?.endpoint ?? '/api/properties/:id/collab/token',
);

const participants = computed(() => [
    { id: 1, name: 'Marisol Vega', role: 'Owner', status: 'online' },
    { id: 2, name: 'Sofia Graham', role: 'Designer', status: 'typing…' },
    { id: 3, name: 'Leon Chen', role: 'Investor', status: 'offline' },
]);

const messages = computed(() => [
    {
        id: 'msg-1',
        author: 'Sofia Graham',
        timestamp: 'Today • 9:42 AM',
        body: 'Uploaded Glow-Up v2. Take a look at the lighting adjustments on slide 4.',
    },
    {
        id: 'msg-2',
        author: 'Leon Chen',
        timestamp: 'Today • 9:15 AM',
        body: 'Looks great. Can we pull SpyHunt comps under $900K for the appendix?',
    },
    {
        id: 'msg-3',
        author: 'You',
        timestamp: 'Today • 9:10 AM',
        body: 'Copy. I will pipe the latest PixrWorth comps into PixrSeal.',
    },
]);
</script>

<template>
    <div class="flex flex-col gap-6 text-[#1f2937]">
        <header class="flex flex-col gap-4 p-6 neu-surface shadow-neu-out ">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">PixrCollab Channel</h2>
                    <p class="text-sm text-gray-500">
                        Real-time workspace chat, synced with Glow-Up, Worth, and Seal updates.
                    </p>
                </div>
                <span
                    class="hidden items-center gap-2 rounded-full  px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#7c4dff]  sm:inline-flex"
                >
                    API
                    <span class="font-medium">{{ endpointBadge }}</span>
                </span>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.3fr_1fr]">
            <article class="flex flex-col gap-5 neu-surface shadow-neu-out p-6">
                <header class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold">Live Thread</h3>
                        <p class="text-sm text-gray-500">Firebase channel with read receipts and presence out-of-the-box.</p>
                    </div>
                    <Wifi class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <div class="flex flex-col gap-4 p-5 neu-surface shadow-neu-out">
                    <article
                        v-for="message in messages"
                        :key="message.id"
                        class="p-5 neu-surface shadow-neu-in text-sm text-gray-600"
                    >
                        <header class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-gray-400">
                            <span>{{ message.author }}</span>
                            <span>{{ message.timestamp }}</span>
                        </header>
                        <p class="mt-2 text-sm text-[#1f2937]">{{ message.body }}</p>
                    </article>
                </div>

                <form class="flex flex-col gap-3">
                    <textarea
                        rows="3"
                        placeholder="Drop an update or @mention a collaborator"
                        class="px-4 py-3 neu-surface shadow-neu-in text-sm text-gray-600"
                    />
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 self-end rounded-[18px] bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-[8px_8px_18px_rgba(108,72,219,0.45),-6px_-6px_18px_rgba(212,199,255,0.4)] transition hover:shadow-[inset_6px_6px_16px_rgba(86,55,176,0.6),inset_-6px_-6px_16px_rgba(158,132,255,0.6)] focus:outline-none focus:ring-2 focus:ring-[#7c4dff]/60"
                    >
                        <Send class="h-4 w-4" />
                        Send update
                    </button>
                </form>
            </article>

            <aside class="flex flex-col gap-5 rounded-[28px] neu-surface shadow-neu-out p-6">
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Participants</h3>
                    <Users class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <ul class="space-y-3 text-sm text-gray-600">
                    <li
                        v-for="participant in participants"
                        :key="participant.id"
                        class="flex items-center justify-between px-4 py-3 neu-surface shadow-neu-in text-sm text-gray-600"
                    >
                        <div>
                            <p class="font-semibold text-[#1f2937]">{{ participant.name }}</p>
                            <p class="text-xs uppercase tracking-[0.3em] text-gray-400">{{ participant.role }}</p>
                        </div>
                        <span
                            class="rounded-full bg-[#7c4dff]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-[#7c4dff]"
                        >
                            {{ participant.status }}
                        </span>
                    </li>
                </ul>

                <div class="p-4 neu-surface shadow-neu-out">
                    <div class="flex items-center gap-2">
                        <MessageCircle class="h-4 w-4" />
                        Connect this channel to PixrSeal comment threads automatically.
                    </div>
                </div>
            </aside>
        </section>
    </div>
</template>
