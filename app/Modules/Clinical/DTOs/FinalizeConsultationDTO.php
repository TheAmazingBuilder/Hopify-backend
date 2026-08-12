<?php

declare(strict_types=1);

namespace App\Modules\Clinical\DTOs;

final readonly class FinalizeConsultationDTO
{
    public function __construct(
        public string $consultationUuid,
        public string $finalizedByUuid,
    ) {
    }

    public function toArray(): array
    {
        return [
            'consultation_uuid' => $this->consultationUuid,
            'finalized_by_uuid' => $this->finalizedByUuid,
        ];
    }
}