<?php

declare(strict_types=1);

namespace App\Modules\Clinical\DTOs;

use Illuminate\Http\Request;

final readonly class RecordLabResultDTO
{
    public function __construct(
        public string $labOrderItemUuid,
        public string $value,
        public ?string $unit = null,
        public ?string $referenceRange = null,
        public bool $isAbnormal = false,
        public ?string $abnormalityLevel = null,
        public ?string $notes = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            labOrderItemUuid: $request->validated('lab_order_item_uuid'),
            value: $request->validated('value'),
            unit: $request->validated('unit'),
            referenceRange: $request->validated('reference_range'),
            isAbnormal: (bool) $request->validated('is_abnormal', false),
            abnormalityLevel: $request->validated('abnormality_level'),
            notes: $request->validated('notes'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'lab_order_item_uuid' => $this->labOrderItemUuid,
            'value' => $this->value,
            'unit' => $this->unit,
            'reference_range' => $this->referenceRange,
            'is_abnormal' => $this->isAbnormal,
            'abnormality_level' => $this->abnormalityLevel,
            'notes' => $this->notes,
        ], static fn ($value) => $value !== null);
    }
}