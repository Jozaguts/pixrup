export type SpyHuntComparableStatus = 'sold' | 'active' | 'rental';

export interface SpyHuntComparable {
    id: string;
    address: string;
    price: number;
    rentPerMonth?: number | null;
    beds: number | null;
    baths: number | null;
    sqft: number | null;
    status: SpyHuntComparableStatus;
    propertyType: 'House' | 'Condo' | 'Multi-family' | 'Townhome';
    distanceMiles: number;
    lastEvent: string;
    dom: number;
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
