<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpyHuntFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'radius' => ['nullable','numeric'],
            'property_types' => ['nullable','array'],
            'price_min' => ['nullable','numeric'],
            'price_max' => ['nullable','numeric'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
