<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRoundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hospitalization_uuid' => ['required', 'string', 'uuid', 'exists:hospitalizations,uuid'],
            'doctor_uuid' => ['required', 'string', 'uuid', 'exists:employees,uuid'],
            'subjective' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'objective' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'assessment' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'plan' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'occurred_at' => ['sometimes', 'date'],
        ];
    }
}
