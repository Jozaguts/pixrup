<script setup lang="ts">
import type { SpyHuntComparable } from '@/components/properties/workspace/spyhunt/types';
import type { PropertyWorkspaceProperty, SpyHunt, WorkspaceModuleMeta } from '@/components/properties/workspace/types';
import { computed, onMounted, ref, toRefs } from 'vue';
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
const { spyhunt, avgPrice, avgPerSqft, dayOnMarket, radius, radiusMatches, mode } = toRefs(useSpyHunt());

interface Props {
    property: PropertyWorkspaceProperty;
    meta: WorkspaceModuleMeta;
    moduleId: string;
}

const props = defineProps<Props>();
const loading = ref(false);
const center = ref<LatLngTuple | LatLng>([0, 0]);

const zoom = ref(14);
const bounds = ref<LatLngBounds | null>(null);

function onViewChanged(e: ViewChangedEvent) {
    center.value = e.center;
    zoom.value = e.zoom;
    bounds.value = e.bounds;
}
type Marker = { lag: number; lat: number; lng: number };
const markers = ref<Marker[]>([]);
async function loadSpyHunt() {
    loading.value = true;
    const res = await fetch(spyHuntRoutes.fetch.get(props.property.id).url, {
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
    });
    const json = await res.json();
    spyhunt.value = json.data as SpyHunt;
    center.value = [spyhunt.value.property.lat, spyhunt.value.property.lng];
    markers.value = spyhunt.value.comps[mode.value].map((comp) => {
        return { lat: comp.latitude, lng: comp.longitude };
    }) as Marker[];
    loading.value = false;
}

const activeMarker = ref<LeafletMouseEvent | null>(null);
const markerScreenPos = ref({ x: 0, y: 0 });
function onMarkHover(marker: LeafletMouseEvent): void {
    activeMarker.value = marker;
    const point = marker.containerPoint;
    if (!point) {
        return;
    }
    markerScreenPos.value = { x: point.x, y: point.y };
}
/*
 * COMPUTED
 * */

const isReady = computed(() => !loading.value && !!spyhunt.value.property?.lat);
onMounted(() => {
    loadSpyHunt();
});
console.log(markers.value);
</script>
<template>
    <div class="flex flex-col gap-6 text-[#111827]">

        <SpyHuntWorkSpaceSkeleton v-if="loading" />
        <section v-else-if="isReady" class="space-y-6">
            <div class="mt-10 grid min-h-[400px] grid-cols-1 gap-4 md:grid-cols-12 lg:grid-cols-12">
                <div class="max-h-[600px] rounded-[12px] bg-gray-200 shadow-neu-in md:col-span-9 lg:col-span-9">
                    <VMap
                        :max-zoom="15"
                        :popupopen="popup"
                        :center="center"
                        :zoom="zoom"
                        @view-changed="onViewChanged"
                        class="h-full rounded-[12px]"
                    >
                        <VMapGoogleTileLayer />
                        <VMapZoomControl />
                        <VMapAttributionControl />
                        <VMapMarker
                            :latlng="[spyhunt.property?.lat, spyhunt?.property?.lng]"
                            @mouseover="onMarkHover"
                            @mouseout="activeMarker = null"
                        >
                            <VMapPinIcon color="#6e33ff" root-class="npo-form-shadow">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path
                                        d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z"
                                    />
                                </svg>
                            </VMapPinIcon>
                        </VMapMarker>
                        <VMapMarker
                            v-for="(marker, idx) in markers"
                            :key="idx"
                            :latlng="[marker.lat, marker.lng]"
                            @mouseover="onMarkHover"
                            @mouseout="activeMarker = null"
                        />
                        <transition name="fade">
                            <div
                                v-if="activeMarker"
                                class="absolute z-[500] w-56 rounded-lg border border-gray-200 bg-white p-3 shadow-lg"
                                :style="{
                                    top: markerScreenPos.y + 'px',
                                    left: markerScreenPos.x + 150 + 'px',
                                    transform: 'translate(-50%, -100%)',
                                }"
                            >
                                <p class="font-semibold text-gray-800">activeMarker</p>
                                <p class="text-sm text-gray-600">Price:</p>
                            </div>
                        </transition>
                        <section class="absolute top-0 left-0 z-[700] p-2">
                            <NeuphormistTabs
                                :value="spyhunt.filters.defaults.radius"
                                @onchange="(v) => (spyhunt.filters.defaults.radius = v.id)"
                                :items="radius"
                            />
                        </section>
                        <section class="absolute top-[50%] left-0 z-[700] p-2">
                            <div class="npo-form-shadow flex flex-col rounded-[12px] p-5">
                                <div class="flex gap-2 items-center max-w-[300px] text-base">
                                    <Icon icon="ph:map-pin-bold"  class="h-8 w-8 font-bold text-primary  "/>
                                    <p class="truncate">  {{spyhunt.property.title}}</p>
                                </div>
                                <p>Properties within this radius: {{ radiusMatches }} Comparables</p>
                            </div>
                        </section>
                    </VMap>
                </div>
                <div class="pa-2 md:col-span-3 lg:col-span-3">
                    <h2 class="mb-2 text-2xl font-semibold tracking-tight text-[#1f2937] sm:text-2xl md:text-2xl">
                        Market Overview
                    </h2>
                    <div class="flex flex-col gap-4">
                        <MarketOverviewCard
                            icon="ph:currency-dollar-bold"
                            label="Avg. Price"
                            :title="avgPrice.value"
                            :percentage="avgPrice.percentDiff"
                        />
                        <MarketOverviewCard
                            icon="ph:currency-dollar-bold"
                            label="Avg. price per sqft"
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
                            :title="spyhunt.market_snapshot.trend30d ?? 'No information in the last 30 days '"
                        />
                        <MarketOverviewCard
                            icon="ph:chart-line"
                            label="ZIP Avg Days on Market"
                            :title="
                                spyhunt.stats.zipDom
                                    ? spyhunt.stats.zipDom + ' D'
                                    : 'No information in the last 30 days'
                            "
                        />
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
