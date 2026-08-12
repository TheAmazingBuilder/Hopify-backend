<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Policies;

use App\Modules\Clinical\Models\Prescription;
use App\Modules\Foundation\Models\User;
use App\Shared\Enums\EmployeeRoleType;
use App\Shared\Enums\PrescriptionStatus;
use App\Shared\Traits\TenantAwarePolicy;

class PrescriptionPolicy
{
    use TenantAwarePolicy;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(
            ClinicalPermissions::PRESCRIPTIONS_VIEW
        );
    }

    public function view(
        User $user,
        Prescription $prescription
    ): bool {
        return $user->hasPermissionTo(
            ClinicalPermissions::PRESCRIPTIONS_VIEW
        )
            && $this->belongsToSameTenant(
                $user,
                $prescription
            );
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(
            ClinicalPermissions::PRESCRIPTIONS_CREATE
        )
            && $this->isActiveDoctor($user);
    }

    public function cancel(
        User $user,
        Prescription $prescription
    ): bool {
        if (! $user->hasPermissionTo(
            ClinicalPermissions::PRESCRIPTIONS_CANCEL
        )) {
            return false;
        }

        if (! $this->belongsToSameTenant(
            $user,
            $prescription
        )) {
            return false;
        }

        if ($prescription->status !== PrescriptionStatus::Active) {
            return false;
        }

        return $this->isPrescribingDoctor(
            $user,
            $prescription
        );
    }

    public function dispense(
    User $user,
    Prescription $prescription
    ): bool

    {
        return $user->hasPermissionTo(
            ClinicalPermissions::PRESCRIPTIONS_DISPENSE
        )
            && $this->belongsToSameTenant(
                $user,
                $prescription
            )
            && $this->isActivePharmacist($user);
    }

    private function isActiveDoctor(User $user): bool
    {
        $employee = $user->employee;

        return $employee !== null
            && $employee->is_active
            && $employee->role_type
                === EmployeeRoleType::Doctor;
    }

    private function isPrescribingDoctor(
        User $user,
        Prescription $prescription
    ): bool {
        $employee = $user->employee;

        if (! $employee || ! $employee->is_active) {
            return false;
        }

        return $employee->role_type
                === EmployeeRoleType::Doctor
            && (string) $employee->uuid
                === (string) $prescription->doctor_uuid;
    }

    private function isActivePharmacist(User $user): bool
    {
        $employee = $user->employee;

        return $employee !== null
            && $employee->is_active
            && $employee->role_type
                === EmployeeRoleType::Pharmacist;
    }

    /** 
    private function belongsToSameTenant(
        User $user,
        Prescription $prescription
    ): bool {
        if (tenancy()->initialized) {
            return $prescription->tenant_id === tenant('id');
        }

        return $user->tenant_id === $prescription->tenant_id;
    }*/
}