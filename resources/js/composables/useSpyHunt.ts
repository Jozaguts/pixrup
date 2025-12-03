import type { PropertyWorkspaceProperty, SpyHunt } from '@/components/properties/workspace/types';
import { computed, ref } from 'vue';
import type { SpyHuntComparable } from '@/components/properties/workspace/spyhunt/types';

export default function useSpyHunt() {
    const spyhunt = ref<SpyHunt>({
        property: {
            lat: 0,
            lng: 0,
            square_footage: 0,
        },
        filters: {
            radius: [1,3,5],
            defaults: {
                radius: 1,
                mode: 'sale',
            },
        },
        comps: {
            summary: {
                sale_count: 0,
                rent_count: 0,
            },
            sale: [] as SpyHuntComparable,
            rent: [] as SpyHuntComparable,
        },
        market_snapshot: {
            avgPricePerFt: 0,
            avgRentPerFt:0,
            daysOnMarket: 0,
            trend30d: 0,
            avgRentPrice: 0,
            avgSalePrice: 0,
            trend30dTotal: 0,
        },
        value_estimate: {
            price: 0,
            range_low: 0,
            range_high: 0,
        }
    });
    const formatAddress = (property: PropertyWorkspaceProperty) => {
        const primary = property.address?.line1 ?? property.title ?? null;
        const locality = [property.address?.city, property.address?.state]
            .filter(Boolean)
            .join(', ');
        const sections = [primary, locality].filter(
            (section): section is string => Boolean(section),
        );
        if (sections.length) {
            return sections.join(' • ');
        }
        return property.id ? `Property #${property.id}` : 'PixrSpyHunt';
    };
    const moneyFormat = (value: number) => {
        const USDollar =  new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            compactDisplay: 'short',
            maximumFractionDigits: 0,
        });
       return USDollar.format(value)
    }
    const numberFormat = (value: number) => {
        const format =  new Intl.NumberFormat('en-US', )
        return format.format(value)
    }
    const mode = computed(()=> spyhunt.value.filters.defaults.mode)
    const avgPrice = computed(() =>{
        const diff = spyhunt.value.value_estimate.price - spyhunt.value.market_snapshot.avgSalePrice;
        const percentDiff = ((diff / spyhunt.value.market_snapshot.avgSalePrice) * 100).toFixed(1);
        return {
            value: moneyFormat(mode.value === 'sale' ? spyhunt.value.market_snapshot.avgSalePrice : spyhunt.value.market_snapshot.avgRentPrice),
            percentDiff: mode.value === 'sale' ? percentDiff + '%' : 'N/A',
        }
    })
    const avgPerSqft = computed(() =>{
        const mode = spyhunt.value.filters.defaults.mode
            const propertyPricePerFt = spyhunt.value.value_estimate.price / spyhunt.value.property.square_footage
            const marketAvg = spyhunt.value.market_snapshot.avgPricePerFt
            const diff = propertyPricePerFt - marketAvg
            const percentDiff = ((diff / marketAvg) * 100).toFixed(1) + '%'
            return{
                value: moneyFormat(mode.value === 'sale' ? spyhunt.value.market_snapshot.avgPricePerFt : spyhunt.value.market_snapshot.avgRentPerFt),
                percentDiff: mode === 'sale' ? percentDiff  : 'N/A',
            }
    })
    const dayOnMarket = computed(() =>{
        return {
            value: spyhunt.value.market_snapshot.daysOnMarket,
            percentDiff: spyhunt.value.stats.subjectDom + 'D'
        }
    })
    const radius = computed(() =>{
        return spyhunt.value.filters.radius.map((r) => ({id: r, label: r + 'mi', icon: 'ph:map-pin-simple-area-light'}))
    })
    const radiusMatches = computed(() =>{
       return spyhunt.value.comps[mode.value].filter((comp) => comp.distance <= spyhunt.value.filters.defaults.radius)?.length ?? 0
    })
    const comparables = computed(()=>{
        return spyhunt.value.comps[mode.value].filter((comp) => comp.distance <= spyhunt.value.filters.defaults.radius)
    })
    const trend30d = computed(() =>{
        const value =  spyhunt.value.market_snapshot.trend30d.toFixed(2)  + '%'
        const propertyPricePerFt = spyhunt.value.value_estimate.price / spyhunt.value.property.square_footage
        const avgPerSqft = spyhunt.value.market_snapshot.avgPricePerFt
        const diff = propertyPricePerFt - avgPerSqft

        const percentDiff =( (diff / avgPerSqft) * 100).toFixed(2) + '%'

        return {
            value,
            percentDiff
        }
    })
    const formatComparable = (comparable: SpyHuntComparable)=>{
        if (!Object.keys(comparable).length){
            return
        }
        const price = comparable?.price ?? 0
        const squareFootage = comparable?.squareFootage ?? 0
        const pricePerFt =( price / squareFootage) .toFixed(0)
        const distance = comparable.distance.toFixed(2)
        return {
            ...comparable,
            price: moneyFormat(price),
            squareFootage: numberFormat(squareFootage),
            pricePerFt: moneyFormat(pricePerFt),
            distance
        }
    }
    const spyHuntProperty  = computed(() =>{
        const propertyPrice = spyhunt.value.value_estimate.price ?? 0
        const propertySquareFootage = spyhunt.value.property.square_footage ?? 0
        const pricePerFt =( propertyPrice / propertySquareFootage).toFixed(2)
        return {
            ...spyhunt.value.property,
            square_footage: numberFormat(propertySquareFootage),
            price: moneyFormat(propertyPrice),
            pricePerFt: moneyFormat(pricePerFt),
            radiusMatches: radiusMatches.value,
            lowest_estimate_price: moneyFormat(spyhunt.value.value_estimate.range_low),
            highest_estimate_price:moneyFormat( spyhunt.value.value_estimate.range_high),
        }
    })
    return {
        spyHuntProperty,
        spyhunt,
        avgPrice,
        avgPerSqft,
        dayOnMarket,
        radius,
        radiusMatches,
        mode,
        comparables,
        trend30d,
        formatAddress,
        moneyFormat,
        formatComparable
    }
}