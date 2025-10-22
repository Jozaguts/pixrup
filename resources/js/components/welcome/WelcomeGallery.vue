<script setup lang="ts">
interface ListingCard {
    id: number | string;
    title: string;
    image: string;
}

defineProps<{
    listings: ListingCard[];
}>();
</script>

<template>
    <section
        class="flex w-full max-w-5xl flex-wrap items-center justify-center gap-6 py-10 text-white"
    >
        <article
            v-for="listing in listings"
            :key="listing.id"
            class="group flex w-[150px] flex-col items-center rounded-3xl bg-white/70 p-3 text-center shadow-[0_15px_45px_rgba(92,76,255,0.25)] backdrop-blur"
        >
            <div
                class="relative aspect-[3/4] w-full overflow-hidden rounded-2xl "
            >
                <img
                    :src="listing.image"
                    :alt="listing.title"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                />
            </div>
            <span
                class="mt-3 inline-flex rounded-[12px] bg-[#6E33FF] px-4 py-1 text-xs font-semibold text-white shadow-md"
            >
                {{ listing.title }}
            </span>
        </article>
    </section>
</template>
