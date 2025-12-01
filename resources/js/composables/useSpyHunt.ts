import type { PropertyWorkspaceProperty, SpyHunt } from '@/components/properties/workspace/types';
import { computed, ref } from 'vue';

export default function useSpyHunt() {
    const spyhunt = ref<SpyHunt>({} as SpyHunt);
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

    return {
        spyhunt,
        avgPrice,
        avgPerSqft,
        dayOnMarket,
        formatAddress,
        moneyFormat
    }
}