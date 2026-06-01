<?php

namespace App\Modules\Patient\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatientInsurance extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'patient_uuid', 'insurance_company_id', 'policy_number', 'group_number',
        'subscriber_name', 'subscriber_relationship', 'valid_from', 'valid_until',
        'copay_amount', 'deductible_amount', 'is_primary', 'is_active',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_uuid', 'uuid');
    }


    protected $casts = [
        'valid_from'         => 'date',
        'valid_until'        => 'date',
        'copay_amount'       => 'decimal:2',
        'deductible_amount'  => 'decimal:2',
        'is_primary'         => 'boolean',
        'is_active'          => 'boolean',
    ];

    public function insuranceCompany(): BelongsTo 
    { 
        return $this->belongsTo(InsuranceCompany::class); 
    }

    public function scopeActive(Builder $q): Builder  
    { 
        return $q->where('is_active', true); 
    }
    public function scopePrimary(Builder $q): Builder 
    { 
        return $q->where('is_primary', true); 
    }
    public function isExpired(): bool 
    { 
        return $this->valid_until?->isPast() ?? false; 
    }
    public function isValid(): bool   
    { 
        return $this->is_active && !$this->isExpired(); 
    }
}
