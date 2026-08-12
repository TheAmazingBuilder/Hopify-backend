<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelPrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prescription_uuid' => [
                'required',
                'uuid',
                'exists:prescriptions,uuid',
            ],
        ];
    }
}