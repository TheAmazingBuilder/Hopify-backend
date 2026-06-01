<?php

namespace App\Modules\Patient\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\HR\Models\Employee;
use App\Shared\Enums\AllergySeverity;

class Allergy extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'patient_uuid',
        'allergen',
        'type',
        'severity',
        'reaction',
        'diagnosed_at',
        'is_active',
        
    ];

    public function patient():BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_uuid', 'uuid');
    }

    protected $casts = [
        'severity'  => AllergySeverity::class,
        'noted_at'  => 'date',
        'is_active' => 'boolean',
    ];

    public function reportedBy(): BelongsTo 
    { 
        return $this->belongsTo(Employee::class, 'reported_by'); 
    }

    public function scopeActive(Builder $q): Builder   
    { 
        return $q->where('is_active', true);
    }
    
    public function scopeCritical(Builder $q): Builder 
    {
        return $q->where('severity', AllergySeverity::LifeThreatening->value);
    }
    public function isCritical(): bool 
    {
         return $this->severity === AllergySeverity::LifeThreatening; 
    }
}
