<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_uuid' => ['required', 'string', 'uuid', 'exists:departments,uuid'],
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:30', 'in:consultation,recovery,surgery'],
            'floor' => ['nullable', 'integer', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
