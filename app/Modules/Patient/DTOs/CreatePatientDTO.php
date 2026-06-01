<?php

namespace App\Modules\Patient\DTOs;

class CreatePatientDTO
{
    public function __construct(
        public string $fname,
        public string $lname,
        public string $dob,
        public string $gender,
        public ?string $blood_type = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $city = null,
        public ?string $address = null,
        public ?string $marital_status = null,
        public ?string $occupation = null,
        public ?string $nationality = null,
        public ?string $mrn = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            fname: $request->validated('fname'),
            lname: $request->validated('lname'),
            dob: $request->validated('dob'),
            gender: $request->validated('gender'),
            blood_type: $request->validated('blood_type'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
            city: $request->validated('city'),
            address: $request->validated('address'),
            marital_status: $request->validated('marital_status'),
            occupation: $request->validated('occupation'),
            nationality: $request->validated('nationality'),
            mrn: $request->validated('mrn'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'fname' => $this->fname,
            'lname' => $this->lname,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'blood_type' => $this->blood_type,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'address' => $this->address,
            'marital_status' => $this->marital_status,
            'occupation' => $this->occupation,
            'nationality' => $this->nationality,
            'mrn' => $this->mrn,
        ]);
    }
}
