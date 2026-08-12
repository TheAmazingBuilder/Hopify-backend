<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Actions;

use App\Modules\Hospitalization\DTOs\DischargeHospitalizationDTO;
use App\Modules\Hospitalization\Models\Hospitalization;
use App\Modules\Hospitalization\Repositories\HospitalizationRepositoryInterface;
use App\Modules\Hospitalization\Repositories\BedRepositoryInterface;
use App\Modules\Hospitalization\Repositories\BedAssignmentRepositoryInterface;
use Illuminate\Validation\ValidationException;

class DischargePatientAction
{
    public function __construct(
        protected HospitalizationRepositoryInterface $hospitalizationRepository,
        protected BedRepositoryInterface $bedRepository,
        protected BedAssignmentRepositoryInterface $bedAssignmentRepository,
    ) {}

    public function execute(string $hospitalizationUuid, DischargeHospitalizationDTO $dto): Hospitalization
    {
        $hospitalization = $this->hospitalizationRepository->findByUuid($hospitalizationUuid);

        if (! $hospitalization) {
            throw ValidationException::withMessages([
                'hospitalization' => ['Hospitalisation introuvable.'],
            ]);
        }

        if (! $hospitalization->isActive()) {
            throw ValidationException::withMessages([
                'hospitalization' => ['Ce patient est déjà sorti.'],
            ]);
        }

        // Met à jour l'hospitalisation
        $this->hospitalizationRepository->update($hospitalizationUuid, [
            'status' => 'discharged',
            'discharged_at' => now(),
            'discharge_diagnosis' => $dto->discharge_diagnosis,
            'discharge_notes' => $dto->discharge_notes,
            'discharge_type' => $dto->discharge_type,
        ]);

        // Libère le lit
        $this->bedRepository->updateStatus($hospitalization->bed_uuid, 'available');

        // Libère l'assignation de lit
        $this->bedAssignmentRepository->releaseCurrent($hospitalizationUuid);

        return $hospitalization->fresh();
    }
}
