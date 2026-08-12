<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Actions;

use App\Modules\Hospitalization\DTOs\CreateNursingNoteDTO;
use App\Modules\Hospitalization\Models\NursingNote;
use App\Modules\Hospitalization\Repositories\NursingNoteRepositoryInterface;

class CreateNursingNoteAction
{
    public function __construct(
        protected NursingNoteRepositoryInterface $repository
    ) {}

    public function execute(CreateNursingNoteDTO $dto): NursingNote
    {
        return $this->repository->create($dto->toArray());
    }
}
