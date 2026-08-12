<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateLabResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lab_result_uuid' => [
                'required',
                'uuid',
                'exists:lab_results,uuid',
            ],

            'employee_uuid' => [
                'required',
                'uuid',
                'exists:employees,uuid',
            ],
        ];
    }
}