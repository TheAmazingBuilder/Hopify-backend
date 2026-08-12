<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportImagingResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imaging_result_uuid' => [
                'required',
                'uuid',
                'exists:imaging_results,uuid',
            ],

            'radiologist_uuid' => [
                'required',
                'uuid',
                'exists:employees,uuid',
            ],

            'report' => [
                'required',
                'string',
            ],

            'impression' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_critical' => [
                'required',
                'boolean',
            ],
        ];
    }
}