<?php

declare(strict_types=1);

namespace App\Modules\Clinical\DTOs;

use Illuminate\Http\Request;

final readonly class CreateLabOrderDTO
{
    /**
     * @param array<int, string> $labTestUuids
     */
    public function __construct(
        public string $patientUuid,
        public string $orderedByUuid,
        public array $labTestUuids,
        public ?string $consultationUuid = null,
        public ?string $orderNumber = null,
        public string $priority = 'routine',
        public ?string $clinicalNotes = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            patientUuid: $request->validated('patient_uuid'),
            orderedByUuid: $request->validated('ordered_by_uuid'),
            labTestUuids: $request->validated('lab_test_uuids', []),
            consultationUuid: $request->validated('consultation_uuid'),
            orderNumber: $request->validated('order_number'),
            priority: $request->validated('priority', 'routine'),
            clinicalNotes: $request->validated('clinical_notes'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'consultation_uuid' => $this->consultationUuid,
            'patient_uuid' => $this->patientUuid,
            'ordered_by_uuid' => $this->orderedByUuid,
            'order_number' => $this->orderNumber,
            'priority' => $this->priority,
            'clinical_notes' => $this->clinicalNotes,
        ], static fn ($value) => $value !== null);
    }
}