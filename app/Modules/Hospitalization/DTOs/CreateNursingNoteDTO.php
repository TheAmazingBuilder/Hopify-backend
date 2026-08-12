<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\DTOs;

use App\Modules\Hospitalization\Requests\StoreNursingNoteRequest;
use Carbon\Carbon;

readonly class CreateNursingNoteDTO
{
    public function __construct(
        public string $hospitalization_uuid,
        public string $nurse_uuid,
        public string $type,
        public string $note,
        public Carbon $noted_at,
    ) {}

    public static function fromRequest(StoreNursingNoteRequest $request): self
    {
        return new self(
            hospitalization_uuid: $request->validated('hospitalization_uuid'),
            nurse_uuid: $request->validated('nurse_uuid'),
            type: $request->validated('type'),
            note: $request->validated('note'),
            noted_at: Carbon::parse($request->validated('noted_at', now()->toDateTimeString())),
        );
    }

    public function toArray(): array
    {
        return [
            'hospitalization_uuid' => $this->hospitalization_uuid,
            'nurse_uuid' => $this->nurse_uuid,
            'type' => $this->type,
            'note' => $this->note,
            'noted_at' => $this->noted_at,
        ];
    }
}
