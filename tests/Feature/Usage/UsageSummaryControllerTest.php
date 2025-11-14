<?php

use App\Models\Property;
use App\Models\UsagePropertyMonthly;
use App\Models\User;

test('usage summary endpoint returns current snapshot', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'plan' => 'professional',
    ]);

    $property = Property::factory()->create();
    $periodKey = now('UTC')->format('Y-m');

    UsagePropertyMonthly::query()->create([
        'account_scope_type' => 'user',
        'account_scope_id' => $user->id,
        'user_id' => $user->id,
        'property_id' => $property->id,
        'period_key' => $periodKey,
        'plan_snapshot' => 'professional',
        'action_first' => 'appraisal',
        'first_action_at' => now('UTC'),
    ]);

    $this->actingAs($user);

    $response = $this->withHeaders(['Accept' => 'application/json'])
        ->get(route('usage.summary'));

    $response->assertOk()
        ->assertJsonPath('used', 1)
        ->assertJsonPath('plan.tier', 'professional')
        ->assertJsonPath('period_key', $periodKey);
});
