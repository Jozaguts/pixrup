<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery as MockeryAlias;

afterEach(function () {
    MockeryAlias::close();
});

test('google callback creates a new user and logs them in', function () {
    $googleUser = MockeryAlias::mock(SocialiteUser::class);
    $googleUser->shouldReceive('getId')->andReturn('google-id-123');
    $googleUser->shouldReceive('getEmail')->andReturn('google-user@example.com');
    $googleUser->shouldReceive('getName')->andReturn('Google User');
    $googleUser->shouldReceive('getNickname')->andReturn(null);

    $provider = MockeryAlias::mock(Provider::class);
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($googleUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'email' => 'google-user@example.com',
        'provider' => 'google',
        'provider_id' => 'google-id-123',
    ]);
});

test('google callback links existing user by email', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'existing@example.com',
        'provider' => null,
        'provider_id' => null,
        'email_verified_at' => null,
    ]);

    $googleUser = MockeryAlias::mock(SocialiteUser::class);
    $googleUser->shouldReceive('getId')->andReturn('google-existing-456');
    $googleUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $googleUser->shouldReceive('getName')->andReturn('Existing Google User');
    $googleUser->shouldReceive('getNickname')->andReturn(null);

    $provider = MockeryAlias::mock(Provider::class);
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($googleUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticatedAs($user->fresh());

    expect($user->fresh()->provider)->toBe('google');
    expect($user->fresh()->provider_id)->toBe('google-existing-456');
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});
