<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use App\Shared\Enums\LabOrderPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreLabOrderRequest extends FormRequest
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

            'order_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'priority' => [
                'required',
                new Enum(LabOrderPriority::class),
            ],

            'clinical_notes' => [
                'nullable',
                'string',
            ],

            'lab_test_uuids' => [
                'required',
                'array',
                'min:1',
            ],

            'lab_test_uuids.*' => [
                'required',
                'uuid',
                'exists:lab_tests,uuid',
                'distinct',
            ],
        ];
    }
}