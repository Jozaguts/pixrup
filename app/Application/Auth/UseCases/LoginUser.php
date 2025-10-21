<?php

namespace App\Application\Auth\UseCases;

use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\TwoFactorAuthenticatable;

class LoginUser
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    /**
     * Attempt to authenticate a user.
     *
     * @param  array{email:string,password:string,remember?:bool}  $credentials
     * @return string Indicates the next step (either 'authenticated' or 'two-factor')
     */
    public function execute(array $credentials): string
    {
        $userEntity = $this->repository->findByEmail($credentials['email']);

        if (! $userEntity || ! Hash::check($credentials['password'], $userEntity->password)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        /** @var \App\Models\User $user */
        $user = UserModel::query()->findOrFail($userEntity->id);

        if ($this->requiresTwoFactorChallenge($user)) {
            $this->startTwoFactorChallenge($user, $credentials);

            return 'two-factor';
        }

        Auth::login($user, $credentials['remember'] ?? false);

        return 'authenticated';
    }

    private function requiresTwoFactorChallenge(UserModel $user): bool
    {
        if (! Features::enabled(Features::twoFactorAuthentication())) {
            return false;
        }

        if (! in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
            return false;
        }

        if (empty($user->two_factor_secret)) {
            return false;
        }

        if (Fortify::confirmsTwoFactorAuthentication()) {
            return ! is_null($user->two_factor_confirmed_at);
        }

        return true;
    }

    /**
     * Prepare the session for two factor authentication.
     *
     * @param  array{remember?:bool}  $credentials
     */
    private function startTwoFactorChallenge(UserModel $user, array $credentials): void
    {
        session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => (bool) ($credentials['remember'] ?? false),
        ]);

        TwoFactorAuthenticationChallenged::dispatch($user);
    }
}
