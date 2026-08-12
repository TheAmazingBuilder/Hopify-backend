<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'status_changed_at' => $this->status_changed_at?->toIso8601String(),
            'room' => $this->whenLoaded('room', fn () => new RoomResource($this->room)),
            'current_patient' => $this->whenLoaded('currentHospitalization.patient', fn () => [
                'uuid' => $this->currentHospitalization->patient->uuid,
                'full_name' => $this->currentHospitalization->patient->full_name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
