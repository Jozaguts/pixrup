<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    beds?: number | null;
    baths?: number | null;
    sqft?: number | null;
    yearBuilt?: number | null;
}

const props = withDefaults(defineProps<Props>(), {
    beds: null,
    baths: null,
    sqft: null,
    yearBuilt: null,
});

const details = computed(() => [
    {
        id: 'beds',
        label: 'Beds',
        value:
            props.beds !== null && props.beds !== undefined
                ? props.beds
                : '—',
    },
    {
        id: 'baths',
        label: 'Baths',
        value:
            props.baths !== null && props.baths !== undefined
                ? props.baths
                : '—',
    },
    {
        id: 'sqft',
        label: 'Sq Ft',
        value:
            props.sqft !== null && props.sqft !== undefined
                ? Intl.NumberFormat('en-US', {
                      maximumFractionDigits: 0,
                  }).format(props.sqft)
                : '—',
    },
    {
        id: 'yearBuilt',
        label: 'Year built',
        value:
            props.yearBuilt !== null && props.yearBuilt !== undefined
                ? props.yearBuilt
                : '—',
    },
]);
</script>

<template>
    <section
        class="neu-surface shadow-neu-out flex flex-col gap-4 rounded-[26px] p-6 transition-all duration-200 ease-in-out"
    >
        <header>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#6b7280]">
                Property details
            </p>
            <h3 class="mt-1 text-lg font-semibold text-[#0d0d12]">
                Subject snapshot
            </h3>
        </header>

        <dl class="grid gap-3 sm:grid-cols-2">
            <div
                v-for="detail in details"
                :key="detail.id"
                class="rounded-[18px] bg-[#f4f5fa] px-4 py-3 text-sm text-[#6b7280] shadow-[inset_8px_8px_18px_rgba(210,212,226,0.55),inset_-8px_-8px_18px_rgba(255,255,255,0.95)]"
            >
                <dt class="text-xs uppercase tracking-[0.28em] text-[#9ca3af]">
                    {{ detail.label }}
                </dt>
                <dd class="mt-1 text-base font-semibold text-[#0d0d12]">
                    {{ detail.value }}
                </dd>
            </div>
        </dl>
    </section>
</template>
