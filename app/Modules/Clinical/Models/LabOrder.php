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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Shared\Enums\LabOrderPriority;
use App\Shared\Enums\LabOrderStatus;

class LabOrder extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'lab_orders';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'consultation_uuid',
        'patient_uuid',
        'ordered_by_uuid',
        'order_number',
        'status',
        'priority',
        'clinical_notes',
        'collected_at',
        'collected_by_uuid',
    ];

    protected $casts = [
        'status' => LabOrderStatus::class,
        'priority' => LabOrderPriority::class,
        'collected_at' => 'datetime',
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

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'collected_by_uuid',
            'uuid'
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            LabOrderItem::class,
            'lab_order_uuid',
            'uuid'
        );
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', LabOrderStatus::Pending->value);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', LabOrderStatus::InProgress->value);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', LabOrderStatus::Completed->value);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', LabOrderStatus::Cancelled->value);
    }

    public function scopeRoutine(Builder $query): Builder
    {
        return $query->where('priority', LabOrderPriority::Routine->value);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('priority', LabOrderPriority::Urgent->value);
    }

    public function scopeStat(Builder $query): Builder
    {
        return $query->where('priority', LabOrderPriority::Stat->value);
    }

    public function isPending(): bool
    {
        return $this->status === LabOrderStatus::Pending;
    }

    public function isInProgress(): bool
    {
        return $this->status === LabOrderStatus::InProgress;
    }

    public function isCompleted(): bool
    {
        return $this->status === LabOrderStatus::Completed;
    }

    public function isCancelled(): bool
    {
        return $this->status === LabOrderStatus::Cancelled;
    }
}