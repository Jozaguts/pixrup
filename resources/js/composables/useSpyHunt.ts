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
    const mode = computed(()=> spyhunt.value.filters.defaults.mode)
    const avgPrice = computed(() =>{
        const diff = spyhunt.value.value_estimate.price - spyhunt.value.market_snapshot.avgSalePrice;
        const percentDiff = ((diff / spyhunt.value.market_snapshot.avgSalePrice) * 100).toFixed(1);
        return {
            value: moneyFormat(
                spyhunt.value.filters.defaults.mode == 'sale'
                    ? spyhunt.value.market_snapshot.avgSalePrice
                    : (spyhunt.value.market_snapshot.avgRentPrice ?? 0),
            ),
            percentDiff,
        }
    })
    const avgPerSqft = computed(() =>{
        const mode = spyhunt.value.filters.defaults.mode
        const propertyPricePerFt = spyhunt.value.value_estimate.price / spyhunt.value.property.square_footage
        const marketAvg = mode === 'sale'
            ? spyhunt.value.market_snapshot.avgPricePerFt
            : spyhunt.value.market_snapshot.avgRentPerFt
        const diff = propertyPricePerFt - marketAvg
        const percentDiff = ((diff / marketAvg) * 100).toFixed(1)

        return {
            value: moneyFormat(
                spyhunt.value.filters.defaults.mode == 'sale'
                    ? spyhunt.value.market_snapshot.avgPricePerFt
                    : (spyhunt.value.market_snapshot.avgRentPerFt ?? 0),
            ),
            percentDiff: percentDiff
        }
    })
    const dayOnMarket = computed(() =>{
        const diff =  spyhunt.value.stats.subjectDom -  spyhunt.value.market_snapshot.daysOnMarket;
        const percentDiff = ((diff / spyhunt.value.market_snapshot.daysOnMarket) * 100).toFixed(1);
        return {
            value: spyhunt.value.market_snapshot.daysOnMarket,
            percentDiff
        }
    })
    const radius = computed(() =>{
        return spyhunt.value.filters.radius.map((r, index) => ({id: index + 1, label: r + 'ml', icon: 'ph:map-pin-simple-area-light'}))
    })
    const radiusMatches = computed(() =>{
       return spyhunt.value.comps[mode.value].filter((comp) => comp.distance <= spyhunt.value.filters.defaults.radius)?.length ?? 0

    })

    return {
        spyhunt,
        avgPrice,
        avgPerSqft,
        dayOnMarket,
        radius,
        radiusMatches,
        mode,
        formatAddress,
        moneyFormat
    }
}