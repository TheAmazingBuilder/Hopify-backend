<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Actions;

use App\Modules\Hospitalization\DTOs\CreateBedDTO;
use App\Modules\Hospitalization\Models\Bed;
use App\Modules\Hospitalization\Repositories\BedRepositoryInterface;

class CreateBedAction
{
    public function __construct(
        protected BedRepositoryInterface $repository
    ) {}

    public function execute(CreateBedDTO $dto): Bed
    {
        return $this->repository->create($dto->toArray());
    }
}
