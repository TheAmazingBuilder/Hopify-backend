<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Hospitalization\Actions\CreateBedAction;
use App\Modules\Hospitalization\DTOs\CreateBedDTO;
use App\Modules\Hospitalization\Repositories\BedRepositoryInterface;
use App\Modules\Hospitalization\Requests\StoreBedRequest;
use App\Modules\Hospitalization\Resources\BedResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BedController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected BedRepositoryInterface $repository
    ) {
        $this->authorizeResource(
            \App\Modules\Hospitalization\Models\Bed::class,
            'bed',
            ['except' => ['index', 'store']]
        );
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Modules\Hospitalization\Models\Bed::class);
        return BedResource::collection($this->repository->getAll($request->all()));
    }

    public function store(StoreBedRequest $request, CreateBedAction $action)
    {
        $this->authorize('create', \App\Modules\Hospitalization\Models\Bed::class);
        $bed = $action->execute(CreateBedDTO::fromRequest($request));
        return (new BedResource($bed))->response()->setStatusCode(201);
    }

    public function show(string $uuid)
    {
        $bed = $this->repository->findByUuid($uuid);
        if (! $bed) return response()->json(['message' => 'Bed not found'], 404);
        $this->authorize('view', $bed);
        return new BedResource($bed);
    }

    public function update(StoreBedRequest $request, string $uuid)
    {
        $bed = $this->repository->findByUuid($uuid);
        if (! $bed) return response()->json(['message' => 'Bed not found'], 404);
        $this->authorize('update', $bed);
        $this->repository->update($uuid, $request->validated());
        return new BedResource($bed->fresh());
    }

    public function destroy(string $uuid)
    {
        $bed = $this->repository->findByUuid($uuid);
        if (! $bed) return response()->json(['message' => 'Bed not found'], 404);
        $this->authorize('delete', $bed);
        $this->repository->delete($uuid);
        return response()->json(['message' => 'Bed deleted'], 200);
    }

    public function availableByRoom(string $roomUuid)
    {
        $this->authorize('viewAny', \App\Modules\Hospitalization\Models\Bed::class);
        return BedResource::collection($this->repository->getAvailableByRoom($roomUuid));
    }
}
