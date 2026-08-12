<?php

declare(strict_types=1);

namespace App\Modules\Clinical\DTOs;

use Illuminate\Http\Request;

final readonly class CreateImagingOrderDTO
{
    public function __construct(
        public string $patientUuid,
        public string $orderedByUuid,
        public string $modality,
        public string $bodyPart,
        public string $urgency = 'routine',
        public ?string $consultationUuid = null,
        public ?string $clinicalIndication = null,
        public ?string $notes = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            patientUuid: $request->validated('patient_uuid'),
            orderedByUuid: $request->validated('ordered_by_uuid'),
            modality: $request->validated('modality'),
            bodyPart: $request->validated('body_part'),
            urgency: $request->validated('urgency', 'routine'),
            consultationUuid: $request->validated('consultation_uuid'),
            clinicalIndication: $request->validated('clinical_indication'),
            notes: $request->validated('notes'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'consultation_uuid' => $this->consultationUuid,
            'patient_uuid' => $this->patientUuid,
            'ordered_by_uuid' => $this->orderedByUuid,
            'modality' => $this->modality,
            'body_part' => $this->bodyPart,
            'urgency' => $this->urgency,
            'clinical_indication' => $this->clinicalIndication,
            'notes' => $this->notes,
        ], static fn ($value) => $value !== null);
    }
}