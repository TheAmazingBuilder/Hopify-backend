<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Scheduling\Actions\CreateDoctorScheduleAction;
use App\Modules\Scheduling\DTOs\CreateDoctorScheduleDTO;
use App\Modules\Scheduling\Repositories\DoctorScheduleRepositoryInterface;
use App\Modules\Scheduling\Requests\StoreDoctorScheduleRequest;
use App\Modules\Scheduling\Resources\DoctorScheduleResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DoctorScheduleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DoctorScheduleRepositoryInterface $repository
    ) {
        $this->authorizeResource(
            \App\Modules\Scheduling\Models\DoctorSchedule::class,
            'doctorSchedule',
            ['except' => ['index', 'store']]
        );
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Modules\Scheduling\Models\DoctorSchedule::class);
        return DoctorScheduleResource::collection($this->repository->getAll($request->all()));
    }

    public function store(StoreDoctorScheduleRequest $request, CreateDoctorScheduleAction $action)
    {
        $this->authorize('create', \App\Modules\Scheduling\Models\DoctorSchedule::class);
        $dto = CreateDoctorScheduleDTO::fromRequest($request);
        $schedule = $action->execute($dto);
        return (new DoctorScheduleResource($schedule))->response()->setStatusCode(201);
    }

    public function show(string $uuid)
    {
        $schedule = $this->repository->findByUuid($uuid);
        if (! $schedule) return response()->json(['message' => 'Schedule not found'], 404);
        $this->authorize('view', $schedule);
        return new DoctorScheduleResource($schedule);
    }

    public function update(StoreDoctorScheduleRequest $request, string $uuid)
    {
        $schedule = $this->repository->findByUuid($uuid);
        if (! $schedule) return response()->json(['message' => 'Schedule not found'], 404);
        $this->authorize('update', $schedule);
        $this->repository->update($uuid, $request->validated());
        return new DoctorScheduleResource($schedule->fresh());
    }

    public function destroy(string $uuid)
    {
        $schedule = $this->repository->findByUuid($uuid);
        if (! $schedule) return response()->json(['message' => 'Schedule not found'], 404);
        $this->authorize('delete', $schedule);
        $this->repository->delete($uuid);
        return response()->json(['message' => 'Schedule deleted'], 200);
    }

    public function byDoctor(string $doctorUuid)
    {
        $this->authorize('viewAny', \App\Modules\Scheduling\Models\DoctorSchedule::class);
        return DoctorScheduleResource::collection($this->repository->getByDoctor($doctorUuid));
    }
}
