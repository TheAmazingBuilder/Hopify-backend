<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalizeConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consultation_uuid' => [
                'required',
                'uuid',
                'exists:consultations,uuid',
            ],

            'finalized_by_uuid' => [
                'required',
                'uuid',
                'exists:users,uuid',
            ],
        ];
    }
}