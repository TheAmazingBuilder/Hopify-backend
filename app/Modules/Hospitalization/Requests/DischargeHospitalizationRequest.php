<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DischargeHospitalizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discharge_diagnosis' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'discharge_notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'discharge_type' => ['required', 'string', 'max:20', 'in:planned,ama,transfer,deceased'],
        ];
    }
}
