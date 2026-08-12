<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'type' => $this->type,
            'floor' => $this->floor,
            'capacity' => $this->capacity,
            'is_active' => $this->is_active,
            'department' => $this->whenLoaded('department', fn () => [
                'uuid' => $this->department->uuid,
                'name' => $this->department->name,
            ]),
            'beds_count' => $this->whenCounted('beds'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
