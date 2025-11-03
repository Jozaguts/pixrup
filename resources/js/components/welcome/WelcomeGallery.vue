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
        class="flex w-full flex-wrap items-center justify-center gap-6 py-2 text-white mt-10"
    >
        <article
            v-for="listing in listings"
            :key="listing.id"
            class="min-w-[150px] max-w-[170px] neu-surface shadow-neu-in p-4  group flex sm:w-[200px]  flex-col items-center  text-center "
        >
            <div
                class="relative aspect-[3/4] w-full overflow-hidden  "
            >
                <img
                    :src="listing.image"
                    :alt="listing.title"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                />
            </div>
            <span
                class="neu-btn mt-3 inline-flex  px-4 py-1 text-xs font-semibold whitespace-nowrap text-[3vw] sm:text-[2vw] md:text-[1.5vw] lg:text-[1vw]  "
            >
                {{ listing.title }}
            </span>
        </article>
    </section>
</template>
