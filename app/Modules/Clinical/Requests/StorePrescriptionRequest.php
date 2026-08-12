<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use App\Shared\Enums\EmployeeRoleType;
use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
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

            'doctor_uuid' => [
                'required',
                'uuid',
                'exists:employees,uuid',
            ],

            'consultation_uuid' => [
                'nullable',
                'uuid',
                'exists:consultations,uuid',
            ],

            'prescription_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'valid_until' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*' => [
                'required',
                'array',
            ],

            'items.*.medication_uuid' => [
                'required',
                'uuid',
                'exists:medications,uuid',
                'distinct',
            ],

            'items.*.dosage' => [
                'required',
                'string',
                'max:255',
            ],

            'items.*.frequency' => [
                'required',
                'string',
                'max:255',
            ],

            'items.*.route' => [
                'nullable',
                'string',
                'max:30',
            ],

            'items.*.duration_days' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'items.*.quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'items.*.instructions' => [
                'nullable',
                'string',
            ],

            'items.*.is_substitutable' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}