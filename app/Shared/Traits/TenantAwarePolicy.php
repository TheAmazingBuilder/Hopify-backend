<?php

declare(strict_types=1);

namespace App\Shared\Traits;

use App\Modules\Foundation\Models\User;
use Illuminate\Database\Eloquent\Model;

trait TenantAwarePolicy
{
    protected function belongsToSameTenant(
        User $user,
        Model $model
    ): bool {
        /*
         * En mode tenancy, la base courante est déjà isolée
         * au niveau du tenant.
         *
         * Si le modèle possède malgré tout un tenant_id,
         * on vérifie également sa cohérence.
         */
        if (tenancy()->initialized) {
            $modelTenantId = $model->getAttribute('tenant_id');

            if ($modelTenantId !== null) {
                return (string) $modelTenantId
                    === (string) tenant('id');
            }

            return true;
        }

        /*
         * Hors contexte tenancy, on ne permet pas d'accéder
         * à une ressource tenant-scoped sans une correspondance
         * explicite de tenant_id.
         */
        $userTenantId = $user->getAttribute('tenant_id');
        $modelTenantId = $model->getAttribute('tenant_id');

        return $userTenantId !== null
            && $modelTenantId !== null
            && (string) $userTenantId === (string) $modelTenantId;
    }
}