<script setup lang="ts">
import type { SpyHuntComparable } from '@/components/properties/workspace/spyhunt/types';
import type { PropertyWorkspaceProperty, SpyHunt, WorkspaceModuleMeta } from '@/components/properties/workspace/types';
import { computed, onBeforeUnmount, onMounted, ref, toRefs } from 'vue';
import { Icon } from '@iconify/vue';
import SpyHuntWorkSpaceSkeleton from '@/components/skeleton/SpyHuntWorkSpaceSkeleton.vue';
import useSpyHunt from '@/composables/useSpyHunt';
import spyHuntRoutes from '@/routes/properties/spyhunt';
import L, { LeafletMouseEvent } from 'leaflet';
import type { LatLng, LatLngBounds, LatLngTuple } from 'leaflet';
import type { ViewChangedEvent } from 'vue-use-leaflet';
import {
    VMap,
    VMapAttributionControl,
    VMapGoogleTileLayer,
    VMapMarker,
    VMapPinIcon,
    VMapZoomControl,
} from 'vue-map-ui';
import MarketOverviewCard from '@/components/properties/workspace/spyhunt/MarketOverviewCard.vue';
import NeuphormistTabs from '@/components/NeuphormistTabs.vue';
import WorkspaceModuleHeader from '@/components/properties/workspace/WorkspaceModuleHeader.vue';
const { spyhunt, avgPrice, dayOnMarket, radius, mode, comparables, trend30d, spyHuntProperty, avgPerSqft } =
    toRefs(useSpyHunt());

interface Props {
    property: PropertyWorkspaceProperty;
    meta: WorkspaceModuleMeta;
    moduleId: string;
}

const props = defineProps<Props>();
const loading = ref(false);
const center = ref<LatLngTuple | LatLng>([0, 0]);

const zoom = ref(13);
const bounds = ref<LatLngBounds | null>(null);

function onViewChanged(e: ViewChangedEvent) {
    center.value = e.center;
    zoom.value = e.zoom;
    bounds.value = e.bounds;
}
async function loadSpyHunt(force = false) {
    loading.value = true;
    const options = force ? { query: { force: 1 } } : undefined;
    const res = await fetch(spyHuntRoutes.fetch.get(props.property.id, options).url, {
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
    });
    const json = await res.json();
    spyhunt.value = json.data as SpyHunt;
    center.value = [spyhunt.value.property.lat, spyhunt.value.property.lng];

    loading.value = false;
}

const activeMarker = ref<LeafletMouseEvent | null>(null);
const activeComparable = ref<SpyHuntComparable>();
const markerScreenPos = ref({ x: 0, y: 0 });
const hideMarkerTimeout = ref<number | null>(null);
const isMobile = ref(false);
const viewport = ref({ width: 0, height: 0 });

const updateViewport = () => {
    if (typeof window === 'undefined') {
        return;
    }
    viewport.value = { width: window.innerWidth, height: window.innerHeight };
    isMobile.value = window.innerWidth < 768;
};

const tooltipStyle = computed(() => {
    if (!activeMarker.value) {
        return {};
    }

    const mapSize = activeMarker.value?.target?._map?.getSize?.();
    const containerWidth = mapSize?.x ?? viewport.value.width;
    const containerHeight = mapSize?.y ?? viewport.value.height;

    const baseLeft = markerScreenPos.value.x;
    const baseTop = markerScreenPos.value.y;

    if (!isMobile.value) {
        return {
            top: `${baseTop}px`,
            left: `${baseLeft + 200}px`,
            transform: 'translate(-50%, -100%)',
        };
    }

    const padding = 16;
    const maxWidth = 340;
    const tooltipWidth = Math.max(0, Math.min(maxWidth, containerWidth - padding * 2));
    const halfWidth = tooltipWidth / 2;
    const minLeft = padding + halfWidth;
    const maxLeft = containerWidth - padding - halfWidth;

    let left = baseLeft;
    if (maxLeft < minLeft) {
        left = containerWidth / 2;
    } else {
        left = Math.min(Math.max(left, minLeft), maxLeft);
    }

    const minTop = padding + 8;
    const maxTop = containerHeight - padding;
    let top = Math.min(Math.max(baseTop, minTop), maxTop);

    return {
        top: `${top}px`,
        left: `${left}px`,
        transform: 'translate(-50%, -110%)',
    };
});
function clearHideMarker(): void {
    if (hideMarkerTimeout.value === null) {
        return;
    }
    window.clearTimeout(hideMarkerTimeout.value);
    hideMarkerTimeout.value = null;
}

function scheduleHideMarker(): void {
    clearHideMarker();
    hideMarkerTimeout.value = window.setTimeout(() => {
        activeMarker.value = null;
        activeComparable.value = undefined;
    }, 300);
}

function setActiveMarker(marker: LeafletMouseEvent, comparable: SpyHuntComparable): void {
    clearHideMarker();
    activeMarker.value = marker;
    activeComparable.value = useSpyHunt().formatComparable(comparable) as unknown as SpyHuntComparable;
    const point = marker.containerPoint;
    if (!point) {
        return;
    }
    markerScreenPos.value = { x: point.x, y: point.y };
}

function onMarkHover(marker: LeafletMouseEvent, comparable: SpyHuntComparable): void {
    setActiveMarker(marker, comparable);
}

function onMarkClick(marker: LeafletMouseEvent, comparable: SpyHuntComparable): void {
    if (activeComparable.value?.id === comparable.id) {
        scheduleHideMarker();
        return;
    }
    setActiveMarker(marker, comparable);
}
/*
 * COMPUTED
 * */

const isReady = computed(() => !loading.value && !!spyhunt.value.property?.lat);
onMounted(() => {
    updateViewport();
    window.addEventListener('resize', updateViewport);
    loadSpyHunt();
});

onBeforeUnmount(() => {
    clearHideMarker();
    window.removeEventListener('resize', updateViewport);
});
</script>
<template>
    <div class="flex flex-col gap-6 pt-6 text-accent">
        <WorkspaceModuleHeader
            eyebrow="PixrSpyHunt"
            title="Market Overview & Comparables"
            description="RentCast comps, pricing, and demand signals."
        >
            <template #actions>
                <button
                    type="button"
                    :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-[12px] bg-primary/50 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#5f2fe0] disabled:cursor-not-allowed disabled:opacity-70"
                    @click="loadSpyHunt(true)"
                >
                    <Icon icon="ph:arrow-clockwise" :class="['h-4 w-4', { 'animate-spin': loading }]" />
                    Re-fetch
                </button>
            </template>
        </WorkspaceModuleHeader>
        <SpyHuntWorkSpaceSkeleton v-if="loading" />
        <section v-else-if="isReady" class="npo-form-shadow rounded-[12px] bg-surface p-2 shadow-neu-in md:p-4 lg:p-4">
            <div class="flex flex-col gap-4 md:grid md:grid-cols-12 lg:grid lg:grid-cols-12">
                <div
                    class="col-span-1 mt-4 h-full min-h-[600px] rounded-[12px] bg-surface p-2 shadow-neu-in md:col-span-9 md:p-4 lg:col-span-9 lg:p-4"
                >
                    <div class="npo-form-shadow mb-4 flex flex-col rounded-[12px] p-4 md:hidden lg:hidden">
                        <div class="relative mb-6">
                            <label
                                for="labels-range-input"
                                class="text-sm font-semibold tracking-wide text-accent uppercase"
                                >Radius</label
                            >
                            <input
                                id="labels-range-input"
                                type="range"
                                :min="1"
                                :max="5"
                                :step="1"
                                :value="spyhunt.filters.defaults.radius"
                                @change="(v) => (spyhunt.filters.defaults.radius = v?.target?.value)"
                                class="h-4 w-full cursor-pointer appearance-none overflow-hidden rounded-full !bg-[#e4e6ee] shadow-inner"
                            />
                            <span class="text-body absolute start-0 -bottom-6 text-sm">1</span>
                            <span
                                class="text-body absolute start-1/2 -bottom-6 -translate-x-1/2 text-sm rtl:translate-x-1/2"
                                >3</span
                            >
                            <span class="text-body absolute end-0 -bottom-6 text-sm">5</span>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm font-semibold tracking-wide text-accent uppercase">
                                Property type</span
                            >
                            <NeuphormistTabs
                                :value="mode"
                                @onchange="(v) => (spyhunt.filters.defaults.mode = v.id)"
                                :items="[
                                    { id: 'sale', label: 'Sale' },
                                    { id: 'rent', label: 'Rent' },
                                ]"
                                parentClasses="flex w-fit mt-2 "
                            />
                        </div>
                    </div>
                    <VMap
                        :max-zoom="16"
                        :max-zoom-out="10"
                        :scroll-wheel-zoom="false"
                        :center="center"
                        :zoom="zoom"
                        @view-changed="onViewChanged"
                        class="min-h-[600px] rounded-[12px]"
                    >
                        <VMapGoogleTileLayer />
                        <VMapZoomControl />
                        <VMapAttributionControl />
                        <VMapMarker :latlng="[spyhunt.property?.lat, spyhunt?.property?.lng]">
                            <VMapPinIcon color="#6e33ff" root-class="npo-form-shadow">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path
                                        d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z"
                                    />
                                </svg>
                            </VMapPinIcon>
                        </VMapMarker>
                        <VMapMarker
                            v-for="(comp, idx) in comparables"
                            :key="idx"
                            :latlng="[comp.latitude, comp.longitude]"
                            @mouseover="(e) => onMarkHover(e, comp)"
                            @mouseout="scheduleHideMarker"
                            @click="(e) => onMarkClick(e, comp)"
                        >
                            <VMapPinIcon
                                :color="comp.status === 'Active' ? '#3B82F6' : '#16A34A'"
                                root-class="npo-form-shadow"
                            >
                            </VMapPinIcon>
                        </VMapMarker>

                        <transition name="fade">
                            <div
                                v-if="activeMarker"
                                class="min-full m absolute z-[600] max-w-[340px] rounded-lg bg-surface p-4 shadow-neu-in"
                                :style="tooltipStyle"
                                @mouseenter="clearHideMarker"
                                @mouseleave="scheduleHideMarker"
                            >
                                <p class="font-semibold text-accent">{{ activeComparable?.address }}</p>
                                <div class="mt-1 mb-3 flex gap-2 text-accent">
                                    <span class="text-sm">{{ activeComparable?.price }}</span> -
                                    <span class="text-sm">{{ activeComparable?.squareFootage }} sqft</span> -
                                    <span class="text-sm">{{ activeComparable?.pricePerFt }}/ft²</span>
                                </div>
                                <div class="flex gap-x-4">
                                    <div class="inline-flex items-center justify-center text-base text-accent">
                                        <Icon icon="ph:bulldozer-light" class="h-6 w-6"></Icon>
                                        <span class="ml-1">{{ activeComparable?.yearBuilt }}</span>
                                    </div>
                                    <div class="inline-flex items-center justify-center text-base text-accent">
                                        <Icon
                                            icon="material-symbols-light:bedroom-parent-outline"
                                            class="h-6 w-6"
                                        ></Icon>
                                        <span class="ml-1">{{ activeComparable?.bedrooms }}</span>
                                    </div>
                                    <div class="inline-flex items-center justify-center text-base text-accent">
                                        <Icon icon="material-symbols-light:shower-outline" class="h-6 w-6"></Icon>
                                        <span class="ml-1">{{ activeComparable?.bathrooms }}</span>
                                    </div>
                                    <div class="inline-flex items-center justify-center text-sm text-accent">
                                        <Icon icon="game-icons:path-distance" class="h-5 w-5"></Icon>
                                        <span class="ml-1">{{ activeComparable?.distance }}</span>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <section class="absolute top-0 left-0 z-[700] flex flex-col gap-2 p-2 md:flex-row lg:flex-row">
                            <NeuphormistTabs
                                :value="spyhunt.filters.defaults.radius"
                                @onchange="(v) => (spyhunt.filters.defaults.radius = v.id)"
                                :items="radius"
                                parentClasses="hidden"
                            />
                            <NeuphormistTabs
                                :value="mode"
                                @onchange="(v) => (spyhunt.filters.defaults.mode = v.id)"
                                :items="[
                                    { id: 'sale', label: 'Sale' },
                                    { id: 'rent', label: 'Rent' },
                                ]"
                                parentClasses="hidden"
                            />
                        </section>
                        <section
                            class="absolute bottom-0 left-0 z-[700] w-full p-2 md:top-[50%] md:w-fit lg:top-[50%] lg:w-fit"
                        >
                            <div class="npo-form-shadow flex flex-col rounded-[12px] p-4">
                                <div
                                    class="flex max-w-[200px] items-center gap-2 text-sm md:max-w-[300px] md:text-base lg:max-w-[300px] lg:text-base"
                                >
                                    <Icon icon="ph:map-pin-bold" class="h-8 w-8 font-bold text-primary" />
                                    <p class="truncate">{{ spyHuntProperty.title }}</p>
                                </div>
                                <div>
                                    <div class="mt-1 mb-3 flex gap-2 text-on-surface">
                                        <span class="text-sm font-bold">{{ spyHuntProperty.price }}</span> -
                                        <span class="text-sm">{{ spyHuntProperty.square_footage }} sqft</span> -
                                        <span class="text-sm">{{ spyHuntProperty.pricePerFt }}/ft²</span>
                                    </div>
                                    <div class="my-2 w-full text-base font-bold">
                                        <div class="items-center- flex justify-between text-accent">
                                            <p class="text-lg text-on-surface">Low Estimate</p>
                                            <span>{{ spyHuntProperty.lowest_estimate_price }}</span>
                                        </div>
                                        <div
                                            class="relative h-4 w-full overflow-hidden rounded-full bg-surface shadow-neu-in"
                                        >
                                            <div
                                                class="absolute inset-y-0 left-0 w-[70%] rounded-full bg-gradient-to-r from-[#ccf] to-[#6e33ff] transition-all duration-500 ease-out"
                                            />
                                        </div>
                                        <div class="items-center- flex justify-between text-accent">
                                            <p class="text-lg text-on-surface">High Estimate</p>
                                            <span>{{ spyHuntProperty.highest_estimate_price }}</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-x-4 text-on-surface">
                                        <div class="inline-flex items-center justify-center text-base">
                                            <Icon
                                                icon="material-symbols-light:bedroom-parent-outline"
                                                class="h-6 w-6 text-accent"
                                            ></Icon>
                                            <span class="ml-1">{{ spyHuntProperty.bedrooms }}</span>
                                        </div>
                                        <div class="inline-flex items-center justify-center text-base">
                                            <Icon
                                                icon="material-symbols-light:shower-outline"
                                                class="h-6 w-6 !text-accent"
                                            ></Icon>
                                            <span class="ml-1">{{ spyHuntProperty.bathrooms }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-4 text-xs">
                                    Properties within this radius: {{ spyHuntProperty.radiusMatches }} Comparables
                                </p>
                            </div>
                        </section>
                        <section class="absolute right-0 bottom-0 z-[9999] hidden p-2 md:block lg:block">
                            <div class="flex flex-col rounded-[12px] bg-surface p-4 shadow-neu-in">
                                <div class="flex gap-x-4">
                                    <div class="flex items-center justify-center">
                                        <Icon icon="pajamas:status-active" class="h-4 w-4 font-bold text-[#3B82F6]" />
                                        <span class="ml-2 text-accent">Active</span>
                                    </div>
                                    <div class="flex items-center justify-center">
                                        <Icon icon="pajamas:status-active" class="h-4 w-4 font-bold text-[#16A34A]" />
                                        <span class="ml-2 text-accent">Inactive</span>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </VMap>
                </div>
                <div class="pa-2 md:col-span-3 lg:col-span-3">
                    <h2 class="mb-2 text-2xl font-semibold tracking-tight text-accent sm:text-2xl md:text-2xl">
                        Market Overview
                    </h2>
                    <div class="flex flex-col gap-4">
                        <div
                            class="flex grid-cols-2 flex-col justify-between gap-4 rounded-[12px] bg-surface p-2 sm:gap-3 md:grid md:p-4 lg:grid lg:p-4"
                        >
                            <MarketOverviewCard
                                icon="ph:currency-dollar-bold"
                                label="Avg. Price"
                                :title="avgPrice.value"
                                :percentage="avgPrice.percentDiff"
                            />
                            <MarketOverviewCard
                                icon="ph:currency-dollar-bold"
                                label="Avg. Price per Sqft"
                                :title="avgPerSqft.value"
                                :percentage="avgPerSqft.percentDiff"
                            />
                            <MarketOverviewCard
                                icon="ph:chart-bar"
                                label="Avg. Days on market"
                                :title="dayOnMarket.value"
                                :percentage="dayOnMarket.percentDiff"
                            />
                            <MarketOverviewCard
                                icon="ph:chart-line"
                                label="Trend 30D"
                                :title="trend30d.value"
                                :percentage="trend30d.percentDiff"
                            />
                            <MarketOverviewCard
                                icon="ph:chart-line"
                                label="ZIP Avg Days on Market"
                                class="md:col-span-2 lg:col-span-2"
                                :title="
                                    spyhunt.stats.zipDom
                                        ? spyhunt.stats.zipDom + ' D'
                                        : 'No information in the last 30 days'
                                "
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
