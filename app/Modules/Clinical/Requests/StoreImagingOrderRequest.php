<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use App\Shared\Enums\ImagingModality;
use App\Shared\Enums\ImagingUrgency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreImagingOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_uuid' => [
                'required',
                'uuid',
                'exists:patients,uuid',
            ],

            'ordered_by_uuid' => [
                'required',
                'uuid',
                'exists:employees,uuid',
            ],

            'consultation_uuid' => [
                'nullable',
                'uuid',
                'exists:consultations,uuid',
            ],

            'modality' => [
                'required',
                new Enum(ImagingModality::class),
            ],

            'body_part' => [
                'required',
                'string',
                'max:255',
            ],

            'urgency' => [
                'required',
                new Enum(ImagingUrgency::class),
            ],

            'clinical_indication' => [
                'nullable',
                'string',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}