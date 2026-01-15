<?php

declare(strict_types=1);

namespace App\Interface\Properties\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribePlanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'price_id' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }
}
