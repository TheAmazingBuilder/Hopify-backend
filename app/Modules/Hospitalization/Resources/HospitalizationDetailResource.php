<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalizationDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'patient' => $this->whenLoaded('patient', fn () => [
                'uuid' => $this->patient->uuid,
                'full_name' => $this->patient->full_name,
                'mrn' => $this->patient->mrn,
                'phone' => $this->patient->phone,
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
            'discharge_notes' => $this->discharge_notes,
            'discharge_type' => $this->discharge_type,
            'is_active' => $this->isActive(),
            'nursing_notes' => $this->whenLoaded('nursingNotes', fn () => NursingNoteResource::collection($this->nursingNotes)),
            'doctor_rounds' => $this->whenLoaded('doctorRounds', fn () => DoctorRoundResource::collection($this->doctorRounds)),
            'bed_assignments' => $this->whenLoaded('bedAssignments', fn () => $this->bedAssignments->map(fn ($ba) => [
                'uuid' => $ba->uuid,
                'bed' => $ba->bed?->name,
                'assigned_at' => $ba->assigned_at?->toIso8601String(),
                'released_at' => $ba->released_at?->toIso8601String(),
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
