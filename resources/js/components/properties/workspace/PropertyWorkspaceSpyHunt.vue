<script setup lang="ts">
import type { SpyHuntComparable } from '@/components/properties/workspace/spyhunt/types';
import type {
    PropertyWorkspaceProperty,
    WorkspaceModuleMeta,
} from '@/components/properties/workspace/types';
import { computed, onMounted, ref } from 'vue';
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

interface Props {
    property: PropertyWorkspaceProperty;
    meta: WorkspaceModuleMeta;
    moduleId: string;
}
type SpyHunt = {
    property: {
        lat: number;
        lng: number;
    };
    filters: {
        radius: number[];
        defaults: {
            radius: number;
            mode: 'sale' | 'rent';
        };
    };
    comps: {
        summary: {
            sale_count: number;
            rent_count: number;
        };
        sale: SpyHuntComparable[];
        rent: SpyHuntComparable[];
    };
    market_snapshot: {
        avgPricePerFt: number;
        avgRentPerFt: number;
        daysOnMarket: number;
        trend30d: number;
        avgRentPrice: number;
        avgSalePrice:number;
        trend30dTotal:number;
    };
    value_estimate: {
        price: number;
        range_low: number;
        range_high: number;
    };
};
const spyhunt = ref<SpyHunt>({} as SpyHunt);
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
    loading.value = false;
    spyhunt.value = json.data as SpyHunt;
    center.value = [spyhunt.value.property.lat, spyhunt.value.property.lng];
    markers.value = spyhunt.value.comps[
        spyhunt.value.filters.defaults.mode
    ].map((comp) => {
        return { lat: comp.latitude, lng: comp.longitude };
    }) as Marker[];
}
onMounted(() => {
    loadSpyHunt();
});
const activeMarker = ref<LeafletMouseEvent | null>(null);
const markerScreenPos = ref({ x: 0, y: 0 });
const mapRef = ref<HTMLElement | null>(null);
function onMarkHover(marker: LeafletMouseEvent): void {
    activeMarker.value = marker;
    const point = marker.layerPoint;
    if (!point) {
        return;
    }
    markerScreenPos.value = { x: point.x, y: point.y };
}
/*
* COMPUTED
* */
const avgValues = computed(()=> {
    const diff = spyhunt.value.value_estimate.price  - spyhunt.value.market_snapshot.avgSalePrice
    const percentDiff  = ( (diff / spyhunt.value.market_snapshot.avgSalePrice) * 100).toFixed(1)
    return {
        avgPrice: useSpyHunt().moneyFormat(
            spyhunt.value.filters.defaults.mode == 'sale'
                ? spyhunt.value.market_snapshot.avgSalePrice
                : spyhunt.value.market_snapshot.avgRentPrice ?? 0
        ),
        percentDiff
    }
})
const isReady = computed(() => !loading.value && spyhunt.value.property?.lat);
</script>
<template>
    <div class="flex flex-col gap-6 text-[#111827]">
        <SpyHuntWorkSpaceSkeleton v-if="loading" />
        <section v-else-if="isReady" class="space-y-6">
            <div
                class="mt-10 grid min-h-[400px] grid-cols-1 gap-4 md:grid-cols-12 lg:grid-cols-12"
            >
                <div class="bg-gray-200 rounded-[12px] shadow-neu-in md:col-span-9 lg:col-span-9">
                    <VMap
                        ref="mapRef"
                        :center="center"
                        :zoom="zoom"
                        @view-changed="onViewChanged"
                        class="h-full rounded-[12px]"
                    >
                        <VMapGoogleTileLayer />
                        <VMapZoomControl />
                        <VMapAttributionControl />
                        <VMapMarker
                            :latlng="[
                                spyhunt.property?.lat,
                                spyhunt?.property?.lng,
                            ]"
                            @mouseover="onMarkHover"
                            @mouseout="activeMarker = null"
                        >
                            <VMapPinIcon
                                color="#6e33ff"
                                root-class="npo-form-shadow"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"
                                >
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
                                <p class="font-semibold text-gray-800">
                                    activeMarker
                                </p>
                                <p class="text-sm text-gray-600">Price:</p>
                            </div>
                        </transition>
                    </VMap>
                </div>

                <MarketOverviewCard
                    icon="ph:currency-dollar-bold"
                    label="Avg. Price"
                    :title="avgValues.avgPrice"
                    :percentage="avgValues.percentDiff"
                />
            </div>
        </section>
    </div>
</template>
