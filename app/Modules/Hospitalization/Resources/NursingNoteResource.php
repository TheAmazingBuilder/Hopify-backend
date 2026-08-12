<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NursingNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'type' => $this->type,
            'note' => $this->note,
            'noted_at' => $this->noted_at?->toIso8601String(),
            'nurse' => $this->whenLoaded('nurse', fn () => [
                'uuid' => $this->nurse->uuid,
                'name' => $this->nurse->name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
