<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\Models\Prescription;
use App\Modules\Clinical\Repositories\PrescriptionRepositoryInterface;
use App\Shared\Enums\PrescriptionStatus;
use Illuminate\Validation\ValidationException;

final class CancelPrescriptionAction
{
    public function __construct(
        private PrescriptionRepositoryInterface $repository,
    ) {
    }

    public function execute(
        string $prescriptionUuid
    ): Prescription {
        $prescription = $this->repository->findByUuid(
            $prescriptionUuid,
            true
        );

        if (! $prescription) {
            throw ValidationException::withMessages([
                'prescription_uuid' =>
                    'The specified prescription does not exist.',
            ]);
        }

        if ($prescription->status !== PrescriptionStatus::Active) {
            throw ValidationException::withMessages([
                'prescription_uuid' =>
                    'Only an active prescription can be cancelled.',
            ]);
        }

        $prescription->cancel();

        return $prescription->fresh([
            'patient',
            'doctor',
            'consultation',
            'items.medication.category',
        ]);
    }
}