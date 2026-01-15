<?php

declare(strict_types=1);

namespace App\Interface\Properties\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelSubscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }
}
