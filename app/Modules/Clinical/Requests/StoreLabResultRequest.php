<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use App\Shared\Enums\LabAbnormalityLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreLabResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lab_order_item_uuid' => [
                'required',
                'uuid',
                'exists:lab_order_items,uuid',
            ],

            'value' => [
                'required',
                'string',
                'max:100',
            ],

            'unit' => [
                'nullable',
                'string',
                'max:30',
            ],

            'reference_range' => [
                'nullable',
                'string',
                'max:50',
            ],

            'is_abnormal' => [
                'required',
                'boolean',
            ],

            'abnormality_level' => [
                'nullable',
                new Enum(LabAbnormalityLevel::class),
                'required_if:is_abnormal,true',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}