<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use App\Shared\Enums\EmployeeRoleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultationRequest extends FormRequest
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
                Rule::exists('employees', 'uuid')
                    ->where('is_active', true),
            ],

            'appointment_uuid' => [
                'nullable',
                'uuid',
                'exists:appointments,uuid',
            ],

            'hospitalization_uuid' => [
                'nullable',
                'uuid',
                'exists:hospitalizations,uuid',
            ],

            'chief_complaint' => [
                'nullable',
                'string',
            ],

            'history_of_illness' => [
                'nullable',
                'string',
            ],

            'review_of_systems' => [
                'nullable',
                'string',
            ],

            'physical_examination' => [
                'nullable',
                'string',
            ],

            'assessment' => [
                'nullable',
                'string',
            ],

            'plan' => [
                'nullable',
                'string',
            ],

            'follow_up_date' => [
                'nullable',
                'date',
            ],

            'follow_up_instructions' => [
                'nullable',
                'string',
            ],

            'consultation_date' => [
                'required',
                'date',
            ],
        ];
    }
}