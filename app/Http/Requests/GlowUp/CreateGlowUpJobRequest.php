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
            'image' => [
                'required',
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

        return [
            'room_type' => $validated['room_type'],
            'style' => $validated['style'],
        ];
    }
}
