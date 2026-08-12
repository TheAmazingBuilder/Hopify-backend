<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'max:30', 'in:pending,confirmed,cancelled,completed,no_show'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }
}
