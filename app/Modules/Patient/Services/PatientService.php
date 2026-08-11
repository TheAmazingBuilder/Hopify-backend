<?php

declare(strict_types=1);

namespace App\Modules\Patient\Services;

use App\Modules\Patient\Models\Patient;
use App\Modules\Patient\Repositories\PatientRepositoryInterface;

class PatientService
{
    public function __construct(
        protected PatientRepositoryInterface $repository
    ) {}

    public function createPatient(array $data): Patient
    {
        $patient = $this->repository->create($data);

        $patient->registration_number = $this->generateRegistrationNumber($patient);
        $patient->save();

        return $patient->fresh();
    }

    public function updatePatient(Patient $patient, array $data): Patient
    {
        $this->repository->update($patient->uuid, $data);
        return $patient->fresh();
    }

    private function generateRegistrationNumber(Patient $patient): string
    {
        $tenantPrefix = strtoupper(substr(tenant('id'), 0, 3));
        $year = now()->year;
        $sequence = Patient::where('tenant_id', tenant('id'))->count();

        return "{$tenantPrefix}-{$year}-" . str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }
}


/**
 * 
 *      
 * <?php

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
     *
    private function generateRegistrationNumber(Patient $patient): string
    {
        $tenantPrefix = strtoupper(substr($patient->tenant->code, 0, 3));
        $year = date('Y');
        $nextId = $patient->tenant->patients()->count() + 1;
        
        return "{$tenantPrefix}-{$year}-{$nextId}";
    }
}
 * 
 */



