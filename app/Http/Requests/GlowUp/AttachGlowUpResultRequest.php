<?php

namespace App\Http\Requests\GlowUp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachGlowUpResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                Rule::in(['save_to_property', 'add_to_report']),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function validatedPayload(): array
    {
        $validated = $this->validated();

        return [
            'action' => $validated['action'],
            'notes' => $validated['notes'] ?? null,
        ];
    }
}
