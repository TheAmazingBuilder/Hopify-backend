<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\DTOs;

use App\Modules\Hospitalization\Requests\StoreRoomRequest;

readonly class CreateRoomDTO
{
    public function __construct(
        public string $department_uuid,
        public string $name,
        public string $type,
        public ?int $floor,
        public int $capacity,
        public bool $is_active = true,
    ) {}

    public static function fromRequest(StoreRoomRequest $request): self
    {
        return new self(
            department_uuid: $request->validated('department_uuid'),
            name: $request->validated('name'),
            type: $request->validated('type'),
            floor: $request->validated('floor'),
            capacity: (int) $request->validated('capacity', 1),
            is_active: $request->boolean('is_active', true),
        );
    }

    public function toArray(): array
    {
        return [
            'department_uuid' => $this->department_uuid,
            'name' => $this->name,
            'type' => $this->type,
            'floor' => $this->floor,
            'capacity' => $this->capacity,
            'is_active' => $this->is_active,
        ];
    }
}
