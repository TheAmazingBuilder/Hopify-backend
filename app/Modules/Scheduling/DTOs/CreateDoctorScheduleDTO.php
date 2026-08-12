<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\DTOs;

use App\Modules\Scheduling\Requests\StoreDoctorScheduleRequest;
use Carbon\Carbon;

readonly class CreateDoctorScheduleDTO
{
    public function __construct(
        public string $doctor_uuid,
        public int $day_of_week,
        public Carbon $start_time,
        public Carbon $end_time,
        public bool $is_active = true,
    ) {}

    public static function fromRequest(StoreDoctorScheduleRequest $request): self
    {
        return new self(
            doctor_uuid: $request->validated('doctor_uuid'),
            day_of_week: (int) $request->validated('day_of_week'),
            start_time: Carbon::parse($request->validated('start_time')),
            end_time: Carbon::parse($request->validated('end_time')),
            is_active: $request->boolean('is_active', true),
        );
    }

    public function toArray(): array
    {
        return [
            'doctor_uuid' => $this->doctor_uuid,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time->format('H:i:s'),
            'end_time' => $this->end_time->format('H:i:s'),
            'is_active' => $this->is_active,
        ];
    }
}
