<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\DTOs;

use App\Modules\Hospitalization\Requests\StoreDoctorRoundRequest;
use Carbon\Carbon;

readonly class CreateDoctorRoundDTO
{
    public function __construct(
        public string $hospitalization_uuid,
        public string $doctor_uuid,
        public ?string $subjective,
        public ?string $objective,
        public ?string $assessment,
        public ?string $plan,
        public Carbon $occurred_at,
    ) {}

    public static function fromRequest(StoreDoctorRoundRequest $request): self
    {
        return new self(
            hospitalization_uuid: $request->validated('hospitalization_uuid'),
            doctor_uuid: $request->validated('doctor_uuid'),
            subjective: $request->validated('subjective'),
            objective: $request->validated('objective'),
            assessment: $request->validated('assessment'),
            plan: $request->validated('plan'),
            occurred_at: Carbon::parse($request->validated('occurred_at', now()->toDateTimeString())),
        );
    }

    public function toArray(): array
    {
        return [
            'hospitalization_uuid' => $this->hospitalization_uuid,
            'doctor_uuid' => $this->doctor_uuid,
            'subjective' => $this->subjective,
            'objective' => $this->objective,
            'assessment' => $this->assessment,
            'plan' => $this->plan,
            'occurred_at' => $this->occurred_at,
        ];
    }
}
