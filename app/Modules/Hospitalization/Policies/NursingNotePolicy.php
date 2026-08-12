<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Policies;

use App\Modules\Foundation\Models\User;
use App\Modules\Hospitalization\Models\NursingNote;

class NursingNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('nursing_notes.view');
    }

    public function view(User $user, NursingNote $note): bool
    {
        return $user->hasPermissionTo('nursing_notes.view');
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['nurse', 'doctor', 'admin']);
    }

    public function update(User $user, NursingNote $note): bool
    {
        return $user->uuid === $note->nurse_uuid || $user->hasRole('admin');
    }

    public function delete(User $user, NursingNote $note): bool
    {
        return $user->hasRole('admin');
    }
}
