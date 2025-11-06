<script setup lang="ts">
import {
    Camera,
    ExternalLink,
    Layers,
    PlayCircle,
    Share2,
} from 'lucide-vue-next';
import type { PropertyWorkspaceProperty, WorkspaceModuleMeta } from './types';

interface Props {
    property: PropertyWorkspaceProperty;
    meta?: WorkspaceModuleMeta | null;
    moduleId: string;
}

const props = defineProps<Props>();
</script>

<template>
    <div class="flex flex-col gap-6 text-[#1f2937]">
        <header class="neu-surface flex flex-col gap-4 p-6 shadow-neu-out">
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-lg font-semibold">PixrVision 3D Tour</h2>
                    <p class="text-sm text-gray-500">
                        Manage Matterport embeds, callouts, and sharing
                        permissions without leaving Pixrup.
                    </p>
                </div>
                <span
                    class="hidden items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold tracking-[0.3em] text-[#7c4dff] uppercase sm:inline-flex"
                >
                    API
                    <span class="font-medium">{{
                        props.meta?.endpoint ?? '/api/properties/:id/vision'
                    }}</span>
                </span>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <article class="neu-surface space-y-5 p-6 shadow-neu-out">
                <div class="neu-surface p-6 shadow-neu-out">
                    <div
                        class="neu-surface relative flex aspect-video items-center justify-center shadow-neu-out"
                    >
                        <button
                            type="button"
                            class="neu-btn flex items-center gap-2 px-5 py-3 text-sm font-semibold text-[#7c4dff]"
                        >
                            <PlayCircle class="h-5 w-5" />
                            Launch tour
                        </button>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">
                        Embed is secured with token:
                        {{ props.meta?.status ?? 'ready' }}.
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div
                        class="neu-surface px-4 py-5 text-sm text-gray-600 shadow-neu-out"
                    >
                        <p
                            class="text-xs tracking-[0.3em] text-gray-400 uppercase"
                        >
                            Highlights
                        </p>
                        <p class="mt-2 font-semibold text-[#1f2937]">
                            18 callouts
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Tagged from Glow-Up assets
                        </p>
                    </div>
                    <div
                        class="neu-surface px-4 py-5 text-sm text-gray-600 shadow-neu-out"
                    >
                        <p
                            class="text-xs tracking-[0.3em] text-gray-400 uppercase"
                        >
                            Guided mode
                        </p>
                        <p class="mt-2 font-semibold text-[#1f2937]">Enabled</p>
                        <p class="mt-1 text-xs text-gray-500">
                            Average watch time 4:12
                        </p>
                    </div>
                    <div
                        class="neu-surface px-4 py-5 text-sm text-gray-600 shadow-neu-out"
                    >
                        <p
                            class="text-xs tracking-[0.3em] text-gray-400 uppercase"
                        >
                            Captured via
                        </p>
                        <p class="mt-2 font-semibold text-[#1f2937]">
                            Matterport Pro3
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Synced 5 days ago
                        </p>
                    </div>
                </div>
            </article>

            <aside
                class="neu-surface flex flex-col gap-5 rounded-[28px] p-6 shadow-neu-out"
            >
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Asset controls</h3>
                    <Layers class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <div
                    class="neu-surface p-4 text-sm text-gray-600 shadow-neu-out"
                >
                    <p class="font-semibold text-[#1f2937]">
                        PixrVision playlist
                    </p>
                    <p class="text-sm text-gray-500">
                        Connect to PixrSeal to embed interactive tour in the
                        investor report.
                    </p>
                </div>

                <div
                    class="neu-surface p-4 text-sm text-gray-600 shadow-neu-out"
                >
                    <p class="font-semibold text-[#1f2937]">Share settings</p>
                    <p class="text-sm text-gray-500">
                        Password protected link — expires in 7 days.
                    </p>
                    <button
                        type="button"
                        class="neu-surface mt-3 inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold tracking-[0.3em] text-[#7c4dff] uppercase shadow-neu-in"
                    >
                        <Share2 class="h-4 w-4" />
                        Copy link
                    </button>
                </div>

                <div class="neu-surface p-4 shadow-neu-out">
                    <div class="flex items-center gap-2">
                        <Camera class="h-4 w-4" />
                        Sync latest panos from mobile capture kit.
                    </div>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 self-start rounded-[18px] bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-[8px_8px_18px_rgba(108,72,219,0.45),-6px_-6px_18px_rgba(212,199,255,0.4)] transition hover:shadow-[inset_6px_6px_16px_rgba(86,55,176,0.6),inset_-6px_-6px_16px_rgba(158,132,255,0.6)] focus:ring-2 focus:ring-[#7c4dff]/60 focus:outline-none"
                >
                    <ExternalLink class="h-4 w-4" />
                    Open Matterport console
                </button>
            </aside>
        </section>
    </div>
</template>
