<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Hospitalization\Actions\AdmitPatientAction;
use App\Modules\Hospitalization\Actions\DischargePatientAction;
use App\Modules\Hospitalization\Actions\TransferPatientAction;
use App\Modules\Hospitalization\DTOs\CreateHospitalizationDTO;
use App\Modules\Hospitalization\DTOs\DischargeHospitalizationDTO;
use App\Modules\Hospitalization\DTOs\TransferHospitalizationDTO;
use App\Modules\Hospitalization\Repositories\HospitalizationRepositoryInterface;
use App\Modules\Hospitalization\Repositories\BedRepositoryInterface;
use App\Modules\Hospitalization\Requests\StoreHospitalizationRequest;
use App\Modules\Hospitalization\Requests\DischargeHospitalizationRequest;
use App\Modules\Hospitalization\Requests\TransferHospitalizationRequest;
use App\Modules\Hospitalization\Resources\HospitalizationResource;
use App\Modules\Hospitalization\Resources\HospitalizationDetailResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HospitalizationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected HospitalizationRepositoryInterface $repository,
        protected BedRepositoryInterface $bedRepository
    ) {
        $this->authorizeResource(
            \App\Modules\Hospitalization\Models\Hospitalization::class,
            'hospitalization',
            ['except' => ['index', 'store', 'discharge', 'transfer']]
        );
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Modules\Hospitalization\Models\Hospitalization::class);
        return HospitalizationResource::collection($this->repository->getAll($request->all()));
    }

    public function store(StoreHospitalizationRequest $request, AdmitPatientAction $action)
    {
        $this->authorize('create', \App\Modules\Hospitalization\Models\Hospitalization::class);
        $dto = CreateHospitalizationDTO::fromRequest($request);
        $hosp = $action->execute($dto);
        return (new HospitalizationResource($hosp))->response()->setStatusCode(201);
    }

    public function show(string $uuid)
    {
        $hosp = $this->repository->findByUuid($uuid);
        if (! $hosp) return response()->json(['message' => 'Hospitalization not found'], 404);
        $this->authorize('view', $hosp);
        return new HospitalizationDetailResource($hosp);
    }

    public function update(StoreHospitalizationRequest $request, string $uuid)
    {
        $hosp = $this->repository->findByUuid($uuid);
        if (! $hosp) return response()->json(['message' => 'Hospitalization not found'], 404);
        $this->authorize('update', $hosp);
        $this->repository->update($uuid, $request->validated());
        return new HospitalizationResource($hosp->fresh());
    }

    public function destroy(string $uuid)
    {
        $hosp = $this->repository->findByUuid($uuid);
        if (! $hosp) return response()->json(['message' => 'Hospitalization not found'], 404);
        $this->authorize('delete', $hosp);

        if ($hosp->isActive()) {
            $this->bedRepository->updateStatus($hosp->bed_uuid, 'available');
        }

        $this->repository->delete($uuid);
        return response()->json(['message' => 'Hospitalization deleted'], 200);
    }

    public function discharge(string $uuid, DischargeHospitalizationRequest $request, DischargePatientAction $action)
    {
        $hosp = $this->repository->findByUuid($uuid);
        if (! $hosp) return response()->json(['message' => 'Hospitalization not found'], 404);
        $this->authorize('discharge', $hosp);
        $dto = DischargeHospitalizationDTO::fromArray($request->validated());
        $result = $action->execute($uuid, $dto);
        return new HospitalizationResource($result);
    }

    public function transfer(string $uuid, TransferHospitalizationRequest $request, TransferPatientAction $action)
    {
        $hosp = $this->repository->findByUuid($uuid);
        if (! $hosp) return response()->json(['message' => 'Hospitalization not found'], 404);
        $this->authorize('transfer', $hosp);
        $dto = TransferHospitalizationDTO::fromArray($request->validated());
        $user = $request->user();
        $result = $action->execute($uuid, $dto, $user->uuid);
        return new HospitalizationResource($result);
    }
}
