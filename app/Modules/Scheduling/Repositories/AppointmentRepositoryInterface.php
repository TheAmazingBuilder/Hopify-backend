<?php

namespace App\Modules\Scheduling\Repositories;

use App\Modules\Scheduling\Models\Appointment;
use Illuminate\Support\Collection;

interface AppointmentRepositoryInterface
{
    public function getDoctorAppointments(string $doctorUuid, string $date): Collection;
    public function create(array $data): Appointment;
    public function updateStatus(string $uuid, string $status): bool;
}
