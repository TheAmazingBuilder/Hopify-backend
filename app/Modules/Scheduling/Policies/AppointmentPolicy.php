<?php

declare(strict_types=1);

namespace App\Modules\Scheduling\Policies;

use App\Modules\Foundation\Models\User;
use App\Modules\Scheduling\Models\Appointment;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('appointments.view');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->hasPermissionTo('appointments.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('appointments.create');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->hasPermissionTo('appointments.update')
            && ! $appointment->isCancelled();
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('admin');
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return $user->hasPermissionTo('appointments.cancel')
            && ! $appointment->isCancelled()
            && $appointment->status !== 'completed';
    }

    public function confirm(User $user, Appointment $appointment): bool
    {
        return $user->hasAnyRole(['doctor', 'nurse', 'receptionist', 'admin'])
            && $appointment->status === 'pending';
    }

    public function complete(User $user, Appointment $appointment): bool
    {
        return $user->hasAnyRole(['doctor', 'nurse', 'admin'])
            && $appointment->status === 'confirmed';
    }
}
