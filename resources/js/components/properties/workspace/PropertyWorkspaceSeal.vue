<script setup lang="ts">
import {
    Download,
    FileText,
    LayoutTemplate,
    Settings2,
    Stamp,
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
        <header class="flex flex-col gap-4 neu-surface p-6 shadow-neu-out">
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-lg font-semibold">
                        PixrSeal Report Builder
                    </h2>
                    <p class="text-sm text-gray-500">
                        Assemble investor-ready PDFs, exportable decks, and
                        white-labeled collateral.
                    </p>
                </div>
                <span
                    class="hidden items-center gap-2 neu-surface px-4 py-2 text-xs font-semibold tracking-[0.3em] text-[#7c4dff] uppercase shadow-neu-out sm:inline-flex"
                >
                    API
                    <span class="font-medium">{{
                        props.meta?.endpoint ?? '/api/properties/:id/report'
                    }}</span>
                </span>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <article class="space-y-5 neu-surface p-6 shadow-neu-out">
                <header class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold">Report Timeline</h3>
                        <p class="text-sm text-gray-500">
                            Auto-sync modules, drag-and-drop sections, render
                            instantly.
                        </p>
                    </div>
                    <Stamp class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <ol class="space-y-4 text-sm text-gray-600">
                    <li class="neu-surface p-5 shadow-neu-in">
                        <p
                            class="text-xs tracking-[0.3em] text-gray-400 uppercase"
                        >
                            1. Overview
                        </p>
                        <p class="mt-1 font-semibold text-[#1f2937]">
                            Executive Summary
                        </p>
                        <p class="text-sm text-gray-500">
                            Pulls from the Overview module and automates hero
                            metrics.
                        </p>
                    </li>
                    <li class="neu-surface p-5 shadow-neu-in">
                        <p
                            class="text-xs tracking-[0.3em] text-gray-400 uppercase"
                        >
                            2. Insights
                        </p>
                        <p class="mt-1 font-semibold text-[#1f2937]">
                            Valuation & Market Intel
                        </p>
                        <p class="text-sm text-gray-500">
                            Combines PixrWorth comps and SpyHunt heatmap
                            snapshots.
                        </p>
                    </li>
                    <li class="neu-surface p-5 shadow-neu-in">
                        <p
                            class="text-xs tracking-[0.3em] text-gray-400 uppercase"
                        >
                            3. Visuals
                        </p>
                        <p class="mt-1 font-semibold text-[#1f2937]">
                            Glow-Up Storyboard
                        </p>
                        <p class="text-sm text-gray-500">
                            Select AI renders to embed, with auto captions per
                            scenario.
                        </p>
                    </li>
                </ol>

                <div class="neu-surface p-6 shadow-neu-in">
                    <p class="text-sm text-gray-500">Need a different look?</p>
                    <button
                        type="button"
                        class="mt-3 inline-flex items-center gap-2 rounded-[18px] bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-[8px_8px_18px_rgba(108,72,219,0.45),-6px_-6px_18px_rgba(212,199,255,0.4)] transition hover:shadow-[inset_6px_6px_16px_rgba(86,55,176,0.6),inset_-6px_-6px_16px_rgba(158,132,255,0.6)] focus:ring-2 focus:ring-[#7c4dff]/60 focus:outline-none"
                    >
                        <LayoutTemplate class="h-4 w-4" />
                        Switch template
                    </button>
                </div>
            </article>

            <aside class="flex flex-col gap-5 neu-surface p-6 shadow-neu-out">
                <header class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Export & settings</h3>
                    <Settings2 class="h-5 w-5 text-[#7c4dff]" />
                </header>

                <div class="space-y-4 text-sm text-gray-600">
                    <div class="neu-surface p-4 shadow-neu-out">
                        <p class="font-semibold text-[#1f2937]">Branding</p>
                        <p class="text-sm text-gray-500">
                            Using Pixrup default, includes agent contact block
                            and disclaimers.
                        </p>
                    </div>

                    <div class="neu-surface p-4 shadow-neu-out">
                        <p class="font-semibold text-[#1f2937]">Distribution</p>
                        <p class="text-sm text-gray-500">
                            Auto send to PixrCollab channel and investor
                            contacts.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 self-start rounded-[18px] bg-[#7c4dff] px-4 py-2 text-sm font-semibold text-white shadow-[8px_8px_18px_rgba(108,72,219,0.45),-6px_-6px_18px_rgba(212,199,255,0.4)] transition hover:shadow-[inset_6px_6px_16px_rgba(86,55,176,0.6),inset_-6px_-6px_16px_rgba(158,132,255,0.6)] focus:ring-2 focus:ring-[#7c4dff]/60 focus:outline-none"
                >
                    <Download class="h-4 w-4" />
                    Generate PDF
                </button>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 self-start rounded-[18px] bg-white px-4 py-2 text-sm font-semibold text-[#7c4dff] shadow-[6px_6px_16px_rgba(200,200,216,0.45),-6px_-6px_16px_rgba(255,255,255,0.95)] transition hover:shadow-[inset_6px_6px_16px_rgba(179,176,220,0.45),inset_-6px_-6px_16px_rgba(244,240,255,0.85)] focus:ring-2 focus:ring-[#7c4dff]/60 focus:outline-none"
                >
                    <FileText class="h-4 w-4" />
                    Preview in browser
                </button>
            </aside>
        </section>
    </div>
</template>
