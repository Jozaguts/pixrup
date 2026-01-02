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
const { spyhunt, avgPrice, dayOnMarket, radius, mode, comparables, trend30d, spyHuntProperty, avgPerSqft } = toRefs(useSpyHunt());

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

    loading.value = false;
}

const activeMarker = ref<LeafletMouseEvent | null>(null);
const activeComparable = ref<SpyHuntComparable>()
const markerScreenPos = ref({ x: 0, y: 0 });
function onMarkHover(marker: LeafletMouseEvent, comparable: SpyHuntComparable): void {
    activeMarker.value = marker;
    activeComparable.value = useSpyHunt().formatComparable(comparable) as unknown as SpyHuntComparable
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
</script>
<template>
    <div class="flex flex-col gap-6 text-[#111827]">
        <SpyHuntWorkSpaceSkeleton v-if="loading" />
        <section v-else-if="isReady">
            <div class="mt-10 md:grid lg:grid gap-4 md:grid-cols-12 lg:grid-cols-12 flex flex-col">
                <div class="shadow-neu-in p-6 bg-gray-200 rounded-[12px] mt-4 h-full min-h-[600px] rounded-[12px] col-span-1 md:col-span-9 lg:col-span-9 ">
                    <div class="flex flex-col lg:hidden md:hidden npo-form-shadow  p-4 rounded-[12px] mb-4 ">
                        <div class="relative mb-6 ">
                            <label for="labels-range-input" class="text-sm font-semibold tracking-wide text-accent/50 uppercase">Radius</label>
                            <input id="labels-range-input"
                                   type="range"
                                   :min="1"
                                   :max="5"
                                   :step="1"
                                   :value="spyhunt.filters.defaults.radius"
                                   @change="(v) => (spyhunt.filters.defaults.radius = v?.target?.value)"
                                   class="w-full h-4  appearance-none cursor-pointer   overflow-hidden rounded-full !bg-[#e4e6ee] shadow-inner"
                            >
                            <span class="text-sm text-body absolute start-0 -bottom-6">1</span>
                            <span class="text-sm text-body absolute start-1/2 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">3</span>
                            <span class="text-sm text-body absolute end-0 -bottom-6">5</span>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm font-semibold tracking-wide text-accent/50 uppercase">  Property type</span>
                            <NeuphormistTabs
                                :value="mode"
                                @onchange="(v) => (spyhunt.filters.defaults.mode = v.id)"
                                :items="[{id: 'sale', label: 'Sale'}, {id: 'rent', label: 'Rent'}]"
                                parentClasses="flex w-fit    m-0 "
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
                        class="min-h-[600px]  rounded-[12px]"
                    >
                        <VMapGoogleTileLayer />
                        <VMapZoomControl />
                        <VMapAttributionControl />
                        <VMapMarker
                            :latlng="[spyhunt.property?.lat, spyhunt?.property?.lng]"
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
                            v-for="(comp, idx) in comparables"
                            :key="idx"
                            :latlng="[comp.latitude, comp.longitude]"
                            @mouseover="(e) => onMarkHover(e,comp)"
                            @mouseout="activeMarker = null"
                        >
                            <VMapPinIcon :color="comp.status === 'Active' ? '#3B82F6': '#16A34A'" root-class="npo-form-shadow">
                            </VMapPinIcon>
                        </VMapMarker>

                        <transition name="fade">
                            <div
                                v-if="activeMarker"
                                class="absolute z-[600] min-full  max-w-[340px] m rounded-lg shadow-neu-in bg-gray-200 p-4"
                                :style="{
                                    top: markerScreenPos.y + 'px',
                                    left: markerScreenPos.x + 200 + 'px',
                                    transform: 'translate(-50%, -100%)',
                                }"
                            >
                                <p class="font-semibold text-gray-800">{{activeComparable?.address}}</p>
                                 <div class="flex gap-2 text-gray-600 mt-1 mb-3">
                                     <span class="text-sm">{{activeComparable?.price}}</span> -
                                     <span class="text-sm">{{activeComparable?.squareFootage}} sqft</span> -
                                     <span class="text-sm">{{activeComparable?.pricePerFt}}/ft²</span>
                                 </div>
                                 <div class="flex gap-x-4">
                                     <div class="text-base inline-flex justify-center items-center">
                                         <Icon icon="ph:bulldozer-light" class="w-6 h-6 "></Icon>
                                         <span class=" ml-1 ">{{activeComparable?.yearBuilt}}</span>
                                     </div>
                                     <div class="text-base inline-flex justify-center items-center">
                                         <Icon icon="material-symbols-light:bedroom-parent-outline" class="w-6 h-6 "></Icon>
                                         <span class=" ml-1 ">{{activeComparable?.bedrooms}}</span>
                                     </div>
                                     <div class="text-base inline-flex justify-center items-center">
                                         <Icon icon="material-symbols-light:shower-outline" class="w-6 h-6 "></Icon>
                                         <span class=" ml-1">{{activeComparable?.bathrooms}}</span>
                                     </div>
                                     <div class="text-sm inline-flex justify-center items-center">
                                         <Icon icon="game-icons:path-distance" class="w-5 h-5 "></Icon>
                                         <span class=" ml-1">{{activeComparable?.distance}}</span>
                                     </div>
                                 </div>
                            </div>
                        </transition>
                        <section class="absolute top-0 left-0 z-[700] p-2 flex flex-col md:flex-row lg:flex-row gap-2">
                            <NeuphormistTabs
                                :value="spyhunt.filters.defaults.radius"
                                @onchange="(v) => (spyhunt.filters.defaults.radius = v.id)"
                                :items="radius"
                                parentClasses="hidden"
                            />
                            <NeuphormistTabs
                                :value="mode"
                                @onchange="(v) => (spyhunt.filters.defaults.mode = v.id)"
                                :items="[{id: 'sale', label: 'Sale'}, {id: 'rent', label: 'Rent'}]"
                                parentClasses="hidden"
                            />
                        </section>
                        <section class="absolute md:top-[50%] lg:top-[50%] bottom-0 left-0 z-[700] p-2 w-full lg:w-fit md:w-fit ">
                            <div class="npo-form-shadow flex flex-col rounded-[12px] p-4">
                                <div class="flex gap-2 items-center md:max-w-[300px] lg:max-w-[300px] max-w-[200px] lg:text-base md:text-base text-sm ">
                                    <Icon icon="ph:map-pin-bold"  class="h-8 w-8 font-bold text-primary  "/>
                                    <p class="truncate ">  {{spyHuntProperty.title}}</p>
                                </div>
                                <div>
                                    <div class="flex gap-2 text-gray-600 mt-1 mb-3">
                                        <span class="text-sm font-bold">{{spyHuntProperty.price}}</span> -
                                        <span class="text-sm">{{spyHuntProperty.square_footage}} sqft</span> -
                                        <span class="text-sm">{{spyHuntProperty.pricePerFt}}/ft²</span>
                                    </div>
                                    <div class="w-full text-base font-bold my-2">
                                      <div class="flex items-center- justify-between">
                                          <p class="text-lg">Low Estimate</p>
                                          <span>{{spyHuntProperty.lowest_estimate_price}}</span>
                                      </div>
                                        <div
                                            class="relative h-6 w-full overflow-hidden rounded-full !bg-[#e4e6ee] shadow-inner"
                                        >
                                            <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r transition-all duration-500 ease-out from-[#ccf] to-[#6e33ff] w-[70%]" />
                                        </div>
                                        <div class="flex items-center- justify-between">
                                            <p class="text-lg">High Estimate</p>
                                            <span>{{spyHuntProperty.highest_estimate_price}}</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-x-4">
                                        <div class="text-base inline-flex justify-center items-center">
                                            <Icon icon="material-symbols-light:bedroom-parent-outline" class="w-6 h-6 "></Icon>
                                                <span class=" ml-1 ">{{spyHuntProperty.bedrooms}}</span>
                                        </div>
                                        <div class="text-base inline-flex justify-center items-center">
                                            <Icon icon="material-symbols-light:shower-outline" class="w-6 h-6 "></Icon>
                                            <span class=" ml-1">{{spyHuntProperty.bathrooms}}</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs mt-4">Properties within this radius: {{ spyHuntProperty.radiusMatches }} Comparables</p>
                            </div>
                        </section>
                        <section class="absolute bottom-0 right-0 z-[9999] hidden md:block lg:block p-2 ">
                            <div class=" shadow-neu-in bg-gray-200 flex flex-col rounded-[12px] p-4 ">
                                <div class="flex gap-x-4">
                                    <div class="flex items-center justify-center">
                                        <Icon icon="pajamas:status-active"  class="h-4 w-4 font-bold text-[#3B82F6]  "/>
                                        <span class="ml-2">Active</span>
                                    </div>
                                    <div class="flex items-center justify-center">
                                        <Icon icon="pajamas:status-active"  class="h-4 w-4 font-bold text-[#16A34A]  "/>
                                        <span class="ml-2">Inactive</span>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </VMap>
                </div>
                <div class="pa-2 md:col-span-3 lg:col-span-3">
                    <h2 class="mb-2 text-2xl font-semibold tracking-tight text-[#1f2937] sm:text-2xl md:text-2xl">
                        Market Overview
                    </h2>
                    <div class="flex flex-col gap-4">
                        <div class="  bg-gray-200 justify-between rounded-[12px] p-5 md:grid lg:grid flex flex-col sm:gap-3 grid-cols-2 gap-4">
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
                                class="lg:col-span-2 md:col-span-2"
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
