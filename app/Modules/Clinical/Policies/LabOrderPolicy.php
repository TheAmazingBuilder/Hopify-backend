<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Policies;

use App\Modules\Clinical\Models\LabOrder;
use App\Modules\Clinical\Models\LabResult;
use App\Modules\Foundation\Models\User;
use App\Shared\Enums\EmployeeRoleType;
use App\Shared\Enums\LabOrderStatus;
use App\Shared\Traits\TenantAwarePolicy;

class LabOrderPolicy
{
    use TenantAwarePolicy;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(
            ClinicalPermissions::LABS_VIEW
        );
    }

    public function view(
        User $user,
        LabOrder $order
    ): bool {
        return $user->hasPermissionTo(
            ClinicalPermissions::LABS_VIEW
        )
            && $this->belongsToSameTenant($user, $order);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(
            ClinicalPermissions::LABS_CREATE
        )
            && $this->isActiveClinicalEmployee($user);
    }

    public function recordResult(
        User $user,
        LabOrder $order
    ): bool {
        if (! $user->hasPermissionTo(
            ClinicalPermissions::LAB_RESULTS_RECORD
        )) {
            return false;
        }

        if (! $this->belongsToSameTenant($user, $order)) {
            return false;
        }

        if ($order->status === LabOrderStatus::Cancelled) {
            return false;
        }

        return $this->isActiveLaboratoryEmployee($user);
    }

    public function validateResult(
        User $user,
        LabResult $result
    ): bool {
        if (! $user->hasPermissionTo(
            ClinicalPermissions::LAB_RESULTS_VALIDATE

        )) {
            return false;
        }

        $order = $result->labOrderItem?->labOrder;

        if (! $order) {
            return false;
        }

        if (! $this->belongsToSameTenant($user, $order)) {
            return false;
        }

        return $this->isActiveLaboratoryValidator($user);
    }

    private function isActiveClinicalEmployee(
        User $user
    ): bool {
        $employee = $user->employee;

        if (! $employee || ! $employee->is_active) {
            return false;
        }

        return in_array(
            $employee->role_type,
            [
                EmployeeRoleType::Doctor,
                EmployeeRoleType::Nurse,
            ],
            true
        );
    }

    private function isActiveLaboratoryEmployee(
        User $user
    ): bool {
        $employee = $user->employee;

        return $employee !== null
            && $employee->is_active
            && $employee->role_type
                === EmployeeRoleType::Technician;
    }

    private function isActiveLaboratoryValidator(
        User $user
    ): bool {
        $employee = $user->employee;

        return $employee !== null
            && $employee->is_active
            && in_array(
                $employee->role_type,
                [
                    EmployeeRoleType::Doctor,
                    EmployeeRoleType::Technician,
                ],
                true
            );
    }

    /** 
    private function belongsToSameTenant(
        User $user,
        LabOrder $order
    ): bool {
        if (tenancy()->initialized) {
            return $order->tenant_id === tenant('id');
        }

        return $user->tenant_id === $order->tenant_id;
    }**/
}