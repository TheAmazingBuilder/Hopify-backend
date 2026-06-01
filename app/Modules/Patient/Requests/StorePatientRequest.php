<?php

namespace App\Modules\Patient\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Shared\Enums\Gender;
use App\Shared\Enums\BloodType;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Les autorisations fines seront gérées par les Policies
    }

    public function rules(): array
    {
        return [
            'fname' => ['required', 'string', 'max:100'],
            'lname' => ['required', 'string', 'max:100'],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['required', new Enum(Gender::class)],
            'blood_type' => ['nullable', new Enum(BloodType::class)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'marital_status' => ['nullable', 'string'],
            'nationality' => ['nullable', 'string'],
        ];
    }
}
