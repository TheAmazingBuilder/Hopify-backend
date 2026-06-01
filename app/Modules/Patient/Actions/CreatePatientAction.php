<?php

namespace App\Modules\Patient\Actions;

use App\Modules\Patient\Models\Patient;
use App\Modules\Patient\Repositories\PatientRepositoryInterface;
use App\Modules\Patient\Events\PatientCreated;
use App\Modules\Patient\DTOs\CreatePatientDTO;
use Illuminate\Support\Str;

class CreatePatientAction
{
    public function __construct(
        protected PatientRepositoryInterface $repository
    ) {}

    /**
     * Exécute la création d'un patient.
     * 
     * @param CreatePatientDTO $dto
     * @return Patient
     */
    public function execute(CreatePatientDTO $dto): Patient 
    {
        $data = $dto->toArray();

        // Génération automatique du Matricule (MRN) si non fourni
        if (empty($data['mrn'])) {
            $data['mrn'] = $this->generateMrn();
        }

        // Création via le Repository (Abstraction de la DB)
        $patient = $this->repository->create($data);
        
        // Dispatch de l'événement domaine
        event(new PatientCreated($patient));
        
        // Enregistrement de l'audit (Sera actif une fois le modèle AuditLog créé)
        // AuditLog::record('patient.created', $patient);
        
        return $patient;
    }

    /**
     * Génère un matricule unique.
     */
    protected function generateMrn(): string
    {
        return 'PAT-' . date('Ym') . '-' . strtoupper(Str::random(4));
    }
}
