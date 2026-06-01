<?php

namespace App\Modules\Patient\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'uuid' => $this->uuid,
            'mrn' => $this->mrn,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'full_name' => $this->full_name,
            'dob' => $this->dob?->format('Y-m-d'),
            'gender' => $this->gender,
            'blood_type' => $this->blood_type,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'is_deceased' => $this->is_deceased,
            
            // Relations (optionnelles)
            'contacts' => JsonResource::collection($this->whenLoaded('contacts')),
            'allergies' => JsonResource::collection($this->whenLoaded('allergies')),
            'insurances' => JsonResource::collection($this->whenLoaded('insurances')),
            'antecedents' => JsonResource::collection($this->whenLoaded('antecedents')),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
