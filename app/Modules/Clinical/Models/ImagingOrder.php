<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use App\Modules\Hr\Models\Employee;
use App\Modules\Patient\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Shared\Enums\ImagingModality;
use App\Shared\Enums\ImagingOrderStatus;
use App\Shared\Enums\ImagingUrgency;

class ImagingOrder extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'imaging_orders';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'consultation_uuid',
        'patient_uuid',
        'ordered_by_uuid',
        'modality',
        'body_part',
        'urgency',
        'status',
        'clinical_indication',
        'notes',
    ];

    protected $casts = [
        'status' => ImagingOrderStatus::class,
        'urgency' => ImagingUrgency::class,
        'modality' => ImagingModality::class,
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

    public function orderedBy(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'ordered_by_uuid',
            'uuid'
        );
    }

    public function result(): HasOne
    {
        return $this->hasOne(
            ImagingResult::class,
            'imaging_order_uuid',
            'uuid'
        )->latestOfMany('resulted_at');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status',ImagingOrderStatus::Pending->value);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status',ImagingOrderStatus::InProgress->value);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status',ImagingOrderStatus::Completed->value);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status',ImagingOrderStatus::Cancelled->value);
    }

    public function scopeRoutine(Builder $query): Builder
    {
        return $query->where('urgency', ImagingUrgency::Routine->value);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('urgency',ImagingUrgency::Urgent->value);
    }

    public function scopeEmergency(Builder $query): Builder
    {
        return $query->where('urgency', ImagingUrgency::Emergency->value);
    }

    public function isPending(): bool
    {
        return $this->status === ImagingOrderStatus::Pending;
    }

    public function isInProgress(): bool
    {
        return $this->status === ImagingOrderStatus::InProgress;
    }

    public function isCompleted(): bool
    {
        return $this->status === ImagingOrderStatus::Completed;
    }

    public function isCancelled(): bool
    {
        return $this->status === ImagingOrderStatus::Cancelled;
    }

    public function isEmergency(): bool
    {
        return $this->urgency === ImagingUrgency::Emergency;
    }

    public function hasResult(): bool
    {
        return $this->result()->exists();
    }
}