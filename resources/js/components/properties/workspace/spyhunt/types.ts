export type SpyHuntComparableStatus = 'sold' | 'active' | 'rental';

export interface SpyHuntComparable {
    id: string;
    address: string;
    price: number;
    rentPerMonth?: number | null;
    beds: number | null;
    baths: number | null;
    squareFootage: number | null;
    status: SpyHuntComparableStatus;
    propertyType: 'House' | 'Condo' | 'Multi-family' | 'Townhome';
    distanceMiles: number;
    lastEvent: string;
    daysOnMarket: number;
    thumbnail: string;
    position: {
        x: number;
        y: number;
    };
    tag?: string;
}

export interface SpyHuntFilters {
    radius: number;
    priceRange: [number, number];
    propertyType: 'Any' | 'House' | 'Condo' | 'Multi-family' | 'Townhome';
    mode: 'sale' | 'rent';
}

export type SpyHuntState = 'loading' | 'ready' | 'empty' | 'error';
export type SpyHuntStatus =
    | 'ready'
    | 'processing'
    | 'in-progress'
    | 'needs-action'
    | 'loading'
    | 'error'
    | 'empty'
    | string
export interface SpyHuntHeaderProps {
    title: string
    subtitle: string
    status: SpyHuntStatus
    isRefreshing: boolean
    isAddToReportBusy: boolean
    reportAdded: boolean
}