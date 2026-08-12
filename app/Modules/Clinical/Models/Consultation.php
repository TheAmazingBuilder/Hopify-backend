<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use App\Modules\Hr\Models\Employee;
use App\Modules\Hospitalization\Models\Hospitalization;
use App\Modules\Patient\Models\Patient;
use App\Modules\Scheduling\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'consultations';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'patient_uuid',
        'doctor_uuid',
        'appointment_uuid',
        'hospitalization_uuid',
        'chief_complaint',
        'history_of_illness',
        'review_of_systems',
        'physical_examination',
        'assessment',
        'plan',
        'follow_up_date',
        'follow_up_instructions',
        'is_finalized',
        'finalized_at',
        'finalized_by_uuid',
        'consultation_date',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'is_finalized' => 'boolean',
        'finalized_at' => 'datetime',
        'consultation_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class,
            'patient_uuid',
            'uuid'
        );
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'doctor_uuid',
            'uuid'
        );
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(
            Appointment::class,
            'appointment_uuid',
            'uuid'
        );
    }

    public function hospitalization(): BelongsTo
    {
        return $this->belongsTo(
            Hospitalization::class,
            'hospitalization_uuid',
            'uuid'
        );
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(
            \App\Modules\Foundation\Models\User::class,
            'finalized_by_uuid',
            'uuid'
        );
    }

    public function vitalSigns(): HasMany
    {
        return $this->hasMany(
            VitalSign::class,
            'consultation_uuid',
            'uuid'
        )->orderByDesc('recorded_at');
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(
            Diagnosis::class,
            'consultation_uuid',
            'uuid'
        );
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(
            Prescription::class,
            'consultation_uuid',
            'uuid'
        )->latest('created_at');
    }

    public function labOrders(): HasMany
    {
        return $this->hasMany(
            LabOrder::class,
            'consultation_uuid',
            'uuid'
        )->latest('created_at');
    }

    public function imagingOrders(): HasMany
    {
        return $this->hasMany(
            ImagingOrder::class,
            'consultation_uuid',
            'uuid'
        )->latest('created_at');
    }

    public function scopeFinalized(Builder $query): Builder
    {
        return $query->where('is_finalized', true);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('is_finalized', false);
    }

    public function scopeForPatient(
        Builder $query,
        string $patientUuid
    ): Builder {
        return $query->where('patient_uuid', $patientUuid);
    }

    public function scopeForDoctor(
        Builder $query,
        string $doctorUuid
    ): Builder {
        return $query->where('doctor_uuid', $doctorUuid);
    }

    public function finalize(): bool
    {
        if ($this->is_finalized) {
            return false;
        }

        return $this->update([
            'is_finalized' => true,
            'finalized_at' => now(),
        ]);
    }
}