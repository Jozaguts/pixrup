<?php

use App\Models\User;

test('usage summary endpoint returns current snapshot', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'plan_tier' => 'PRICE_PRO',
        'used_docs' => 3,
        'used_renders' => 1,
    ]);

    $periodKey = now('UTC')->format('Y-m');

    $this->actingAs($user);

    $response = $this->withHeaders(['Accept' => 'application/json'])
        ->get(route('usage.summary'));

    $response->assertOk()
        ->assertJsonPath('usage.docs.used', 3)
        ->assertJsonPath('usage.renders.used', 1)
        ->assertJsonPath('plan.tier', 'PRICE_PRO')
        ->assertJsonPath('period_key', $periodKey);
});
