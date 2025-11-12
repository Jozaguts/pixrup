import type { AppPageProps, PlanUsagePayload } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, reactive, readonly } from 'vue';

export const usePlanUsage = () => {
    const page = usePage<AppPageProps<{ planUsage?: PlanUsagePayload }>>();

    const payload = page.props.planUsage ?? ({} as Partial<PlanUsagePayload>);

    const initialTotal =
        (payload.limit ?? null) ??
        10; /* fallback to a sensible default for onboarding */
    const initialUsed =
        payload.used ??
        initialTotal - (payload.remaining ?? initialTotal);

    const usage = reactive({
        total: Math.max(0, initialTotal),
        used: Math.max(0, initialUsed),
        lastResetAt: payload.resets_at ?? null,
        lastRefreshAt: null,
    });

    const remaining = computed(() => Math.max(0, usage.total - usage.used));

    const limitExceeded = computed(() => remaining.value <= 0);

    const percentUsed = computed(() => {
        if (usage.total === 0) {
            return 0;
        }

        return Math.min(100, Math.round((usage.used / usage.total) * 100));
    });

    const usageLabel = computed(
        () => `${remaining.value} remaining / ${usage.total} total appraisals`,
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
