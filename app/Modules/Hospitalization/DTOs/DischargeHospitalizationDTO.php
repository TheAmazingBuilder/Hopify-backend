<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\DTOs;

readonly class DischargeHospitalizationDTO
{
    public function __construct(
        public ?string $discharge_diagnosis,
        public ?string $discharge_notes,
        public string $discharge_type,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            discharge_diagnosis: $data['discharge_diagnosis'] ?? null,
            discharge_notes: $data['discharge_notes'] ?? null,
            discharge_type: $data['discharge_type'],
        );
    }
}
