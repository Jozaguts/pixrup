<script setup lang="ts">
import { computed } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { Loader2, RefreshCw, TrendingUp, AlertTriangle } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import propertiesRoutes from '@/routes/properties'
import { fetch as fetchWorth } from '@/actions/Interface/Appraisal/Controllers/PropertyWorthController'
import { Button } from '@/components/ui/button'
import type { BreadcrumbItem } from '@/types'

/**
 * Props supplied by the backend PixrWorth controller.
 */
interface Props {
    property: {
        id: number
        title?: string | null
        address?: string | null
        city?: string | null
        state?: string | null
        postal_code?: string | null
        country?: string | null
    }
    worth?: WorthPayload | null
}

/**
 * Representation of comparable sale entries returned by PixrWorth.
 */
interface WorthComparable {
    address?: string | null
    sale_price?: number | null
    distance_miles?: number | null
}

/**
 * Normalized valuation payload returned from the backend.
 */
interface WorthPayload {
    value: number
    value_low: number
    value_high: number
    confidence: number
    comparables: WorthComparable[]
    fetched_at: string
    cached_at?: string | null
    provider: string
}

const props = defineProps<Props>()

type ViewState = 'idle' | 'loading' | 'success' | 'cached' | 'error'

const page = usePage<{ errors?: Record<string, string> | undefined }>()

const currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
})

const worth = computed<WorthPayload | null>(() => props.worth ?? null)
const property = computed<Props['property']>(() => props.property)
const worthErrors = computed<Record<string, string>>(() => page.props.errors ?? {})
const errorMessage = computed(() => worthErrors.value?.worth ?? '')
const propertyId = computed(() => property.value?.id ?? null)

const breadcrumbs = computed<BreadcrumbItem[]>(() => {
    const id = propertyId.value
    const title =
        property.value?.title ??
        property.value?.address ??
        (id ? `Property #${id}` : 'Property')

    const propertyHref = id ? propertiesRoutes.show.url({ property: id }) : '/properties'

    return [
        {
            title,
            href: propertyHref,
        },
        {
            title: 'PixrWorth appraisal',
            href: id ? propertiesRoutes.worth.fetch.url({ property: id }) : propertyHref,
        },
    ]
})

const form = useForm({
    property_id: propertyId.value,
})

/**
 * Description: Trigger valuation fetch for the current property via Wayfinder action.
 * Parameters: None.
 * Returns: void
 * Expected Result: Initiates Inertia POST request and updates page props with latest worth data.
 */
const handleFetch = (): void => {
    if (propertyId.value === null || form.processing) {
        return
    }

    const route = fetchWorth({ property: propertyId.value })

    form.submit(route.method, route.url, {
        preserveState: true,
        preserveScroll: true,
    })
}

/**
 * Description: Format numeric value as US currency for UI display.
 * Parameters: number | null value Numeric value to format.
 * Returns: string
 * Expected Result: Returns formatted currency or em dash when null.
 */
const formatCurrency = (value: number | null | undefined): string => {
    if (value === null || value === undefined || Number.isNaN(Number(value))) {
        return '—'
    }

    return currencyFormatter.format(value)
}

/**
 * Description: Format ISO timestamp for presentation.
 * Parameters: string | null timestamp ISO timestamp string.
 * Returns: string | null
 * Expected Result: Provides locale-aware date label or null when unavailable.
 */
const formatTimestamp = (timestamp?: string | null): string | null => {
    if (!timestamp) {
        return null
    }

    const date = new Date(timestamp)

    if (Number.isNaN(date.getTime())) {
        return null
    }

    return date.toLocaleString()
}

const fetchedAtLabel = computed(() => formatTimestamp(worth.value?.fetched_at))
const cachedAtLabel = computed(() => formatTimestamp(worth.value?.cached_at ?? null))

const comparables = computed<WorthComparable[]>(() => worth.value?.comparables ?? [])
const hasComparables = computed(() => comparables.value.length > 0)

const isCached = computed(() => worth.value !== null && !!worth.value.cached_at)
const hasWorth = computed(() => worth.value !== null)

/**
 * Description: Determine UI state for the valuation view.
 * Parameters: None.
 * Returns: ViewState
 * Expected Result: Guides conditional rendering for idle/loading/success/error states.
 */
const viewState = computed<ViewState>(() => {
    if (form.processing) {
        return 'loading'
    }

    if (!hasWorth.value && errorMessage.value) {
        return 'error'
    }

    if (hasWorth.value && isCached.value) {
        return 'cached'
    }

    if (hasWorth.value) {
        return 'success'
    }

    return 'idle'
})

const confidencePercent = computed(() => {
    if (!worth.value) {
        return null
    }

    return Math.round(worth.value.confidence * 100)
})

const pageTitle = computed(() => property.value?.title ?? 'PixrWorth appraisal')

const composedAddress = computed(() => {
    if (!property.value) {
        return null
    }

    const segments = [property.value.address, property.value.city, property.value.state]
        .filter((segment) => segment && segment.length > 0)
        .join(', ')

    if (property.value.postal_code) {
        return [segments, property.value.postal_code].filter(Boolean).join(' ')
    }

    return segments || null
})

const showCachedBanner = computed(() => viewState.value === 'cached' && cachedAtLabel.value && fetchedAtLabel.value)
const showSuccess = computed(() => viewState.value === 'success' || viewState.value === 'cached')
const isFetchDisabled = computed(() => form.processing || propertyId.value === null)
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="PixrWorth appraisal" />

        <div class="mx-auto flex max-w-4xl flex-col gap-6 py-8">
            <section class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-semibold text-foreground">
                        {{ pageTitle }}
                    </h1>
                    <p v-if="composedAddress" class="text-sm text-muted-foreground">
                        {{ composedAddress }}
                    </p>
                    <p class="text-xs uppercase tracking-wide text-muted-foreground">
                        Provider: {{ worth?.provider ?? 'mock' }}
                    </p>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-4">
                    <Button
                        type="button"
                        :disabled="isFetchDisabled"
                        @click="handleFetch"
                        class="inline-flex items-center gap-2"
                    >
                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <RefreshCw v-else class="h-4 w-4" />
                        <span>Fetch valuation</span>
                    </Button>
                    <span v-if="fetchedAtLabel" class="text-sm text-muted-foreground">
                        Last fetched: {{ fetchedAtLabel }}
                    </span>
                </div>
            </section>

            <section
                v-if="viewState === 'loading'"
                class="rounded-2xl border border-border bg-card p-6 shadow-sm"
            >
                <div class="animate-pulse space-y-4">
                    <div class="h-6 w-1/3 rounded bg-muted"></div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="h-24 rounded-xl bg-muted"></div>
                        <div class="h-24 rounded-xl bg-muted"></div>
                        <div class="h-24 rounded-xl bg-muted"></div>
                    </div>
                    <div class="h-40 rounded-xl bg-muted"></div>
                </div>
            </section>

            <section
                v-else-if="viewState === 'idle'"
                class="rounded-2xl border border-dashed border-border bg-muted/20 p-6 text-center"
            >
                <TrendingUp class="mx-auto mb-4 h-10 w-10 text-muted-foreground" />
                <h2 class="text-lg font-semibold text-foreground">
                    No valuation yet
                </h2>
                <p class="mt-2 text-sm text-muted-foreground">
                    Kick off your first PixrWorth appraisal to unlock comps and confidence scores.
                </p>
            </section>

            <section
                v-else-if="viewState === 'error'"
                class="rounded-2xl border border-destructive bg-destructive/10 p-6"
            >
                <div class="flex flex-col items-center gap-3 text-center">
                    <AlertTriangle class="h-8 w-8 text-destructive" />
                    <p class="text-sm text-destructive">
                        {{ errorMessage || 'Unable to fetch valuation. Please try again later.' }}
                    </p>
                    <Button type="button" variant="outline" @click="handleFetch">
                        Retry fetch
                    </Button>
                </div>
            </section>

            <section
                v-else-if="showSuccess"
                class="space-y-4"
            >
                <div
                    v-if="showCachedBanner"
                    class="rounded-xl border border-border bg-muted/30 p-4 text-sm text-muted-foreground"
                >
                    Cached valuation served from {{ cachedAtLabel }} — market data last refreshed
                    {{ fetchedAtLabel }}.
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border border-border bg-card p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-muted-foreground">
                            Estimated value
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-foreground">
                            {{ formatCurrency(worth?.value ?? null) }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-border bg-card p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-muted-foreground">
                            Low range
                        </p>
                        <p class="mt-2 text-xl font-semibold text-foreground">
                            {{ formatCurrency(worth?.value_low ?? null) }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-border bg-card p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-muted-foreground">
                            High range
                        </p>
                        <p class="mt-2 text-xl font-semibold text-foreground">
                            {{ formatCurrency(worth?.value_high ?? null) }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-border bg-card p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-muted-foreground">
                            Confidence
                        </p>
                        <p class="mt-2 text-xl font-semibold text-foreground">
                            {{ confidencePercent !== null ? confidencePercent + '%' : '—' }}
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-foreground">
                        Comparable sales
                    </h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Nearby transactions calibrating this valuation snapshot.
                    </p>

                    <div v-if="hasComparables" class="mt-4 divide-y divide-border">
                        <div
                            v-for="(comparable, index) in comparables"
                            :key="index"
                            class="grid grid-cols-1 gap-3 py-3 sm:grid-cols-3"
                        >
                            <div>
                                <p class="text-sm font-medium text-foreground">
                                    {{ comparable.address ?? 'Unknown address' }}
                                </p>
                            </div>
                            <div class="text-sm text-muted-foreground">
                                {{ formatCurrency(comparable.sale_price ?? null) }}
                            </div>
                            <div class="text-sm text-muted-foreground">
                                {{ comparable.distance_miles ? comparable.distance_miles.toFixed(2) + ' mi' : 'n/a' }}
                            </div>
                        </div>
                    </div>

                    <p v-else class="mt-4 text-sm text-muted-foreground">
                        No comparable sales returned by the provider.
                    </p>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
