<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorRoundResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'subjective' => $this->subjective,
            'objective' => $this->objective,
            'assessment' => $this->assessment,
            'plan' => $this->plan,
            'occurred_at' => $this->occurred_at?->toIso8601String(),
            'doctor' => $this->whenLoaded('doctor', fn () => [
                'uuid' => $this->doctor->uuid,
                'name' => $this->doctor->name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
