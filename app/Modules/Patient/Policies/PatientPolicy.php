<?php

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
