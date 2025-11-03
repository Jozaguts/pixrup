import { computed, reactive, readonly } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { AppPageProps } from '@/types';

interface PlanUsagePayload {
    total?: number | null;
    limit?: number | null;
    used?: number | null;
    consumption?: number | null;
    remaining?: number | null;
    last_reset_at?: string | null;
    last_refresh_at?: string | null;
}

export const usePlanUsage = () => {
    const page = usePage<AppPageProps<{ planUsage?: PlanUsagePayload }>>();

    const payload = page.props.planUsage ?? {};

    const initialTotal =
        payload.total ??
        payload.limit ??
        10; /* fallback to a sensible default for onboarding */
    const initialUsed =
        payload.used ??
        payload.consumption ??
        (initialTotal - (payload.remaining ?? initialTotal));

    const usage = reactive({
        total: Math.max(0, initialTotal),
        used: Math.max(0, initialUsed),
        lastResetAt: payload.last_reset_at ?? null,
        lastRefreshAt: payload.last_refresh_at ?? null,
    });

    const remaining = computed(() =>
        Math.max(0, usage.total - usage.used),
    );

    const limitExceeded = computed(() => remaining.value <= 0);

    const percentUsed = computed(() => {
        if (usage.total === 0) {
            return 0;
        }

        return Math.min(
            100,
            Math.round((usage.used / usage.total) * 100),
        );
    });

    const usageLabel = computed(
        () =>
            `${remaining.value} remaining / ${usage.total} total appraisals`,
    );

    const helperCopy = computed(() =>
        limitExceeded.value
            ? 'Usage limit reached. Upgrade to unlock more appraisals.'
            : 'Tip: refresh resets with your billing cycle.',
    );

    const registerConsumption = (count = 1) => {
        usage.used = Math.min(usage.total, usage.used + Math.max(count, 0));
    };

    const resetUsage = (total?: number) => {
        usage.used = 0;
        if (typeof total === 'number' && total > 0) {
            usage.total = total;
        }
    };

    return {
        usage: readonly(usage),
        remaining,
        limitExceeded,
        percentUsed,
        usageLabel,
        helperCopy,
        registerConsumption,
        resetUsage,
    };
};
