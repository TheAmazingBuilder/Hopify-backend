<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_uuid' => ['required', 'string', 'uuid', 'exists:rooms,uuid'],
            'name' => ['required', 'string', 'max:20'],
            'type' => ['required', 'string', 'max:30', 'in:standard,icu,pediatric,bariatric'],
            'status' => ['required', 'string', 'max:20', 'in:available,occupied,maintenance,reserved'],
        ];
    }
}
