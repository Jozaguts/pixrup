<script setup lang="ts">
import { computed } from 'vue';
import {
    Camera,
    CloudUpload,
    Image as ImageIcon,
    RefreshCw,
    Sparkles,
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
    () => props.meta?.endpoint ?? '/api/properties/:id/glowup/jobs',
);

const scenarios = computed(() => [
    {
        id: 'modern',
        title: 'Modern Minimalist',
        subtitle: 'Neutral palette, brass accents, widened openings',
        eta: '3 min',
    },
    {
        id: 'lux',
        title: 'Lux Warm',
        subtitle: 'Walnut millwork, feature lighting, velvet textures',
        eta: '4 min',
    },
    {
        id: 'outdoor',
        title: 'Outdoor Resort',
        subtitle: 'Pool cabana concept + landscape lighting',
        eta: '6 min',
    },
]);
</script>

<template>
    <div class="flex flex-col gap-6 text-[#1f2937]">
        <header class="flex flex-col gap-4 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(206,208,223,0.45),-10px_-10px_28px_rgba(255,255,255,0.94)]">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">PixrGlowUp Studio</h2>
                    <p class="text-sm text-gray-500">
                        Generate AI before/after sets and manage renovation storyboards.
                    </p>
                </div>
                <span
                    class="hidden items-center gap-2 rounded-full bg-[#f7f4ff] px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#7c4dff] shadow-[4px_4px_12px_rgba(186,162,255,0.3),-4px_-4px_12px_rgba(255,255,255,0.95)] sm:inline-flex"
                >
                    API
                    <span class="font-medium">{{ endpointBadge }}</span>
                </span>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.3fr_1fr]">
            <article class="flex flex-col gap-6 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(206,208,223,0.45),-10px_-10px_28px_rgba(255,255,255,0.94)]">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="rounded-[24px] bg-[#f4f5fa] p-6 shadow-[inset_12px_12px_26px_rgba(199,202,220,0.6),inset_-12px_-12px_26px_rgba(255,255,255,0.92)]">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Upload photos</p>
                        <p class="mt-2 text-sm text-gray-500">
                            Drop JPG or HEIC up to 15MB. GlowUp aligns images to camera POV automatically.
                        </p>
                        <button
                            type="button"
                            class="mt-4 inline-flex items-center gap-2 rounded-[18px] bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-[8px_8px_18px_rgba(108,72,219,0.45),-6px_-6px_18px_rgba(212,199,255,0.4)] transition hover:shadow-[inset_6px_6px_16px_rgba(86,55,176,0.6),inset_-6px_-6px_16px_rgba(158,132,255,0.6)] focus:outline-none focus:ring-2 focus:ring-[#7c4dff]/60"
                        >
                            <CloudUpload class="h-4 w-4" />
                            Upload set
                        </button>
                    </div>

                    <div class="rounded-[24px] bg-[#f4f5fa] p-6 shadow-[inset_12px_12px_26px_rgba(199,202,220,0.6),inset_-12px_-12px_26px_rgba(255,255,255,0.92)]">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400">Capture in app</p>
                        <p class="mt-2 text-sm text-gray-500">
                            Use the Pixrup mobile app to sync room scans with floor-plan overlays.
                        </p>
                        <button
                            type="button"
                            class="mt-4 inline-flex items-center gap-2 rounded-[18px] bg-white px-4 py-2 text-sm font-semibold text-[#7c4dff] shadow-[6px_6px_16px_rgba(200,200,216,0.45),-6px_-6px_16px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_6px_6px_16px_rgba(179,176,220,0.5),inset_-6px_-6px_16px_rgba(244,240,255,0.85)] focus:outline-none focus:ring-2 focus:ring-[#7c4dff]/50"
                        >
                            <Camera class="h-4 w-4" />
                            Launch camera
                        </button>
                    </div>
                </div>

                <div class="rounded-[24px] bg-[#f7f8fe] p-6 shadow-[inset_10px_10px_24px_rgba(201,203,217,0.6),inset_-10px_-10px_24px_rgba(255,255,255,0.92)]">
                    <header class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold">Scenario presets</h3>
                            <p class="text-sm text-gray-500">Pick a style kit to generate renderings instantly.</p>
                        </div>
                        <Sparkles class="h-5 w-5 text-[#7c4dff]" />
                    </header>

                    <div class="mt-5 grid gap-4 md:grid-cols-3">
                        <label
                            v-for="scenario in scenarios"
                            :key="scenario.id"
                            class="flex cursor-pointer flex-col gap-3 rounded-[20px] bg-white px-4 py-5 text-sm text-gray-600 shadow-[6px_6px_16px_rgba(200,200,216,0.45),-6px_-6px_16px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_6px_6px_16px_rgba(179,176,220,0.45),inset_-6px_-6px_16px_rgba(244,240,255,0.85)]"
                        >
                            <span class="text-sm font-semibold text-[#1f2937]">{{ scenario.title }}</span>
                            <span class="text-xs text-gray-500">{{ scenario.subtitle }}</span>
                            <span class="text-xs font-semibold uppercase tracking-[0.3em] text-[#7c4dff]"
                                >ETA {{ scenario.eta }}</span
                            >
                        </label>
                    </div>
                </div>
            </article>

            <aside class="flex flex-col gap-5 rounded-[28px] bg-white p-6 shadow-[10px_10px_28px_rgba(206,208,223,0.45),-10px_-10px_28px_rgba(255,255,255,0.94)]">
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Latest outputs</h3>
                    <RefreshCw class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <div class="flex flex-col gap-4">
                    <div class="rounded-[20px] bg-[#f4f5fa] p-4 shadow-[inset_8px_8px_20px_rgba(199,202,220,0.6),inset_-8px_-8px_20px_rgba(255,255,255,0.92)]">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-[16px] bg-white shadow-[6px_6px_16px_rgba(202,204,220,0.5),-6px_-6px_16px_rgba(255,255,255,0.92)]">
                                <ImageIcon class="h-5 w-5 text-[#7c4dff]" />
                            </span>
                            <div>
                                <p class="font-semibold text-[#1f2937]">Kitchen Glow-Up</p>
                                <p class="text-sm text-gray-500">Before / After v2 • 4 assets</p>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-gray-500">
                            Shared with investor deck and PixrSeal module.
                        </p>
                    </div>

                    <div class="rounded-[20px] bg-[#f4f5fa] p-4 shadow-[inset_8px_8px_20px_rgba(199,202,220,0.6),inset_-8px_-8px_20px_rgba(255,255,255,0.92)]">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-[16px] bg-white shadow-[6px_6px_16px_rgba(202,204,220,0.5),-6px_-6px_16px_rgba(255,255,255,0.92)]">
                                <ImageIcon class="h-5 w-5 text-[#7c4dff]" />
                            </span>
                            <div>
                                <p class="font-semibold text-[#1f2937]">Curb Appeal</p>
                                <p class="text-sm text-gray-500">Landscape + Lighting • 6 assets</p>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-gray-500">
                            Synced to PixrVision tour thumbnails.
                        </p>
                    </div>
                </div>
            </aside>
        </section>
    </div>
</template>
