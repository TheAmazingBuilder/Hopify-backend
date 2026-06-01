<?php

namespace App\Modules\Scheduling\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Patient\Resources\PatientResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'uuid' => $this->uuid,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'type' => $this->type,
            'reason' => $this->reason,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'doctor_uuid' => $this->doctor_uuid,
        ];
    }
}
