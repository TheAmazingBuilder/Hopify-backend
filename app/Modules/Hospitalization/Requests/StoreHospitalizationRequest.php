<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHospitalizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_uuid' => ['required', 'string', 'uuid', 'exists:patients,uuid'],
            'bed_uuid' => ['required', 'string', 'uuid', 'exists:beds,uuid'],
            'admitted_by_uuid' => ['required', 'string', 'uuid', 'exists:employees,uuid'],
            'attending_doctor_uuid' => ['nullable', 'string', 'uuid', 'exists:employees,uuid'],
            'admission_diagnosis' => ['required', 'string', 'max:2000'],
            'admitted_at' => ['required', 'date'],
            'status' => ['sometimes', 'string', 'max:20', 'in:active,discharged,transferred,deceased'],
        ];
    }
}
