<?php

declare(strict_types=1);

namespace App\Modules\Clinical\DTOs;

use Illuminate\Http\Request;

final readonly class CreateConsultationDTO
{
    public function __construct(
        public string $patientUuid,
        public string $doctorUuid,
        public string $consultationDate,
        public ?string $appointmentUuid = null,
        public ?string $hospitalizationUuid = null,
        public ?string $chiefComplaint = null,
        public ?string $historyOfIllness = null,
        public ?string $reviewOfSystems = null,
        public ?string $physicalExamination = null,
        public ?string $assessment = null,
        public ?string $plan = null,
        public ?string $followUpDate = null,
        public ?string $followUpInstructions = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            patientUuid: $request->validated('patient_uuid'),
            doctorUuid: $request->validated('doctor_uuid'),
            consultationDate: $request->validated('consultation_date'),
            appointmentUuid: $request->validated('appointment_uuid'),
            hospitalizationUuid: $request->validated('hospitalization_uuid'),
            chiefComplaint: $request->validated('chief_complaint'),
            historyOfIllness: $request->validated('history_of_illness'),
            reviewOfSystems: $request->validated('review_of_systems'),
            physicalExamination: $request->validated('physical_examination'),
            assessment: $request->validated('assessment'),
            plan: $request->validated('plan'),
            followUpDate: $request->validated('follow_up_date'),
            followUpInstructions: $request->validated('follow_up_instructions'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'patient_uuid' => $this->patientUuid,
            'doctor_uuid' => $this->doctorUuid,
            'appointment_uuid' => $this->appointmentUuid,
            'hospitalization_uuid' => $this->hospitalizationUuid,
            'chief_complaint' => $this->chiefComplaint,
            'history_of_illness' => $this->historyOfIllness,
            'review_of_systems' => $this->reviewOfSystems,
            'physical_examination' => $this->physicalExamination,
            'assessment' => $this->assessment,
            'plan' => $this->plan,
            'follow_up_date' => $this->followUpDate,
            'follow_up_instructions' => $this->followUpInstructions,
            'consultation_date' => $this->consultationDate,
        ], static fn ($value) => $value !== null);
    }
}