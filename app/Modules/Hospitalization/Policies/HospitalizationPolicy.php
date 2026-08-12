<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Policies;

use App\Modules\Foundation\Models\User;
use App\Modules\Hospitalization\Models\Hospitalization;

class HospitalizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('hospitalizations.view');
    }

    public function view(User $user, Hospitalization $hospitalization): bool
    {
        return $user->hasPermissionTo('hospitalizations.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('hospitalizations.create');
    }

    public function update(User $user, Hospitalization $hospitalization): bool
    {
        return $user->hasPermissionTo('hospitalizations.update');
    }

    public function delete(User $user, Hospitalization $hospitalization): bool
    {
        return $user->hasRole('admin');
    }

    public function discharge(User $user, Hospitalization $hospitalization): bool
    {
        return $user->hasAnyRole(['doctor', 'nurse', 'admin'])
            && $hospitalization->isActive();
    }

    public function transfer(User $user, Hospitalization $hospitalization): bool
    {
        return $user->hasAnyRole(['doctor', 'nurse'])
            && $hospitalization->isActive();
    }
}
