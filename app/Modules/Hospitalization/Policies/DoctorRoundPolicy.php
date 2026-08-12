<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Policies;

use App\Modules\Foundation\Models\User;
use App\Modules\Hospitalization\Models\DoctorRound;

class DoctorRoundPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('doctor_rounds.view');
    }

    public function view(User $user, DoctorRound $round): bool
    {
        return $user->hasPermissionTo('doctor_rounds.view');
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['doctor', 'admin']);
    }

    public function update(User $user, DoctorRound $round): bool
    {
        return $user->uuid === $round->doctor_uuid || $user->hasRole('admin');
    }

    public function delete(User $user, DoctorRound $round): bool
    {
        return $user->hasRole('admin');
    }
}
