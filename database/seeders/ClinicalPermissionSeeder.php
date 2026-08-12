<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Shared\Support\Permissions\ClinicalPermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClinicalPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ClinicalPermissions::all();

        foreach ($permissions as $permission) {
            Permission::findOrCreate(
                $permission,
                'web'
            );
        }

        /*
         * Ne pas attribuer aveuglément les permissions ici
         * si les rôles n'existent pas encore dans le projet.
         *
         * Les associations rôle → permission devront être
         * centralisées dans ton futur seeder RBAC.
         */
        $this->assignIfRoleExists(
            'doctor',
            [
                ClinicalPermissions::CONSULTATIONS_VIEW,
                ClinicalPermissions::CONSULTATIONS_CREATE,
                ClinicalPermissions::CONSULTATIONS_UPDATE,
                ClinicalPermissions::CONSULTATIONS_FINALIZE,

                ClinicalPermissions::PRESCRIPTIONS_VIEW,
                ClinicalPermissions::PRESCRIPTIONS_CREATE,
                ClinicalPermissions::PRESCRIPTIONS_CANCEL,

                ClinicalPermissions::LABS_VIEW,
                ClinicalPermissions::LABS_CREATE,

                ClinicalPermissions::IMAGING_VIEW,
                ClinicalPermissions::IMAGING_CREATE,
            ]
        );

        $this->assignIfRoleExists(
            'pharmacist',
            [
                ClinicalPermissions::PRESCRIPTIONS_VIEW,
                ClinicalPermissions::PRESCRIPTIONS_DISPENSE,
            ]
        );

        $this->assignIfRoleExists(
            'technician',
            [
                ClinicalPermissions::LABS_VIEW,
                ClinicalPermissions::LAB_RESULTS_RECORD,
                ClinicalPermissions::IMAGING_VIEW,
            ]
        );
    }

    private function assignIfRoleExists(
        string $roleName,
        array $permissions
    ): void {
        $role = Role::query()
            ->where('name', $roleName)
            ->where('guard_name', 'web')
            ->first();

        if (! $role) {
            return;
        }

        $role->givePermissionTo($permissions);
    }
}