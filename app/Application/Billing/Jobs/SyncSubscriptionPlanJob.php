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

final class SyncSubscriptionPlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const RETRY_DELAYS = [60, 120, 300, 600, 900, 1800];
    private const MAX_ATTEMPTS = 6;

    public int $tries = 3;

    public function __construct(
        public readonly string $userId,
        public readonly string $subscriptionId,
    ) {
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(StripeSubscriptionPlanSyncService $planSync): void
    {
        $user = User::query()->find($this->userId);
        if (! $user || ! $user->stripe_id) {
            return;
        }

        $localSubscription = $user->subscription('default');
        if ($localSubscription && $localSubscription->stripe_id === $this->subscriptionId) {
            $localStatus = SubscriptionStatus::fromStripe($localSubscription->stripe_status);
            if ($localStatus?->isActive() && $localSubscription->stripe_price) {
                $planSync->syncUserForPrice($user, $localSubscription->stripe_price);

                return;
            }
        }

        $subscription = Cashier::stripe()->subscriptions->retrieve($this->subscriptionId, [
            'expand' => ['items.data.price'],
        ]);

        $customerId = (string) ($subscription->customer ?? '');
        if ($customerId !== '' && $customerId !== $user->stripe_id) {
            return;
        }

        $status = SubscriptionStatus::fromStripe((string) ($subscription->status ?? ''));
        if (! $status?->isActive()) {
            $this->scheduleRetry();

            return;
        }

        $priceId = (string) data_get($subscription, 'items.data.0.price.id', '');
        if ($priceId === '') {
            return;
        }

        if ($localSubscription) {
            $periodEnd = (int) ($subscription->current_period_end ?? 0);
            if ($periodEnd > 0) {
                $localSubscription->forceFill([
                    'current_period_ends_at' => CarbonImmutable::createFromTimestamp($periodEnd)->toDateTimeString(),
                ])->save();
            }
        }

        $planSync->syncUserForPrice($user, $priceId);
    }

    private function scheduleRetry(): void
    {
        if ($this->attempts() >= self::MAX_ATTEMPTS) {
            return;
        }

        $attempt = max(1, $this->attempts());
        $delayIndex = min($attempt - 1, count(self::RETRY_DELAYS) - 1);

        $this->release(self::RETRY_DELAYS[$delayIndex]);
    }
}
