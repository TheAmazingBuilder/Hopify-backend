<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Actions;

use App\Modules\Hospitalization\DTOs\CreateDoctorRoundDTO;
use App\Modules\Hospitalization\Models\DoctorRound;
use App\Modules\Hospitalization\Repositories\DoctorRoundRepositoryInterface;

class CreateDoctorRoundAction
{
    public function __construct(
        protected DoctorRoundRepositoryInterface $repository
    ) {}

    public function execute(CreateDoctorRoundDTO $dto): DoctorRound
    {
        return $this->repository->create($dto->toArray());
    }
}
