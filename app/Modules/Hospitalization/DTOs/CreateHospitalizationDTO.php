<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\DTOs;

use App\Modules\Hospitalization\Requests\StoreHospitalizationRequest;
use Carbon\Carbon;

readonly class CreateHospitalizationDTO
{
    public function __construct(
        public string $patient_uuid,
        public string $bed_uuid,
        public string $admitted_by_uuid,
        public ?string $attending_doctor_uuid,
        public string $admission_diagnosis,
        public Carbon $admitted_at,
        public string $status,
    ) {}

    public static function fromRequest(StoreHospitalizationRequest $request): self
    {
        return new self(
            patient_uuid: $request->validated('patient_uuid'),
            bed_uuid: $request->validated('bed_uuid'),
            admitted_by_uuid: $request->validated('admitted_by_uuid'),
            attending_doctor_uuid: $request->validated('attending_doctor_uuid'),
            admission_diagnosis: $request->validated('admission_diagnosis'),
            admitted_at: Carbon::parse($request->validated('admitted_at', now()->toDateTimeString())),
            status: $request->validated('status', 'active'),
        );
    }

    public function toArray(): array
    {
        return [
            'patient_uuid' => $this->patient_uuid,
            'bed_uuid' => $this->bed_uuid,
            'admitted_by_uuid' => $this->admitted_by_uuid,
            'attending_doctor_uuid' => $this->attending_doctor_uuid,
            'admission_diagnosis' => $this->admission_diagnosis,
            'admitted_at' => $this->admitted_at,
            'status' => $this->status,
        ];
    }
}
