<?php

namespace App\Application\Properties\UseCases;

use App\Application\Properties\DTOs\CreatePropertyDTO;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Domain\Properties\Repositories\PropertyPhotoRepositoryInterface;
use App\Domain\Properties\Repositories\PropertyRepositoryInterface;
use Illuminate\Http\UploadedFile;

readonly class CreatePropertyUseCase
{
    public function __construct(
        private PropertyRepositoryInterface $propertyRepository,
        private PropertyPhotoRepositoryInterface $propertyPhotoRepository,
    ) {}

    /**
     * @param array<UploadedFile>|null $photos
     */
    public function execute(CreatePropertyDTO $createPropertyDTO, ?array $photos ): PropertyEntity
    {
        $propertyEntity = new PropertyEntity(
            id: null,
            title: $createPropertyDTO->title,
            status: $createPropertyDTO->status,
            address: $createPropertyDTO->address,
            city: $createPropertyDTO->city,
            state: $createPropertyDTO->state,
            postal_code: $createPropertyDTO->postal_code,
            country: $createPropertyDTO->country,
            lat: $createPropertyDTO->lat,
            lng: $createPropertyDTO->lng,
            place_id: $createPropertyDTO->place_id,
            metadata: $createPropertyDTO->metadata,
            property_type: $createPropertyDTO->property_type,
            bedrooms: $createPropertyDTO->bedrooms,
            bathrooms: $createPropertyDTO->bathrooms,
            square_footage: $createPropertyDTO->square_footage,
        );

        $savedProperty = $this->propertyRepository->save($propertyEntity);

        if (!empty($photos)){
            $this->propertyPhotoRepository->store($photos, $savedProperty);
        }

        return $savedProperty;
    }
}