<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;

class PasswordResetResponse implements PasswordResetResponseContract
{
    public function __construct(private string $status)
    {
    }

    public function toResponse($request)
    {
        $message = trans($this->status);

        return $request->wantsJson()
            ? new JsonResponse(['message' => $message], 200)
            : redirect()->route('auth.login.show')->with('status', $message);
    }
}
