import type { AppPageProps, PlanUsagePayload, UsageBucketPayload } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, reactive, readonly } from 'vue';

export const usePlanUsage = () => {
    const page = usePage<AppPageProps<{ planUsage?: PlanUsagePayload }>>();

    const payload = page.props.planUsage ?? ({} as Partial<PlanUsagePayload>);
    const docsUsage = payload.usage?.docs as UsageBucketPayload | undefined;
    const fallbackLimit = 10;
    const initialLimit = docsUsage?.limit ?? fallbackLimit;
    const initialUsed = docsUsage?.used ?? 0;
    const initialRemaining =
        docsUsage?.remaining ??
        (initialLimit > 0 ? Math.max(0, initialLimit - initialUsed) : 0);

    const usage = reactive({
        limit: initialLimit,
        used: Math.max(0, initialUsed),
        remaining: initialRemaining,
        isUnlimited: docsUsage?.is_unlimited ?? initialLimit === -1,
        isBlocked: docsUsage?.is_blocked ?? initialLimit === 0,
        canUse: docsUsage?.can_use ?? true,
        percentUsed: docsUsage?.percent_used ?? 0,
        lastResetAt: payload.resets_at ?? null,
        lastRefreshAt: null,
    });

    const recalcUsage = () => {
        usage.isUnlimited = usage.limit === -1;
        usage.isBlocked = usage.limit === 0;

        if (usage.isUnlimited) {
            usage.remaining = null;
            usage.percentUsed = null;
            usage.canUse = true;
            return;
        }

        if (usage.isBlocked) {
            usage.remaining = 0;
            usage.percentUsed = 0;
            usage.canUse = false;
            return;
        }

        usage.remaining = Math.max(0, usage.limit - usage.used);
        usage.percentUsed =
            usage.limit > 0
                ? Math.min(100, Math.round((usage.used / usage.limit) * 100))
                : 0;
        usage.canUse = usage.used < usage.limit;
    };

    recalcUsage();

    const remaining = computed(() =>
        usage.remaining === null ? null : Math.max(0, usage.remaining),
    );

    const limitExceeded = computed(() => !usage.canUse);

    const percentUsed = computed(() => usage.percentUsed ?? 0);

    const usageLabel = computed(() => {
        if (usage.isBlocked) {
            return 'Appraisals unavailable on your plan';
        }

        if (usage.isUnlimited) {
            return 'Unlimited appraisals';
        }

        const remainingLabel = remaining.value ?? 0;
        return `${remainingLabel} remaining / ${usage.limit} total appraisals`;
    });

    const helperCopy = computed(() => {
        if (usage.isBlocked) {
            return 'Upgrade your plan to unlock appraisals.';
        }

        if (usage.isUnlimited) {
            return 'Unlimited appraisals—keep exploring.';
        }

        return limitExceeded.value
            ? 'Usage limit reached. Upgrade to unlock more appraisals.'
            : 'Tip: refresh resets with your billing cycle.';
    });

    const registerConsumption = (count = 1) => {
        const increment = Math.max(count, 0);
        usage.used = Math.max(0, usage.used + increment);
        recalcUsage();
    };

    const resetUsage = (limit?: number) => {
        usage.used = 0;
        if (typeof limit === 'number') {
            usage.limit = limit;
        }
        recalcUsage();
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
