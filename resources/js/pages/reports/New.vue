<template>
    <AppLayout :breadcrumbs="[{title:'Reports', href: '/reports'}]">
        <DashboardSection
            title="Pixrup Reports"
            description="Manage your reports here."
            title-class="!text-3xl font-semibold"
            description-class="text-md font-light text-black/60 mb-6"
            class="w-full h-full"
        >
            <div class="flex flex-row items-center justify-between mb-6">
                <div class="w-5xl">
                    <NeuInput
                        wrapper-class="mb-3 md:mb-0 w-full max-w-md"
                        icon="mdi:search"
                        placeholder="Search file.."
                        v-model="searchString"
                    />
                </div>
                <NeuButton to="reports/new" icon="mdi:note-add-outline" label="Create Report" shake/>
            </div>
            <div class="overflow-hidden rounded-[12px] shadow-neu-out">
                <data-table
                    table-class-name="soft-table"
                    :headers="headers"
                    :items="pdfs"
                    hide-footer
                    show-index
                    :search-field="searchField"
                    :search-value="searchString"
                >
                    <template #header="header">
                        <span class="text-black-4">
                           {{ header.text.toUpperCase() }}
                        </span>
                    </template>
                    <template #item-thumbnail="{ name }">
                        <div class="flex flex-row items-center group">
                            <span class="shadow-neu-out p-3 rounded-[12px] flex items-center justify-center w-12 h-12">
                              <Icon icon="formkit:filepdf" class="w-8 h-8 text-black/80"></Icon>
                            </span>
                            <span class="text-md font-semibold ml-2 text-black/80">
                                {{ name }}
                            </span>
                        </div>
                    </template>

                    <template #item-actions>
                       <NeuButton icon="mdi:download" label="Download Report" :shake="true" />
                    </template>
                </data-table>
            </div>

        </DashboardSection>
    </AppLayout>
</template>

<script setup lang="ts">
 import AppLayout from '@/layouts/AppLayout.vue';
 import { ref, reactive } from 'vue';
 import NeuButton from "@/components/neuphormic-button.vue";
 import DashboardSection from "@/components/DashboardSection.vue";
 import { Icon } from '@iconify/vue';
 import NeuInput from "@/components/NeuInput.vue";
 const pdfs = ref([
     {
         name: "Invoice-January-2025.pdf",
         thumbnail: "https://placehold.co/200x260?text=PDF",
         size: "148 KB",
         date: "Aug 24, 2024",
     },
     {
         name: "Contract-Draft v2.pdf",
         thumbnail: "https://placehold.co/200x260?text=PDF",
         size: "322 KB",
         date: "Aug 25, 2024",
     },
     {
         name: "Meeting-Notes.pdf",
         thumbnail: "https://placehold.co/200x260?text=PDF",
         size: "89 KB",
         date: "Aug 26, 2024",
     },
 ]);

 const headers = [
     {
         text: "File Name",
         value: "thumbnail",
         width: '60%',
     },
     {
         text: "Size",
         value: "size",
     },
     {
         text: "Created At",
         value: "date",
     },

     {
         text: "Last Download",
         value: "date",
         align: "left",
     },
     {
            text: "",
            value: "actions",
            align: "right",
            width: 200
     }
 ];

 const searchString = ref('');
 const searchField = ref('name');
</script>

<style>
.soft-table {
    --easy-table-header-background-color: transparent;  /* light bg */
    --easy-table-header-font-color: #374151;/* dark gray */
    --easy-table-body-row-background-color: white;
    --easy-table-row-border: 1px solid #e5e7eb;
    --easy-table-body-row-height: 72px; /* tall row to fit pdf thumbnail */
    --easy-table-header-item-padding: 1rem;
    --easy-table-border: none;
}

.soft-table td, .pdf-table th {
    padding: 0.5rem 1rem;
}
</style>
