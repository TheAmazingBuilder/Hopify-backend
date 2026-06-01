<?php

namespace App\Modules\Scheduling\Repositories;

use App\Modules\Scheduling\Models\Appointment;
use Illuminate\Support\Collection;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function getDoctorAppointments(string $doctorUuid, string $date): Collection
    {
        return Appointment::where('doctor_uuid', $doctorUuid)
            ->whereDate('start_time', $date)
            ->orderBy('start_time')
            ->get();
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function updateStatus(string $uuid, string $status): bool
    {
        return Appointment::where('uuid', $uuid)->update(['status' => $status]);
    }
}
