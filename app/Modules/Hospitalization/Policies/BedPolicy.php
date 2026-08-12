<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Policies;

use App\Modules\Foundation\Models\User;
use App\Modules\Hospitalization\Models\Bed;

class BedPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('beds.view');
    }

    public function view(User $user, Bed $bed): bool
    {
        return $user->hasPermissionTo('beds.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('beds.create');
    }

    public function update(User $user, Bed $bed): bool
    {
        return $user->hasPermissionTo('beds.update');
    }

    public function delete(User $user, Bed $bed): bool
    {
        return $user->hasRole('admin');
    }
}
