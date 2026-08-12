<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'patient' => $this->whenLoaded('patient', fn () => [
                'uuid' => $this->patient->uuid,
                'full_name' => $this->patient->full_name,
                'mrn' => $this->patient->mrn,
            ]),
            'bed' => $this->whenLoaded('bed', fn () => new BedResource($this->bed)),
            'admitted_by' => $this->whenLoaded('admittedBy', fn () => [
                'uuid' => $this->admittedBy->uuid,
                'name' => $this->admittedBy->name,
            ]),
            'attending_doctor' => $this->whenLoaded('attendingDoctor', fn () => [
                'uuid' => $this->attendingDoctor->uuid,
                'name' => $this->attendingDoctor->name,
            ]),
            'admission_diagnosis' => $this->admission_diagnosis,
            'discharge_diagnosis' => $this->discharge_diagnosis,
            'admitted_at' => $this->admitted_at?->toIso8601String(),
            'discharged_at' => $this->discharged_at?->toIso8601String(),
            'status' => $this->status,
            'discharge_type' => $this->discharge_type,
            'is_active' => $this->isActive(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
