<?php

declare(strict_types=1);

namespace App\Application\Billing\Jobs;

use App\Application\Billing\Services\StripeSubscriptionPlanSyncService;
use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Cashier;

final class FinalizePendingPlanChangeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly string $userId,
    ) {
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [300, 900, 1800];
    }

    public function handle(StripeSubscriptionPlanSyncService $planSync): void
    {
        $user = User::query()->find($this->userId);
        if (! $user || ! $user->stripe_id) {
            return;
        }

        $pendingTier = strtoupper((string) ($user->pending_plan_tier ?? ''));
        $pendingChangeAt = $user->pending_plan_change_at;
        if ($pendingTier === '' || ! $pendingChangeAt) {
            return;
        }

        if (now()->lt($pendingChangeAt)) {
            $this->release(max(60, now()->diffInSeconds($pendingChangeAt)));
            return;
        }

        $subscription = $user->subscription('default');
        if (! $subscription || ! $subscription->stripe_id) {
            return;
        }

        $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($subscription->stripe_id, [
            'expand' => ['items.data.price'],
        ]);

        $status = SubscriptionStatus::fromStripe((string) ($stripeSubscription->status ?? ''));
        if (! $status?->isActive()) {
            return;
        }

        $priceId = (string) data_get($stripeSubscription, 'items.data.0.price.id', '');
        if ($priceId === '') {
            return;
        }

        $periodEnd = (int) ($stripeSubscription->current_period_end ?? 0);
        if ($periodEnd > 0) {
            $subscription->forceFill([
                'current_period_ends_at' => CarbonImmutable::createFromTimestamp($periodEnd)->toDateTimeString(),
            ])->save();
        }

        $resolvedTier = $planSync->tierForPrice($priceId);
        if (! $resolvedTier || $resolvedTier !== $pendingTier) {
            $this->release(300);
            return;
        }

        $planSync->syncUserForPrice($user, $priceId);
    }
}
