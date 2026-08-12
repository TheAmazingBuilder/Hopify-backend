<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'doctor' => $this->whenLoaded('doctor', fn () => [
                'uuid' => $this->doctor->uuid,
                'name' => $this->doctor->fname . ' ' . $this->doctor->lname,
                'specialization' => $this->doctor->specialization,
            ]),
            'day_of_week' => $this->day_of_week,
            'day_name' => $this->getDayName(),
            'start_time' => $this->start_time?->format('H:i'),
            'end_time' => $this->end_time?->format('H:i'),
            'duration_minutes' => $this->durationMinutes(),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    private function getDayName(): string
    {
        $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        return $days[$this->day_of_week] ?? 'Inconnu';
    }
}
