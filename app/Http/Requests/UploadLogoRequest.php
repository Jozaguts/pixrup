<?php

namespace App\Http\Requests;

use App\Models\Image;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $uuid = auth()->user()?->uuid;

        return [
            'logo' => [
                'required',
                'image',
                'max:2048', // 2MB
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('images', 'name')->where('user_uuid', $uuid),
                function ($attribute, $value, $fail) use ($uuid) {
                    $count = Image::count();
                    if ($count >= 5) {
                        $fail('You can upload a maximum of 5 logos.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'You already have a file with this name. Please rename the file.',
            'logo.max' => 'Please provide an image up to 2MB.',
            'logo.image' => 'The uploaded file must be a valid image.',
        ];
    }
}
