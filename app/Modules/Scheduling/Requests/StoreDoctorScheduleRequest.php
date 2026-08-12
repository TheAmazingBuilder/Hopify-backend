<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_uuid' => ['required', 'string', 'uuid', 'exists:employees,uuid'],
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'day_of_week.between' => 'Le jour doit être entre 0 (dimanche) et 6 (samedi).',
            'end_time.after' => 'L'heure de fin doit être après l'heure de début.',
        ];
    }
}
