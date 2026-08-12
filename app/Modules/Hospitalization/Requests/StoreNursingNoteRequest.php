<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNursingNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hospitalization_uuid' => ['required', 'string', 'uuid', 'exists:hospitalizations,uuid'],
            'nurse_uuid' => ['required', 'string', 'uuid', 'exists:employees,uuid'],
            'type' => ['required', 'string', 'max:30', 'in:general,medication,observation,wound'],
            'note' => ['required', 'string', 'max:10000'],
            'noted_at' => ['sometimes', 'date'],
        ];
    }
}
