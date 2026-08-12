<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Policies;

use App\Modules\Clinical\Models\Consultation;
use App\Modules\Foundation\Models\User;
use App\Modules\Patient\Models\Patient;
use App\Shared\Enums\EmployeeRoleType;
use App\Shared\Traits\TenantAwarePolicy;

class ConsultationPolicy
{
    use TenantAwarePolicy;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(
            ClinicalPermissions::CONSULTATIONS_VIEW
        );
    }

    public function view(
        User $user,
        Consultation $consultation
    ): bool {
        return $user->hasPermissionTo(
            ClinicalPermissions::CONSULTATIONS_VIEW
        )
            && $this->belongsToSameTenant($user, $consultation);
    }

    public function create(User $user): bool
    {
        if (! $user->hasPermissionTo(
            ClinicalPermissions::CONSULTATIONS_CREATE

        )) {
            return false;
        }

        return $this->isActiveDoctor($user);
    }

    public function update(
        User $user,
        Consultation $consultation
    ): bool {
        if (! $user->hasPermissionTo(
            ClinicalPermissions::CONSULTATIONS_UPDATE

        )) {
            return false;
        }

        if (! $this->belongsToSameTenant($user, $consultation)) {
            return false;
        }

        if ($consultation->is_finalized) {
            return false;
        }

        return $this->isAssignedDoctor(
            $user,
            $consultation
        );
    }

    public function finalize(
        User $user,
        Consultation $consultation
    ): bool {
        if (! $user->hasPermissionTo(
            ClinicalPermissions::CONSULTATIONS_FINALIZE
        )) {
            return false;
        }

        if (! $this->belongsToSameTenant($user, $consultation)) {
            return false;
        }

        if ($consultation->is_finalized) {
            return false;
        }

        return $this->isAssignedDoctor(
            $user,
            $consultation
        );
    }

    private function isActiveDoctor(User $user): bool
    {
        $employee = $user->employee;

        if (! $employee) {
            return false;
        }

        return $employee->is_active
            && $employee->role_type
                === EmployeeRoleType::Doctor;
    }

    private function isAssignedDoctor(
        User $user,
        Consultation $consultation
    ): bool {
        $employee = $user->employee;

        if (! $employee || ! $employee->is_active) {
            return false;
        }

        return $employee->role_type
                === EmployeeRoleType::Doctor
            && (string) $employee->uuid
                === (string) $consultation->doctor_uuid;
    }

    /** 
    private function belongsToSameTenant(
        User $user,
        Consultation $consultation
    ): bool {
        if (tenancy()->initialized) {
            return $consultation->tenant_id === tenant('id');
        }

        return $user->tenant_id === $consultation->tenant_id;
    }**/
}