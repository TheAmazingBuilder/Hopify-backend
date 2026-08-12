<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\DTOs;

readonly class UpdateAppointmentStatusDTO
{
    public function __construct(
        public string $status,
        public ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            notes: $data['notes'] ?? null,
        );
    }
}
