<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Fortify;

class RedirectAsIntended implements Responsable
{
    public function __construct(public string $name)
    {
        //
    }

    public function toResponse($request): RedirectResponse
    {
        $redirect = redirect()->intended(Fortify::redirects($this->name));

        if ($this->name === 'email-verification') {
            $redirect->with('status', 'already-verified');
        }

        return $redirect;
    }
}
