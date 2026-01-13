<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhysicalStateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return isMonthlyUsageLimitExceeded($this->user()); // only allow if user has tokens left;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'property_id' => [
                'required',
                'uuid',
                Rule::exists('properties', 'id')
            ],
            'version' => ['nullable', 'regex:/^latest|v\d+\.\d+\.\d+$/'],
            'force_recompute' => ['nullable', 'boolean']
        ];
    }
}
