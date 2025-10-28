<script setup lang="ts">
import {
    Camera,
    ExternalLink,
    Layers,
    PlayCircle,
    Share2,
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
</script>

<template>
    <div class="flex flex-col gap-6 text-[#1f2937]">
        <header class="flex flex-col gap-4 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(206,208,223,0.45),-10px_-10px_28px_rgba(255,255,255,0.94)]">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">PixrVision 3D Tour</h2>
                    <p class="text-sm text-gray-500">
                        Manage Matterport embeds, callouts, and sharing permissions without leaving Pixrup.
                    </p>
                </div>
                <span
                    class="hidden items-center gap-2 rounded-full bg-[#f7f4ff] px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#7c4dff] shadow-[4px_4px_12px_rgba(186,162,255,0.3),-4px_-4px_12px_rgba(255,255,255,0.95)] sm:inline-flex"
                >
                    API
                    <span class="font-medium">{{ props.meta?.endpoint ?? '/api/properties/:id/vision' }}</span>
                </span>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <article class="space-y-5 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(206,208,223,0.45),-10px_-10px_28px_rgba(255,255,255,0.94)]">
                <div class="rounded-[28px] bg-[#f7f8fe] p-6 shadow-[inset_14px_14px_34px_rgba(201,203,217,0.6),inset_-14px_-14px_34px_rgba(255,255,255,0.92)]">
                    <div
                        class="relative flex aspect-video items-center justify-center rounded-[22px] bg-gradient-to-br from-[#7c4dff]/20 via-[#f4f5fa] to-[#ffffff] shadow-[6px_6px_18px_rgba(200,200,216,0.45),-6px_-6px_18px_rgba(255,255,255,0.95)]"
                    >
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-[#7c4dff] shadow-[8px_8px_20px_rgba(186,162,255,0.4),-8px_-8px_20px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_8px_8px_18px_rgba(167,148,228,0.55),inset_-8px_-8px_18px_rgba(242,236,255,0.9)] focus:outline-none focus:ring-2 focus:ring-[#7c4dff]/60"
                        >
                            <PlayCircle class="h-5 w-5" />
                            Launch tour
                        </button>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">
                        Embed is secured with token: {{ props.meta?.status ?? 'ready' }}.
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-[20px] bg-[#f4f5fa] px-4 py-5 text-sm text-gray-600 shadow-[inset_10px_10px_22px_rgba(199,202,220,0.6),inset_-10px_-10px_22px_rgba(255,255,255,0.92)]">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Highlights</p>
                        <p class="mt-2 font-semibold text-[#1f2937]">18 callouts</p>
                        <p class="mt-1 text-xs text-gray-500">Tagged from Glow-Up assets</p>
                    </div>
                    <div class="rounded-[20px] bg-[#f4f5fa] px-4 py-5 text-sm text-gray-600 shadow-[inset_10px_10px_22px_rgba(199,202,220,0.6),inset_-10px_-10px_22px_rgba(255,255,255,0.92)]">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Guided mode</p>
                        <p class="mt-2 font-semibold text-[#1f2937]">Enabled</p>
                        <p class="mt-1 text-xs text-gray-500">Average watch time 4:12</p>
                    </div>
                    <div class="rounded-[20px] bg-[#f4f5fa] px-4 py-5 text-sm text-gray-600 shadow-[inset_10px_10px_22px_rgba(199,202,220,0.6),inset_-10px_-10px_22px_rgba(255,255,255,0.92)]">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Captured via</p>
                        <p class="mt-2 font-semibold text-[#1f2937]">Matterport Pro3</p>
                        <p class="mt-1 text-xs text-gray-500">Synced 5 days ago</p>
                    </div>
                </div>
            </article>

            <aside class="flex flex-col gap-5 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(206,208,223,0.45),-10px_-10px_28px_rgba(255,255,255,0.94)]">
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Asset controls</h3>
                    <Layers class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <div class="rounded-[20px] bg-[#f4f5fa] p-4 text-sm text-gray-600 shadow-[inset_8px_8px_20px_rgba(199,202,220,0.6),inset_-8px_-8px_20px_rgba(255,255,255,0.92)]">
                    <p class="font-semibold text-[#1f2937]">PixrVision playlist</p>
                    <p class="text-sm text-gray-500">Connect to PixrSeal to embed interactive tour in the investor report.</p>
                </div>

                <div class="rounded-[20px] bg-[#f4f5fa] p-4 text-sm text-gray-600 shadow-[inset_8px_8px_20px_rgba(199,202,220,0.6),inset_-8px_-8px_20px_rgba(255,255,255,0.92)]">
                    <p class="font-semibold text-[#1f2937]">Share settings</p>
                    <p class="text-sm text-gray-500">Password protected link — expires in 7 days.</p>
                    <button
                        type="button"
                        class="mt-3 inline-flex items-center gap-2 rounded-[16px] bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#7c4dff] shadow-[6px_6px_16px_rgba(200,200,216,0.45),-6px_-6px_16px_rgba(255,255,255,0.95)]"
                    >
                        <Share2 class="h-4 w-4" />
                        Copy link
                    </button>
                </div>

                <div class="rounded-[20px] bg-[#f7f4ff] p-4 text-sm text-[#7c4dff] shadow-[6px_6px_16px_rgba(186,162,255,0.4),-6px_-6px_16px_rgba(255,255,255,0.95)]">
                    <div class="flex items-center gap-2">
                        <Camera class="h-4 w-4" />
                        Sync latest panos from mobile capture kit.
                    </div>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 self-start rounded-[18px] bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-[8px_8px_18px_rgba(108,72,219,0.45),-6px_-6px_18px_rgba(212,199,255,0.4)] transition hover:shadow-[inset_6px_6px_16px_rgba(86,55,176,0.6),inset_-6px_-6px_16px_rgba(158,132,255,0.6)] focus:outline-none focus:ring-2 focus:ring-[#7c4dff]/60"
                >
                    <ExternalLink class="h-4 w-4" />
                    Open Matterport console
                </button>
            </aside>
        </section>
    </div>
</template>
