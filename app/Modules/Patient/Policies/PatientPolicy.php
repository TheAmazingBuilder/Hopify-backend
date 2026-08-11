<?php

declare(strict_types=1);

namespace App\Modules\Patient\Policies;

use App\Modules\Foundation\Models\User;
use App\Modules\Patient\Models\Patient;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('patients.view');
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->hasPermissionTo('patients.view')
            && $this->belongsToSameTenant($user, $patient);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('patients.create');
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->hasPermissionTo('patients.update')
            && $this->belongsToSameTenant($user, $patient);
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->hasRole('admin')
            && $this->belongsToSameTenant($user, $patient);
    }

    public function viewMedicalRecord(User $user, Patient $patient): bool
    {
        return $user->hasAnyRole(['doctor', 'nurse'])
            && $this->belongsToSameTenant($user, $patient);
    }

    private function belongsToSameTenant(User $user, Patient $patient): bool
    {
        if (tenancy()->initialized) {
            return $patient->tenant_id === tenant('id');
        }
        return $user->tenant_id === $patient->tenant_id;
    }
}




/**
 namespace App\Modules\Patient\Policies;

use App\Modules\Patient\Models\Patient;
use App\Models\User;

class PatientPolicy
{
     public function view(User $user, Patient $patient): bool {
        return $user->hasPermission('patients.view');
    }
    public function viewMedicalRecord(User $user, Patient $patient): bool {
        return $user->hasAnyRole(['doctor', 'nurse'])
            && $user->tenant_id === $patient->tenant_id;
    }
    public function delete(User $user, Patient $patient): bool {
        return $user->hasRole('admin'); // jamais suppression physique
    }
}
 */