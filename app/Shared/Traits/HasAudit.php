<?php

namespace App\Shared\Traits;

use App\Modules\Foundation\Models\AuditLog;

trait HasAudit
{
    protected static function bootHasAudit(): void
    {
        static::created(fn($m)  => static::writeAudit('created',  $m, [], $m->getAttributes()));
        static::updated(fn($m)  => static::writeAudit('updated',  $m, $m->getOriginal(), $m->getChanges()));
        static::deleted(fn($m)  => static::writeAudit('deleted',  $m, $m->getOriginal(), []));
        static::restored(fn($m) => static::writeAudit('restored', $m, [], $m->getAttributes()));
    }

    protected static function writeAudit(string $action, $model, array $old, array $new): void
    {
        // Exclure les champs non sensibles des audits
        $excluded = ['updated_at', 'created_at', 'deleted_at'];
        $old = array_diff_key($old, array_flip($excluded));
        $new = array_diff_key($new, array_flip($excluded));

        AuditLog::create([
            'tenant_id'  => $model->tenant_id ?? auth()->user()?->tenant_id,
            'user_id'    => auth()->id(),
            'action'     => strtolower(class_basename($model)) . '.' . $action,
            'model_type' => get_class($model),
            'model_id'   => (string) $model->getKey(),
            'old_values' => empty($old) ? null : $old,
            'new_values' => empty($new) ? null : $new,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
