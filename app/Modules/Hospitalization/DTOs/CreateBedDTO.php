<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\DTOs;

use App\Modules\Hospitalization\Requests\StoreBedRequest;

readonly class CreateBedDTO
{
    public function __construct(
        public string $room_uuid,
        public string $name,
        public string $type,
        public string $status,
    ) {}

    public static function fromRequest(StoreBedRequest $request): self
    {
        return new self(
            room_uuid: $request->validated('room_uuid'),
            name: $request->validated('name'),
            type: $request->validated('type'),
            status: $request->validated('status', 'available'),
        );
    }

    public function toArray(): array
    {
        return [
            'room_uuid' => $this->room_uuid,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'status_changed_at' => now(),
        ];
    }
}
