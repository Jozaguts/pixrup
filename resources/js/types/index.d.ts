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
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    mustVerifyEmail: boolean;
    flash: {
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
        limit: number | null;
    };
    used: number;
    remaining: number;
    limit: number | null;
    period_key: string;
    resets_at?: string | null;
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
