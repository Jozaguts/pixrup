import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | string;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    mustVerifyEmail: boolean;
    flash: {
        limitExceeded?: boolean;
        status?: string | null;
        glowupJob?: GlowUpJobPayload | null;
    };
    planUsage?: PlanUsagePayload | null;
};


export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface PlanUsagePayload {
    plan: {
        tier: string;
        label: string;
    };
    usage: {
        docs: UsageBucketPayload;
        renders: UsageBucketPayload;
    };
    can: {
        docs: boolean;
        renders: boolean;
    };
    period_key: string;
    resets_at?: string | null;
}

export interface BillingPlan {
    name: string;
    renews_at?: string | null;
    is_canceling?: boolean;
    ends_at?: string | null;
    pending_plan?: {
        name: string;
        starts_at?: string | null;
    } | null;
}

export interface BillingPlanPrice {
    id: string;
    unit_amount: number;
    currency: string;
    interval?: string | null;
    interval_count?: number | null;
}

export interface BillingPlanOption {
    id: number;
    key: string;
    name: string;
    description?: string | null;
    price: BillingPlanPrice;
    is_current: boolean;
}

export interface UsageBucketPayload {
    limit: number;
    used: number;
    remaining: number | null;
    is_unlimited: boolean;
    is_blocked: boolean;
    can_use: boolean;
    percent_used: number | null;
}

export interface GlowUpJobPayload {
    id: number;
    property_id: number;
    room_type: string;
    style: string;
    before_url: string;
    after_url: string | null;
    status: string;
    error_message?: string | null;
    progress: number;
    is_terminal: boolean;
    created_at?: string | null;
    updated_at?: string | null;
    usage_recorded_at?: string | null;
}

export type DashboardPageProps = AppPageProps<{
    properties?: DashboardProperty[];
}>;
export interface DashboardProperty {
    id: number | string;
    title: string;
    address: string;
    status: PropertyStatus;
    estimatedValue?: number;
    progress?: number;
    thumbnail?: string | null;
    links?: {
        view?: string;
        report?: string;
    };
}
interface Image {
    uri: string,
    name: string
    [key: PropertyKey]: any
}

interface VerticalStepItem {
    title: string;
    description?: string;
    icon?: string;
    completed: boolean;
    index: string | number;
    component?: any;
}
