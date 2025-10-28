<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateRequest($request);

        $property = DB::transaction(function () use ($validated, $request): Property {
            $property = Property::create([
                'title' => $validated['address'],
                'status' => 'in-progress',
                'address' => $validated['address'],
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'country' => $validated['country'] ?? null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'place_id' => $validated['place_id'] ?? null,
                'metadata' => [
                    'source' => 'ui',
                    'created_via' => 'wizard',
                ],
            ]);

            $this->storePhotos($request, $property);

            return $property;
        });

        return response()->json([
            'id' => $property->id,
            'redirect' => route('properties.show', $property),
            'message' => 'Property successfully created.',
        ], 201);
    }

    /**
     * @throws ValidationException
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'address' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:120'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'place_id' => ['nullable', 'string', 'max:255'],
            'photos.*' => ['nullable', 'file', 'image', 'max:8192'],
        ]);
    }

    protected function storePhotos(Request $request, Property $property): void
    {
        if (! $request->hasFile('photos')) {
            return;
        }

        $files = $request->file('photos');

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store("properties/{$property->id}", 'public');

            PropertyPhoto::create([
                'property_id' => $property->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
