<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Actions;

use App\Modules\Hospitalization\DTOs\TransferHospitalizationDTO;
use App\Modules\Hospitalization\Models\Hospitalization;
use App\Modules\Hospitalization\Repositories\HospitalizationRepositoryInterface;
use App\Modules\Hospitalization\Repositories\BedRepositoryInterface;
use App\Modules\Hospitalization\Repositories\BedAssignmentRepositoryInterface;
use Illuminate\Validation\ValidationException;

class TransferPatientAction
{
    public function __construct(
        protected HospitalizationRepositoryInterface $hospitalizationRepository,
        protected BedRepositoryInterface $bedRepository,
        protected BedAssignmentRepositoryInterface $bedAssignmentRepository,
    ) {}

    public function execute(string $hospitalizationUuid, TransferHospitalizationDTO $dto, string $transferredByUuid): Hospitalization
    {
        $hospitalization = $this->hospitalizationRepository->findByUuid($hospitalizationUuid);

        if (! $hospitalization) {
            throw ValidationException::withMessages([
                'hospitalization' => ['Hospitalisation introuvable.'],
            ]);
        }

        if (! $hospitalization->isActive()) {
            throw ValidationException::withMessages([
                'hospitalization' => ['Impossible de transférer un patient déjà sorti.'],
            ]);
        }

        if ($hospitalization->bed_uuid === $dto->to_bed_uuid) {
            throw ValidationException::withMessages([
                'to_bed_uuid' => ['Le patient est déjà dans ce lit.'],
            ]);
        }

        // Vérifie que le nouveau lit est disponible
        $toBed = $this->bedRepository->findByUuid($dto->to_bed_uuid);
        if (! $toBed || ! $toBed->isAvailable()) {
            throw ValidationException::withMessages([
                'to_bed_uuid' => ['Le lit de destination n'est pas disponible.'],
            ]);
        }

        $fromBedUuid = $hospitalization->bed_uuid;

        // Met à jour l'hospitalisation
        $this->hospitalizationRepository->update($hospitalizationUuid, [
            'bed_uuid' => $dto->to_bed_uuid,
            'status' => 'transferred',
        ]);

        // Libère l'ancien lit
        $this->bedRepository->updateStatus($fromBedUuid, 'available');

        // Occupe le nouveau lit
        $this->bedRepository->updateStatus($dto->to_bed_uuid, 'occupied');

        // Libère l'ancienne assignation
        $this->bedAssignmentRepository->releaseCurrent($hospitalizationUuid);

        // Crée la nouvelle assignation
        $this->bedAssignmentRepository->create([
            'hospitalization_uuid' => $hospitalizationUuid,
            'bed_uuid' => $dto->to_bed_uuid,
            'assigned_at' => now(),
            'assigned_by_uuid' => $transferredByUuid,
        ]);

        return $hospitalization->fresh();
    }
}
