<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Actions;

use App\Modules\Hospitalization\DTOs\CreateHospitalizationDTO;
use App\Modules\Hospitalization\Models\Hospitalization;
use App\Modules\Hospitalization\Repositories\HospitalizationRepositoryInterface;
use App\Modules\Hospitalization\Repositories\BedRepositoryInterface;
use App\Modules\Hospitalization\Repositories\BedAssignmentRepositoryInterface;
use Illuminate\Validation\ValidationException;

class AdmitPatientAction
{
    public function __construct(
        protected HospitalizationRepositoryInterface $hospitalizationRepository,
        protected BedRepositoryInterface $bedRepository,
        protected BedAssignmentRepositoryInterface $bedAssignmentRepository,
    ) {}

    public function execute(CreateHospitalizationDTO $dto): Hospitalization
    {
        // Vérifie qu'aucune hospitalisation active n'existe pour ce patient
        $existing = $this->hospitalizationRepository->getActiveByPatient($dto->patient_uuid);
        if ($existing) {
            throw ValidationException::withMessages([
                'patient_uuid' => ['Ce patient est déjà hospitalisé activement.'],
            ]);
        }

        // Vérifie que le lit est disponible
        $bed = $this->bedRepository->findByUuid($dto->bed_uuid);
        if (! $bed || ! $bed->isAvailable()) {
            throw ValidationException::withMessages([
                'bed_uuid' => ['Le lit sélectionné n'est pas disponible.'],
            ]);
        }

        // Crée l'hospitalisation
        $hospitalization = $this->hospitalizationRepository->create($dto->toArray());

        // Occupe le lit
        $this->bedRepository->updateStatus($dto->bed_uuid, 'occupied');

        // Crée l'assignation de lit
        $this->bedAssignmentRepository->create([
            'hospitalization_uuid' => $hospitalization->uuid,
            'bed_uuid' => $dto->bed_uuid,
            'assigned_at' => now(),
            'assigned_by_uuid' => $dto->admitted_by_uuid,
        ]);

        return $hospitalization;
    }
}
