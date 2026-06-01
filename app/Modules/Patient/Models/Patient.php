<?php

namespace App\Modules\Patient\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Foundation\Models\Tenant;
use App\Shared\Enums\BloodTypeEnum;
use App\Shared\Enums\GenderEnum;
use App\Modules\Patient\Models\Allergy;
use App\Modules\Patient\Models\MedicalAntecedent;
use App\Modules\Patient\Models\PatientContact;
use App\Modules\Patient\Models\PatientInsurance;
use App\Modules\Clinical\Models\Consultation;
use App\Modules\Hospitalization\Models\Hospitalization;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Clinical\Models\Prescription;
use App\Modules\Clinical\Models\LabOrder;
use App\Modules\Clinical\Models\MedicalHistory;


class Patient extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid'; // Re-passage à uuid
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'mrn',
        'fname',
        'lname',
        'dob',
        'gender',
        'blood_type',
        'phone',
        'email',
        'address',
        'city',
        'nationality',
        'is_deceased',
        'deceased_at',
        'metadata',
    ];

    protected $casts = [
        'dob' => 'date',
        'is_deceased' => 'boolean',
        'deceased_at' => 'datetime',
        'metadata' => 'json',
        'address' => 'json',
        'gender' => GenderEnum::class,
        'blood_type' => BloodTypeEnum::class,
    ];

    // --- Relations ---

    public function tenant(): BelongsTo          
    { 
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id'); 
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(PatientContact::class, 'patient_uuid', 'uuid');
    }

    public function insurances(): HasMany
    {
        return $this->hasMany(PatientInsurance::class, 'patient_uuid', 'uuid');
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(Allergy::class, 'patient_uuid', 'uuid')->where('is_active', true);
    }

    public function antecedents(): HasMany
    {
        return $this->hasMany(MedicalAntecedent::class, 'patient_uuid', 'uuid');
    }

    // --- Accessors ---

    public function getFullNameAttribute(): string
    {
        return "{$this->fname} {$this->lname}";
    }
    public function getAgeAttribute(): ?int 
    {
        return $this->dob ? $this->dob->age : null;
    }

    // --- Scopes ---

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function($q) use ($term) {
            $q->where('fname', 'like', "%{$term}%")
              ->orWhere('lname', 'like', "%{$term}%")
              ->orWhere('mrn', 'like', "%{$term}%");
        });
    }

    public function scopeAlive(Builder $q): Builder { return $q->where('is_deceased', false); }
}
