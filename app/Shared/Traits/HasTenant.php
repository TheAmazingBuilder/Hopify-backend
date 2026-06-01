<?php

namespace App\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Foundation\Models\Tenant;

trait HasTenant
{
    protected static function bootHasTenant(): void
    {
        // Auto-assign tenant_id on creation
        static::creating(function ($model) {
            if (auth()->check() && empty($model->tenant_id)) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        // Global scope: toujours filtrer par tenant courant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where(
                    $builder->getModel()->getTable() . '.tenant_id',
                    auth()->user()->tenant_id
                );
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** Bypass du global scope pour les super-admins */
    public function scopeAcrossTenants(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}
