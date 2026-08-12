<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Policies;

use App\Modules\Foundation\Models\User;
use App\Modules\Hospitalization\Models\Room;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('rooms.view');
    }

    public function view(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('rooms.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('rooms.create');
    }

    public function update(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('rooms.update');
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->hasRole('admin');
    }
}
