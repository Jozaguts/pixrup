<?php

namespace App\Domain\Properties\Repositories;

use App\Domain\Properties\Entities\PropertyEntity;
use Illuminate\Http\UploadedFile;

interface PropertyPhotoRepositoryInterface
{
    /**
     * @param array<UploadedFile> $photos
     */
    public function store(array $photos, PropertyEntity $property): void;
}