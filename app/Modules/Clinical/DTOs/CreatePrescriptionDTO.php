<?php

declare(strict_types=1);

namespace App\Modules\Clinical\DTOs;

use Illuminate\Http\Request;

final readonly class CreatePrescriptionDTO
{
    /**
     * @param array<int, array{
     *     medication_uuid: string,
     *     dosage: string,
     *     frequency: string,
     *     route?: ?string,
     *     duration_days?: ?int,
     *     quantity?: ?int,
     *     instructions?: ?string,
     *     is_substitutable?: bool
     * }> $items
     */
    public function __construct(
        public string $patientUuid,
        public string $doctorUuid,
        public array $items,
        public ?string $consultationUuid = null,
        public ?string $prescriptionNumber = null,
        public ?string $notes = null,
        public ?string $validUntil = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            patientUuid: $request->validated('patient_uuid'),
            doctorUuid: $request->validated('doctor_uuid'),
            items: $request->validated('items', []),
            consultationUuid: $request->validated('consultation_uuid'),
            prescriptionNumber: $request->validated('prescription_number'),
            notes: $request->validated('notes'),
            validUntil: $request->validated('valid_until'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'consultation_uuid' => $this->consultationUuid,
            'patient_uuid' => $this->patientUuid,
            'doctor_uuid' => $this->doctorUuid,
            'prescription_number' => $this->prescriptionNumber,
            'notes' => $this->notes,
            'valid_until' => $this->validUntil,
        ], static fn ($value) => $value !== null);
    }
}