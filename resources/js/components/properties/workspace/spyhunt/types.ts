export type SpyHuntComparableStatus = 'Active' | 'Inactive';

export interface SpyHuntComparable {
    id: string;
    rentPerMonth?: number | null;
    status: SpyHuntComparableStatus;
    propertyType: PropertyType
    thumbnail: string;
    tag?: string;
    price: number;
    squareFootage: number;
    bedrooms: number;
    bathrooms: number;
    yearBuild: number;
    daysOnMarket: number;
    distance: number,
    address: string,
    lastSeenDate: Date,
    latitude: number,
    longitude: number,
}

export interface SpyHuntFilters {
    radius: number;
    priceRange: [number, number];
    propertyType: PropertyType
    mode: 'sale' | 'rent';
}
export type PropertyType = 'Single Family' | 'Condo' | 'Multi-family' | 'Townhouse' | 'Manufactured' | 'Apartment' | 'Land';
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
    isRefreshing: boolean
    isAddToReportBusy: boolean
    reportAdded: boolean
}