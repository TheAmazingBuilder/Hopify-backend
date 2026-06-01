<?php

namespace App\Modules\Patient\Services;

use App\Modules\Patient\Models\MedicalRecord;
use App\Modules\Patient\Models\Patient;

class MedicalRecordService
{
    public function createMedicalRecord(Patient $patient, array $data): MedicalRecord
    {
        // Validation et création
        $record = MedicalRecord::create([
            'patient_uuid' => $patient->uuid,
            'record_type'  => $data['record_type'] ?? 'consultation',
            'diagnosis'    => $data['diagnosis'] ?? null,
            'observations' => $data['observations'] ?? null,
            'treatment'    => $data['treatment'] ?? null,
            'appointment_date' => $data['appointment_date'] ?? null,
        ]);
        
        return $record;
    }
}
