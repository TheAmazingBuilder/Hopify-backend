<?php

namespace App\Modules\Patient\Services;

use App\Modules\Patient\Models\Patient;

class PatientService
{
    public function createPatient(array $data): Patient
    {
        // Validation et création
        $patient = Patient::create($data);
        
        // Génération du numéro d'immatriculation
        $patient->registration_number = $this->generateRegistrationNumber($patient);
        $patient->save();
        
        return $patient;
    }

    public function updatePatient(Patient $patient, array $data): Patient
    {
        $patient->update($data);
        return $patient;
    }

    /**
     * Génère un numéro d'immatriculation unique
     */
    private function generateRegistrationNumber(Patient $patient): string
    {
        $tenantPrefix = strtoupper(substr($patient->tenant->code, 0, 3));
        $year = date('Y');
        $nextId = $patient->tenant->patients()->count() + 1;
        
        return "{$tenantPrefix}-{$year}-{$nextId}";
    }
}
