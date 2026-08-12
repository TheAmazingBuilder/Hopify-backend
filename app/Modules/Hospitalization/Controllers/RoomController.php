<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Hospitalization\Actions\CreateRoomAction;
use App\Modules\Hospitalization\DTOs\CreateRoomDTO;
use App\Modules\Hospitalization\Repositories\RoomRepositoryInterface;
use App\Modules\Hospitalization\Requests\StoreRoomRequest;
use App\Modules\Hospitalization\Resources\RoomResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoomController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected RoomRepositoryInterface $repository
    ) {
        $this->authorizeResource(
            \App\Modules\Hospitalization\Models\Room::class,
            'room',
            ['except' => ['index', 'store']]
        );
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Modules\Hospitalization\Models\Room::class);
        return RoomResource::collection($this->repository->getAll($request->all()));
    }

    public function store(StoreRoomRequest $request, CreateRoomAction $action)
    {
        $this->authorize('create', \App\Modules\Hospitalization\Models\Room::class);
        $room = $action->execute(CreateRoomDTO::fromRequest($request));
        return (new RoomResource($room))->response()->setStatusCode(201);
    }

    public function show(string $uuid)
    {
        $room = $this->repository->findByUuid($uuid);
        if (! $room) return response()->json(['message' => 'Room not found'], 404);
        $this->authorize('view', $room);
        return new RoomResource($room);
    }

    public function update(StoreRoomRequest $request, string $uuid)
    {
        $room = $this->repository->findByUuid($uuid);
        if (! $room) return response()->json(['message' => 'Room not found'], 404);
        $this->authorize('update', $room);
        $this->repository->update($uuid, $request->validated());
        return new RoomResource($room->fresh());
    }

    public function destroy(string $uuid)
    {
        $room = $this->repository->findByUuid($uuid);
        if (! $room) return response()->json(['message' => 'Room not found'], 404);
        $this->authorize('delete', $room);
        $this->repository->delete($uuid);
        return response()->json(['message' => 'Room deleted'], 200);
    }
}
