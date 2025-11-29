<script setup lang="ts">
import type {
    SpyHuntComparable,
} from '@/components/properties/workspace/spyhunt/types';
import type {
    PropertyWorkspaceProperty,
    WorkspaceModuleMeta,
} from '@/components/properties/workspace/types';
import { onMounted, ref } from 'vue';
import SpyHuntWorkSpaceSkeleton from '@/components/skeleton/SpyHuntWorkSpaceSkeleton.vue';
import useSpyHunt from '@/composables/useSpyHunt';
import spyHuntRoutes from '@/routes/properties/spyhunt';

interface Props {
    property: PropertyWorkspaceProperty;
    meta: WorkspaceModuleMeta;
    moduleId: string;
}
type SpyHunt = {
    filters:{
        radius: number[],
        defaults:{
            radius: number
        }
    },
    comps:{
        summary:{
            sale_count: number,
            rent_count: number,
        },
        sale: SpyHuntComparable[]
        rent: SpyHuntComparable[]
    }
}
const spyhunt = ref<SpyHunt>({} as SpyHunt);
const props = defineProps<Props>();
const loading = ref(true)
const { formatAddress } = useSpyHunt();
async function loadSpyHunt() {
    loading.value = true
    const res = await fetch(spyHuntRoutes.fetch.get(props.property.id).url,{
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
    const json = await res.json();
    loading.value = false
    spyhunt.value = json.data;
}
onMounted(() => {
    loadSpyHunt()
});
</script>

<template>
    <div class="flex flex-col gap-6 text-[#111827]" >
        <SpyHuntWorkSpaceSkeleton v-if="loading" />
        <section v-else-if="!loading" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-10">
                <div class="space-y-4 lg:col-span-7">

                </div>
            </div>
        </section>
    </div>
</template>
