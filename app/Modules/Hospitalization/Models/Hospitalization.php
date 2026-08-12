<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Patient\Models\Patient;
use App\Modules\Hr\Models\Employee;

class Hospitalization extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'patient_uuid', 'bed_uuid', 'admitted_by_uuid', 'attending_doctor_uuid',
        'admission_diagnosis', 'discharge_diagnosis', 'admitted_at', 'discharged_at',
        'status', 'discharge_notes', 'discharge_type',
    ];

    protected $casts = [
        'admitted_at' => 'datetime',
        'discharged_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_uuid', 'uuid');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'bed_uuid', 'uuid');
    }

    public function admittedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'admitted_by_uuid', 'uuid');
    }

    public function attendingDoctor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'attending_doctor_uuid', 'uuid');
    }

    public function nursingNotes(): HasMany
    {
        return $this->hasMany(NursingNote::class, 'hospitalization_uuid', 'uuid')
            ->orderByDesc('noted_at');
    }

    public function doctorRounds(): HasMany
    {
        return $this->hasMany(DoctorRound::class, 'hospitalization_uuid', 'uuid')
            ->orderByDesc('occurred_at');
    }

    public function bedAssignments(): HasMany
    {
        return $this->hasMany(BedAssignment::class, 'hospitalization_uuid', 'uuid')
            ->orderByDesc('assigned_at');
    }

    public function currentBedAssignment(): HasOne
    {
        return $this->hasOne(BedAssignment::class, 'hospitalization_uuid', 'uuid')
            ->whereNull('released_at')
            ->latest('assigned_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('discharged_at')
            ->where('status', 'active');
    }

    public function scopeByPatient(Builder $query, string $patientUuid): Builder
    {
        return $query->where('patient_uuid', $patientUuid);
    }

    public function scopeByBed(Builder $query, string $bedUuid): Builder
    {
        return $query->where('bed_uuid', $bedUuid);
    }

    public function isActive(): bool
    {
        return $this->discharged_at === null && $this->status === 'active';
    }
}
