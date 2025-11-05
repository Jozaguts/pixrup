<?php

/**
 * Description: Listener responsible for updating persistent usage counters on feature consumption.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Keeps user usage metrics aligned with recorded feature usage.
 */

namespace App\Application\Shared\Listeners;

use App\Domain\Shared\Events\FeatureUsed;
use App\Models\User;
use Carbon\Carbon;

/**
 * Description: Reacts to FeatureUsed events by incrementing monthly usage counters for users.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Dashboard usage widgets remain accurate and reset monthly.
 */
class IncrementFeatureUsage
{
    /**
     * Description: Handle the incoming feature usage event and update the user's usage counters.
     * Parameters: FeatureUsed $event Event containing usage metadata.
     * Returns: void.
     * Expected Result: User's usage_count is incremented and reset window adjusted when necessary.
     */
    public function handle(FeatureUsed $event): void
    {
        if ($event->quantity <= 0) {
            return;
        }

        /** @var User|null $user */
        $user = User::query()->find($event->userId);

        if ($user === null) {
            return;
        }

        $now = Carbon::now();
        $resetAt = $user->usage_reset_at ? Carbon::parse($user->usage_reset_at) : null;

        if ($resetAt === null || $resetAt->isPast()) {
            $resetAt = $now->copy()->startOfMonth()->addMonth();
            $user->usage_count = 0;
            $user->usage_reset_at = $resetAt;
        }

        $user->usage_count = (int) $user->usage_count + $event->quantity;
        $user->save();
    }
}
