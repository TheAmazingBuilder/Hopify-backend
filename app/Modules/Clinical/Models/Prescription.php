<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use App\Modules\Clinical\Models\Consultation;
use App\Modules\Hr\Models\Employee;
use App\Modules\Patient\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Shared\Enums\PrescriptionStatus;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'prescriptions';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'consultation_uuid',
        'patient_uuid',
        'doctor_uuid',
        'prescription_number',
        'status',
        'notes',
        'valid_until',
        'dispensed_at',
        'dispensed_by_uuid',
    ];

    protected $casts = [
        'status' => PrescriptionStatus::class,
        'valid_until' => 'datetime',
        'dispensed_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class,'consultation_uuid','uuid');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class,'patient_uuid','uuid');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'doctor_uuid',
            'uuid'
        );
    }

    public function dispensedBy(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'dispensed_by_uuid',
            'uuid'
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            PrescriptionItem::class,
            'prescription_uuid',
            'uuid'
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status',PrescriptionStatus::Active->value);
    }

    public function scopeDispensed(Builder $query): Builder
    {
        return $query->where('status',PrescriptionStatus::Dispensed->value);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status',PrescriptionStatus::Cancelled->value);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status',PrescriptionStatus::Expired->value);
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query
            ->where('status',PrescriptionStatus::Active->value)
            ->where(function (Builder $q): void {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    public function isActive(): bool
    {
        return $this->status === PrescriptionStatus::Active;
    }

    public function isDispensed(): bool
    {
        return $this->status === PrescriptionStatus::Dispensed;
    }

    public function isCancelled(): bool
    {
        return $this->status === PrescriptionStatus::Cancelled;
    }

    public function isExpired(): bool
    {
        if ($this->status === PrescriptionStatus::Expired) {
            return true;
        }
        return $this->valid_until !== null
            && $this->valid_until->isPast();
    }

    public function isValid(): bool
    {
        return $this->status === PrescriptionStatus::Active
            && (
                $this->valid_until === null
                || $this->valid_until->isFuture()
            );
    }

    public function dispense(Employee $employee): bool
    {
        if (! $this->isValid()) {
            return false;
        }

        return $this->update([
            'status' => PrescriptionStatus::Dispensed,
            'dispensed_at' => now(),
            'dispensed_by_uuid' => $employee->uuid,
        ]);
    }

    public function cancel(): bool
    {
        if ($this->status !== PrescriptionStatus::Active) {
            return false;
        }

        return $this->update([
            'status' => PrescriptionStatus::Cancelled,
        ]);
    }

    public function markExpired(): bool
    {
        if ($this->status !== PrescriptionStatus::Active) {
            return false;
        }

        return $this->update([
            'status' => PrescriptionStatus::Expired,
        ]);
    }
}