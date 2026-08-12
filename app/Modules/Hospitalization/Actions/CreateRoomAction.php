<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Actions;

use App\Modules\Hospitalization\DTOs\CreateRoomDTO;
use App\Modules\Hospitalization\Models\Room;
use App\Modules\Hospitalization\Repositories\RoomRepositoryInterface;

class CreateRoomAction
{
    public function __construct(
        protected RoomRepositoryInterface $repository
    ) {}

    public function execute(CreateRoomDTO $dto): Room
    {
        return $this->repository->create($dto->toArray());
    }
}
