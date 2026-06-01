<?php

namespace App\Modules\Scheduling\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Scheduling\Actions\CreateAppointmentAction;
use App\Modules\Scheduling\Actions\CancelAppointmentAction;
use App\Modules\Scheduling\DTOs\CreateAppointmentDTO;
use App\Modules\Scheduling\Requests\StoreAppointmentRequest;
use App\Modules\Scheduling\Resources\AppointmentResource;
use App\Modules\Scheduling\Repositories\AppointmentRepositoryInterface;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentRepositoryInterface $repository
    ) {}

    public function index(Request $request)
    {
        $appointments = $this->repository->getDoctorAppointments(
            $request->query('doctor_uuid'),
            $request->query('date', date('Y-m-d'))
        );

        return AppointmentResource::collection($appointments);
    }

    public function store(StoreAppointmentRequest $request, CreateAppointmentAction $action)
    {
        $dto = CreateAppointmentDTO::fromRequest($request);
        $appointment = $action->execute($dto);

        return new AppointmentResource($appointment);
    }

    public function cancel(Request $request, string $uuid, CancelAppointmentAction $action)
    {
        $request->validate(['reason' => 'required|string']);
        
        $appointment = $action->execute($uuid, $request->reason);

        return new AppointmentResource($appointment);
    }
}
