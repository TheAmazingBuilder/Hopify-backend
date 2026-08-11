<?php

declare(strict_types=1);

namespace App\Modules\Patient\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Patient\Repositories\PatientRepositoryInterface;
use App\Modules\Patient\Actions\CreatePatientAction;
use App\Modules\Patient\DTOs\CreatePatientDTO;
use App\Modules\Patient\Requests\StorePatientRequest;
use App\Modules\Patient\Resources\PatientResource;
use App\Modules\Patient\Resources\PatientCollection;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PatientController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected PatientRepositoryInterface $repository
    ) {
        $this->authorizeResource(
            \App\Modules\Patient\Models\Patient::class,
            'patient',
            ['except' => ['index', 'store']]
        );
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Modules\Patient\Models\Patient::class);
        $patients = $this->repository->getAll($request->all());
        return new PatientCollection($patients);
    }

    public function store(StorePatientRequest $request, CreatePatientAction $action)
    {
        $this->authorize('create', \App\Modules\Patient\Models\Patient::class);
        $dto = CreatePatientDTO::fromRequest($request);
        $patient = $action->execute($dto);
        return new PatientResource($patient);
    }

    public function show(string $uuid)
    {
        $patient = $this->repository->findByUuid($uuid);
        if (! $patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }
        return new PatientResource($patient);
    }

    public function update(StorePatientRequest $request, string $uuid)
    {
        $patient = $this->repository->findByUuid($uuid);
        if (! $patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }
        $this->repository->update($uuid, $request->validated());
        return new PatientResource($patient->fresh());
    }

    public function destroy(string $uuid)
    {
        $patient = $this->repository->findByUuid($uuid);
        if (! $patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }
        $this->repository->delete($uuid);
        return response()->json(['message' => 'Patient deleted'], 200);
    }
}


/**
 * 
 * <?php

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

 */





