export type WorkspaceStatus =
    | 'in-progress'
    | 'ready'
    | 'processing'
    | 'draft'
    | 'needs-action';

export interface WorkspaceAction {
    id: string;
    label: string;
    module: string;
}

export interface WorkspaceModuleMeta {
    endpoint: string;
    status: WorkspaceStatus | string;
    last_run_at: string | null;
}

export type WorthStatusState =
    | 'idle'
    | 'loading'
    | 'success'
    | 'error'
    | 'cached';

export interface WorthComparable {
    id: string;
    address: string;
    sale_price: number | null;
    sale_date: string | null;
    distance_miles: number | null;
    delta?: string | null;
}

export interface WorthTrendPoint {
    label: string;
    value: number;
}

export interface WorthResult {
    id: number;
    value: number | null;
    value_low?: number | null;
    value_high?: number | null;
    confidence: number | null;
    comparables: WorthComparable[];
    trend: WorthTrendPoint[];
    provider: string | null;
    fetched_at: string | null;
    cached_at?: string | null;
    rental_value?: number | null;
}

export interface PropertyWorkspaceMeta {
    actions?: WorkspaceAction[];
    modules?: Record<string, WorkspaceModuleMeta>;
}

export interface PropertySummary {
    bedrooms?: number;
    bathrooms?: number;
    livingArea?: number;
    lotSize?: number;
    yearBuilt?: number;
    propertyType?: string;
}

export interface PropertyPricing {
    acquisition?: number;
    currentEstimate?: number;
    potentialAfterGlow?: number;
}

export interface PropertyOwner {
    name?: string;
    email?: string;
}

export interface PropertyAddress {
    line1?: string;
    city?: string;
    state?: string;
    postal_code?: string;
}

export interface PropertyWorkspaceProperty {
    id: number | string;
    title?: string;
    status?: WorkspaceStatus | string;
    address?: PropertyAddress;
    owner?: PropertyOwner;
    summary?: PropertySummary;
    pricing?: PropertyPricing;
    last_updated?: string;
    last_updated_human?: string;
    tags?: string[];
    workspace?: PropertyWorkspaceMeta;
    worth?: WorthResult | null;
    glowUp?: GlowUpState;
    usage?: {
        plan: {
            tier: string;
            label: string;
            limit: number | null;
        };
        used: number;
        remaining: number;
        limit: number | null;
        period_key: string;
        resets_at?: string | null;
    } | null;
}

export type ModuleId =
    | 'overview'
    | 'pixrWorth'
    | 'pixrGlowUp'
    | 'pixrSpyHunt'
    | 'pixrVision'
    | 'pixrSeal'
    | 'pixrCollab';

export type GlowUpJobStatus = 'pending' | 'processing' | 'done' | 'error';

export interface GlowUpJob {
    id: number;
    property_id: number;
    room_type: string;
    style: string;
    before_url: string;
    after_url: string | null;
    status: GlowUpJobStatus | string;
    error_message?: string | null;
    progress: number;
    is_terminal: boolean;
    created_at?: string | null;
    updated_at?: string | null;
    usage_recorded_at?: string | null;
}

export interface GlowUpUsage {
    used: number;
    limit: number | null;
    reset_at?: string | null;
}

export interface GlowUpOptionItem {
    value: string;
    label: string;
}

export interface GlowUpState {
    jobs: GlowUpJob[];
    usage?: GlowUpUsage | null;
    options?: {
        room_types?: GlowUpOptionItem[];
        styles?: GlowUpOptionItem[];
    };
    limits?: {
        max_upload_size_mb?: number | null;
    };
}
