<?php

namespace App\Modules\Patient\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Shared\Enums\AntecedentType;
use Illuminate\Database\Eloquent\Builder;


class MedicalAntecedent extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'patient_uuid',
        'type',
        'condition',
        'description',
        'diagnosed_at',
        'status',
        'notes',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_uuid', 'uuid');
    }

     protected $casts = [
        'type'         => AntecedentType::class,
        'diagnosed_at' => 'date',
        'resolved_at'  => 'date',
        'is_active'    => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder     
    {
         return $q->where('is_active', true); 
    }
    
    public function scopeByType(Builder $q, AntecedentType $type): Builder 
    {
        return $q->where('type', $type->value);
    }

    public function isResolved(): bool 
    {
         return $this->resolved_at !== null; 
    }
}
