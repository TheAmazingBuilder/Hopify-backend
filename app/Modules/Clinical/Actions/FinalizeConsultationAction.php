<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\DTOs\FinalizeConsultationDTO;
use App\Modules\Clinical\Models\Consultation;
use App\Modules\Clinical\Repositories\ConsultationRepositoryInterface;
use App\Modules\Foundation\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class FinalizeConsultationAction
{
    public function __construct(
        private ConsultationRepositoryInterface $repository,
    ) {
    }

    public function execute(
        FinalizeConsultationDTO $dto
    ): Consultation {
        $consultation = $this->repository->findByUuid(
            $dto->consultationUuid,
            false
        );

        if (! $consultation) {
            throw ValidationException::withMessages([
                'consultation_uuid' => 'The specified consultation does not exist.',
            ]);
        }

        $user = User::query()
            ->find($dto->finalizedByUuid);

        if (! $user) {
            throw ValidationException::withMessages([
                'finalized_by_uuid' => 'The specified user does not exist.',
            ]);
        }

        if ($consultation->is_finalized) {
            throw ValidationException::withMessages([
                'consultation_uuid' => 'The consultation is already finalized.',
            ]);
        }

        return DB::transaction(
            function () use ($consultation, $dto): Consultation {
                $consultation->update([
                    'is_finalized' => true,
                    'finalized_at' => now(),
                    'finalized_by_uuid' => $dto->finalizedByUuid,
                ]);

                return $consultation->fresh([
                    'patient',
                    'doctor',
                    'appointment',
                    'hospitalization',
                    'vitalSigns',
                    'diagnoses.icdCode',
                    'prescriptions.items.medication',
                    'labOrders.items.labTest',
                    'labOrders.items.result',
                    'imagingOrders.result',
                ]);
            }
        );
    }
}