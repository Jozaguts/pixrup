<?php

namespace App\Services\Files;
use App\Application\Shared\Enums\LogoFileType;
use Illuminate\Validation\ValidationException;

class LogoStorageService
{

    public function validate($image)
    {
        if(!LogoFileType::isValid($image)) {
            $types= implode(', ',LogoFileType::all());
            throw new \RuntimeException(
                'The logo provided must be of type '.$types
            );
        }

        // Logic to validate image
    }
    public function upload($image)
    {
        // Logic to upload image
    }

    public function delete($imagePath)
    {
        // Logic to delete image
    }

    public function optimize($imagePath)
    {
        // Logic to optimize image
    }

}
