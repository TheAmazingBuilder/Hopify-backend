<?php

namespace App\Modules\Foundation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_uuid',
        'action',
        'model_type',
        'model_uuid',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    /**
     * Enregistre une action d'audit.
     */
    public static function record(string $action, Model $model, array $payload = []): self
    {
        return self::create([
            'user_uuid'  => auth()->id(),
            'action'     => $action,
            'model_type' => get_class($model),
            'model_uuid' => (string) $model->getKey(),
            'old_values' => $payload['old'] ?? null,
            'new_values' => $payload['changes'] ?? $model->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Relation avec l'utilisateur (Super-Admin ou User Tenant)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
