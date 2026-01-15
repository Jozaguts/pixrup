<?php

namespace App\Http\Requests\GlowUp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGlowUpJobRequest extends FormRequest
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
        $roomTypes = collect(config('glowup.room_types', []))
            ->pluck('value')
            ->filter()
            ->values()
            ->all();

        $styles = collect(config('glowup.styles', []))
            ->pluck('value')
            ->filter()
            ->values()
            ->all();

        $maxSize = max(1, (int) config('glowup.max_upload_size_mb', 10)) * 1024;

        $mimeRules = array_filter(config('glowup.allowed_mimes', []));

        $imageRules = [
            'room_type' => ['required', 'string', Rule::in($roomTypes)],
            'style' => ['required', 'string', Rule::in($styles)],
            'user_instructions' => ['nullable', 'string', 'max:500'],
            'source_job_id' => ['required_without:image', 'nullable', 'integer', Rule::exists('glowup_jobs', 'id')],
            'image' => [
                'required_without:source_job_id',
                'file',
                'image',
                'max:'.$maxSize,
            ],
        ];

        if (! empty($mimeRules)) {
            $imageRules['image'][] = 'mimetypes:'.implode(',', $mimeRules);
        }

        return $imageRules;
    }

    public function validatedPayload(): array
    {
        $validated = $this->validated();
        $userInstructions = $validated['user_instructions'] ?? null;
        if (is_string($userInstructions)) {
            $userInstructions = trim($userInstructions);
            if ($userInstructions === '') {
                $userInstructions = null;
            }
        }

        return [
            'room_type' => $validated['room_type'],
            'style' => $validated['style'],
            'source_job_id' => $validated['source_job_id'] ?? null,
            'user_instructions' => $userInstructions,
        ];
    }
}
