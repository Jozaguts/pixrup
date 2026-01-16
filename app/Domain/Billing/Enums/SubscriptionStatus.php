<?php

declare(strict_types=1);

namespace App\Domain\Billing\Enums;

enum SubscriptionStatus: string
{
    case INCOMPLETE = 'incomplete';
    case INCOMPLETE_EXPIRED = 'incomplete_expired';
    case TRIALING = 'trialing';
    case ACTIVE = 'active';
    case PAST_DUE = 'past_due';
    case CANCELED = 'canceled';
    case UNPAID = 'unpaid';
    case PAUSED = 'paused';

    public static function fromStripe(?string $status): ?self
    {
        if (! $status) {
            return null;
        }

        return match (strtolower($status)) {
            'incomplete' => self::INCOMPLETE,
            'incomplete_expired' => self::INCOMPLETE_EXPIRED,
            'trialing' => self::TRIALING,
            'active' => self::ACTIVE,
            'past_due' => self::PAST_DUE,
            'canceled' => self::CANCELED,
            'unpaid' => self::UNPAID,
            'paused' => self::PAUSED,
            default => null,
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE || $this === self::TRIALING;
    }
}
