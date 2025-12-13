<?php

namespace App\Application\Properties\DTOs;

class DetailsAdvancedDTO
{
    public function __construct(
        public array $subjectAddress,
        public array $publicRecords,
        public array $hc,
        public array $assessment,
        public array $errors) {

    }
    public function toArray(): array
    {
        return [
            'subjectAddress' => $this->subjectAddress,
            'publicRecords' => $this->publicRecords,
            'hc' => $this->hc,
            'assessment' => $this->assessment,
            'errors' => $this->errors,
        ];
    }

}