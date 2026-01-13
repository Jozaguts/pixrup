<template>
    <AppLayout :breadcrumbs="[{title:'Reports', href: '/reports'}]">
        <DashboardSection
            title="Pixrup Properties"
            description="Manage your properties here."
            title-class="!text-3xl font-semibold"
            description-class="text-md font-light text-accent/60 mb-6"
            class="w-full h-full px-0"
        >
            <div class="flex flex-row items-center justify-between mb-6 gap-5">
                <div class="flex-1">
                    <NeuInput
                        wrapper-class="mb-3 md:mb-0 w-full"
                        icon="ph:building-light"
                        placeholder="Search properties.."
                        v-model="searchString"
                        @keydown.esc="searchString = ''"
                    />
                </div>
                <a title="Create a new property" href="properties/new" class="shadow-neu-out px-4 py-2 rounded-[12px] group mr-4">
                    <Icon icon="ph:stack-plus-light" class="w-6 h-6"/>
                </a>
            </div>
            <div class="overflow-hidden rounded-[12px]">
                <data-table
                    table-class-name="soft-table"
                    :headers="headers"
                    :items="properties"
                    hide-footer
                    search-field="title"
                    :search-value="searchString"
                >
                    <template #header="header">
                        <span class="text-accent/30 font-semibold !text-xs">
                           {{ header.text.toUpperCase() }}
                        </span>
                    </template>
                    <template #item-title="{thumbnail, city, title}">
                        <div class="flex items-center gap-4 cursor-pointer">

                            <!-- Thumbnail -->
                            <div class="npo-form-shadow min-w-16 min-h-16 w-16 h-16 rounded-[8px] overflow-hidden flex-shrink-0 bg-gray-100">
                                <img
                                    :src="thumbnail"
                                    class="h-full w-full object-cover"
                                    alt="Property Thumbnail"
                                    @error="(event) => {
                                        const img = event.target as HTMLImageElement;
                                        img.src = 'https://placehold.co/200x200?text=No+Image';
                                    }"
                                />
                            </div>

                            <!-- Text content -->
                            <div class="flex flex-col overflow-hidden flex-1">
                                <span
                                    class="font-semibold md:text-[15px] text-[13px] truncate block leading-tight"
                                >
                                    {{ title }}
                                </span>
                                                        <span class="text-sm text-accent/70 leading-tight truncate">
                                    {{ city }}
                                </span>
                            </div>
                        </div>

                    </template>
                    <template #item-status="{status}">
                        <p class="text-xs !lowercase">
                            {{status.split('-').join(' ')}}
                        </p>
                    </template>

                    <template #item-actions="{ links }">
                        <div class="flex flex-row items-center justify-start">
                            <a title="Explore property" :href="links.view" class="shadow-neu-out px-4 py-2 rounded-[12px] group mr-4">
                                <Icon icon="ph:link-simple-horizontal-light" class="inline w-4 h-4 group-hover:text-accent/80 text-accent/50 transition all ease-in-out"/>
                            </a>
                            <a title="Explore property" :href="links.report" class="shadow-neu-out px-4 py-2 rounded-[12px] group mr-4">
                                <Icon icon="ph:megaphone-thin" class="inline w-4 h-4 group-hover:text-accent/80 text-accent/50 transition all ease-in-out"/>
                            </a>
                        </div>
                    </template>
                </data-table>
            </div>

        </DashboardSection>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {ref, computed} from 'vue';
import { usePage } from '@inertiajs/vue3';
import DashboardSection from "@/components/DashboardSection.vue";
import { Icon } from '@iconify/vue';
import NeuInput from "@/components/NeuInput.vue";
const page = usePage();
const properties = computed(() => page.props.properties || []);

const headers = [
    {
        text: "#",
        value: "id",
        fixed: true,
        width: 25
    },
    {
        text: "Property Title",
        value: "title",
        align: "center",
        width: 300,
    },
    {
        text: "Property status",
        value: "status",
        align: "right",
    },
    {
        text: 'Estimated Value',
        value: "value",
    },
    {
        text: "Actions",
        value: "actions",
        align: "right",
        width: 150,
    }
];

const searchString = ref('');
</script>
