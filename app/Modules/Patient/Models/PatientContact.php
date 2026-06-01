<?php

namespace App\Modules\Patient\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatientContact extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'patient_uuid',
        'name',
        'relationship',
        'phone',
        'email',
        'is_emergency_contact',
        'is_legal_guardian',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_uuid', 'uuid');
    }

     protected $casts = [
        'is_emergency_contact' => 'boolean',
        'is_legal_guardian'    => 'boolean',
    ];

    // ── Accessors ─────────────────────────────────────────────────────────
    public function getIsEmergencyContactAttribute(): bool {
        return (bool) ($this->attributes['is_emergency_contact'] ?? false);
    }

    public function getIsLegalGuardianAttribute(): bool {
        return (bool) ($this->attributes['is_legal_guardian'] ?? false);
    }
}
