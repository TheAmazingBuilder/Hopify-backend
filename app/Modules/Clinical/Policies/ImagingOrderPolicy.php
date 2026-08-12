<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Policies;

use App\Modules\Clinical\Models\ImagingOrder;
use App\Modules\Clinical\Models\ImagingResult;
use App\Modules\Foundation\Models\User;
use App\Modules\Hr\Models\Employee;
use App\Shared\Enums\EmployeeRoleType;
use App\Shared\Enums\ImagingOrderStatus;
use App\Shared\Traits\TenantAwarePolicy;

class ImagingOrderPolicy
{
    use TenantAwarePolicy;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(
            ClinicalPermissions::IMAGING_VIEW

        );
    }

    public function view(
        User $user,
        ImagingOrder $order
    ): bool {
        return $user->hasPermissionTo(
            ClinicalPermissions::IMAGING_VIEW
        )
            && $this->belongsToSameTenant(
                $user,
                $order
            );
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(
            ClinicalPermissions::IMAGING_CREATE
        )
            && $this->isActiveDoctor($user);
    }

    public function recordResult(
        User $user,
        ImagingOrder $order
    ): bool {
        if (! $user->hasPermissionTo(
            ClinicalPermissions::IMAGING_RESULTS_RECORD
        )) {
            return false;
        }

        if (! $this->belongsToSameTenant($user, $order)) {
            return false;
        }

        if ($order->status === ImagingOrderStatus::Cancelled) {
            return false;
        }

        return $this->isActiveRadiologyEmployee($user);
    }

    public function reportResult(
        User $user,
        ImagingResult $result
    ): bool {
        if (! $user->hasPermissionTo(
            ClinicalPermissions::IMAGING_RESULTS_REPORT
        )) {
            return false;
        }

        $order = $result->imagingOrder;

        if (! $order) {
            return false;
        }

        if (! $this->belongsToSameTenant($user, $order)) {
            return false;
        }

        if (
            $result->radiologist_uuid === null
            || $user->employee === null
        ) {
            return false;
        }

        return (string) $result->radiologist_uuid
            === (string) $user->employee->uuid;
    }

    private function isActiveDoctor(User $user): bool
    {
        $employee = $user->employee;

        return $employee !== null
            && $employee->is_active
            && $employee->role_type
                === EmployeeRoleType::Doctor;
    }

    private function isActiveRadiologyEmployee(
        User $user
    ): bool {
        $employee = $user->employee;

        if (! $employee || ! $employee->is_active) {
            return false;
        }

        /*
         * Dans ton modèle actuel, un radiologue est représenté
         * par Employee(role_type = doctor) + specialization = radiology.
         */
        return $employee->role_type
                === EmployeeRoleType::Doctor
            && mb_strtolower(
                trim((string) $employee->specialization)
            ) === 'radiology';
    }

    /** 
    private function belongsToSameTenant(
        User $user,
        ImagingOrder $order
    ): bool {
        if (tenancy()->initialized) {
            return $order->tenant_id === tenant('id');
        }

        return $user->tenant_id === $order->tenant_id;
    }
        */
}