<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\DTOs;

readonly class TransferHospitalizationDTO
{
    public function __construct(
        public string $to_bed_uuid,
        public string $reason,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            to_bed_uuid: $data['to_bed_uuid'],
            reason: $data['reason'],
        );
    }
}
