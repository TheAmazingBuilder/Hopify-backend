<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImagingResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imaging_order_uuid' => [
                'required',
                'uuid',
                'exists:imaging_orders,uuid',
            ],

            'employee_uuid' => [
                'required',
                'uuid',
                'exists:employees,uuid',
            ],

            'file_path' => [
                'nullable',
                'string',
                'max:500',
            ],

            'thumbnail_path' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}