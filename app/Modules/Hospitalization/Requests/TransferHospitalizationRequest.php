<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferHospitalizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_bed_uuid' => ['required', 'string', 'uuid', 'exists:beds,uuid'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
