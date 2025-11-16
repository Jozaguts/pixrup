<?php

namespace App\Infrastructure\Property\Persistence;

use App\Domain\Properties\Repositories\PropertyPhotoRepositoryInterface;
use App\Models\PropertyPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EloquentPropertyPhotoRepository implements PropertyPhotoRepositoryInterface
{
    public function store($photos, $property): void
    {
        foreach ($photos as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store("/properties/{$property->id}");

            PropertyPhoto::create([
                'property_id' => $property->id,
                'path' => Storage::url($path),
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);
        }
    }
}