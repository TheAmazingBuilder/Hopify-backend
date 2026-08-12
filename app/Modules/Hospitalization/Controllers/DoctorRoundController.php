<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Hospitalization\Actions\CreateDoctorRoundAction;
use App\Modules\Hospitalization\DTOs\CreateDoctorRoundDTO;
use App\Modules\Hospitalization\Repositories\DoctorRoundRepositoryInterface;
use App\Modules\Hospitalization\Requests\StoreDoctorRoundRequest;
use App\Modules\Hospitalization\Resources\DoctorRoundResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DoctorRoundController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DoctorRoundRepositoryInterface $repository
    ) {}

    public function index(string $hospitalizationUuid)
    {
        $this->authorize('viewAny', \App\Modules\Hospitalization\Models\DoctorRound::class);
        return DoctorRoundResource::collection(
            $this->repository->getByHospitalization($hospitalizationUuid)
        );
    }

    public function store(StoreDoctorRoundRequest $request, CreateDoctorRoundAction $action)
    {
        $this->authorize('create', \App\Modules\Hospitalization\Models\DoctorRound::class);
        $round = $action->execute(CreateDoctorRoundDTO::fromRequest($request));
        return (new DoctorRoundResource($round))->response()->setStatusCode(201);
    }

    public function show(string $uuid)
    {
        $round = $this->repository->findByUuid($uuid);
        if (! $round) return response()->json(['message' => 'Round not found'], 404);
        $this->authorize('view', $round);
        return new DoctorRoundResource($round);
    }

    public function update(StoreDoctorRoundRequest $request, string $uuid)
    {
        $round = $this->repository->findByUuid($uuid);
        if (! $round) return response()->json(['message' => 'Round not found'], 404);
        $this->authorize('update', $round);
        $this->repository->update($uuid, $request->validated());
        return new DoctorRoundResource($round->fresh());
    }

    public function destroy(string $uuid)
    {
        $round = $this->repository->findByUuid($uuid);
        if (! $round) return response()->json(['message' => 'Round not found'], 404);
        $this->authorize('delete', $round);
        $this->repository->delete($uuid);
        return response()->json(['message' => 'Round deleted'], 200);
    }
}
