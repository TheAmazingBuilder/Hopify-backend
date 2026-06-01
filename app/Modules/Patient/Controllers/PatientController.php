<?php

namespace App\Modules\Patient\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\Patient\Repositories\PatientRepositoryInterface;
use App\Modules\Patient\Actions\CreatePatientAction;
use App\Modules\Patient\DTOs\CreatePatientDTO;
use App\Modules\Patient\Requests\StorePatientRequest;
use App\Modules\Patient\Resources\PatientResource;
use App\Modules\Patient\Resources\PatientCollection;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(
        protected PatientRepositoryInterface $repository
    ) {}

    public function index(Request $request)
    {
        $patients = $this->repository->getAll($request->all());
        return new PatientCollection($patients);
    }

    public function store(StorePatientRequest $request, CreatePatientAction $action)
    {
        $dto = CreatePatientDTO::fromRequest($request);
        $patient = $action->execute($dto);
        return new PatientResource($patient);
    }

    public function show(string $uuid)
    {
        $patient = $this->repository->findByUuid($uuid);
        
        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        return new PatientResource($patient);
    }
}
