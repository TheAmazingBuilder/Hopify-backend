<?php

namespace App\Modules\Scheduling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_uuid' => ['required', 'exists:patients,uuid'],
            'doctor_uuid' => ['required', 'exists:users,uuid'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'type' => ['nullable', 'string'],
            'reason' => ['nullable', 'string'],
        ];
    }
}
