<?php

namespace App\Modules\Scheduling\DTOs;

class CreateAppointmentDTO
{
    public function __construct(
        public string $patient_uuid,
        public string $doctor_uuid,
        public string $start_time,
        public string $end_time,
        public string $type = 'consultation',
        public ?string $reason = null,
        public ?string $notes = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            patient_uuid: $request->validated('patient_uuid'),
            doctor_uuid: $request->validated('doctor_uuid'),
            start_time: $request->validated('start_time'),
            end_time: $request->validated('end_time'),
            type: $request->validated('type', 'consultation'),
            reason: $request->validated('reason'),
            notes: $request->validated('notes'),
        );
    }

    public function toArray(): array
    {
        return [
            'patient_uuid' => $this->patient_uuid,
            'doctor_uuid' => $this->doctor_uuid,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'type' => $this->type,
            'reason' => $this->reason,
            'notes' => $this->notes,
        ];
    }
}
