<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use App\Modules\Hr\Models\Employee;
use App\Modules\Patient\Models\Patient;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSign extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'vital_signs';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'consultation_uuid',
        'patient_uuid',
        'recorded_by_uuid',
        'temperature_celsius',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'respiratory_rate',
        'oxygen_saturation',
        'weight_kg',
        'height_cm',
        'bmi',
        'pain_scale',
        'blood_glucose',
        'notes',
        'recorded_at',
    ];

    protected $casts = [
        'temperature_celsius' => 'decimal:1',
        'oxygen_saturation' => 'decimal:1',
        'weight_kg' => 'decimal:2',
        'height_cm' => 'decimal:1',
        'bmi' => 'decimal:1',
        'blood_glucose' => 'decimal:1',
        'recorded_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(
            Consultation::class,
            'consultation_uuid',
            'uuid'
        );
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class,
            'patient_uuid',
            'uuid'
        );
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'recorded_by_uuid',
            'uuid'
        );
    }
}