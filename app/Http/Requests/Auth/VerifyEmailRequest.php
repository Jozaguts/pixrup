<?php

namespace App\Http\Requests\Auth;

use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyEmailRequest extends \Laravel\Fortify\Http\Requests\VerifyEmailRequest
{
    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            redirect()->route('verification.notice')->with('status', 'verification-link-invalid')
        );
    }
}
