<?php

namespace App\Modules\Scheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Patient\Models\Patient;
use App\Modules\Foundation\Models\User;

class Appointment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'patient_uuid',
        'doctor_uuid',
        'start_time',
        'end_time',
        'status',
        'type',
        'reason',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'metadata' => 'json',
    ];

    // --- Relations ---

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_uuid', 'uuid');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_uuid', 'uuid');
    }

    // --- Scopes ---

    public function scopeForDoctor($query, string $doctorUuid)
    {
        return $query->where('doctor_uuid', $doctorUuid);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
