<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use App\Modules\Hr\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Shared\Enums\LabAbnormalityLevel;

class LabResult extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lab_results';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'lab_order_item_uuid',
        'value',
        'unit',
        'reference_range',
        'is_abnormal',
        'abnormality_level',
        'notes',
        'resulted_at',
        'resulted_by_uuid',
        'validated_by_uuid',
        'validated_at',
    ];

    protected $casts = [
        'is_abnormal' => 'boolean',
        'abnormality_level' => LabAbnormalityLevel::class,
        'resulted_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function labOrderItem(): BelongsTo
    {
        return $this->belongsTo(
            LabOrderItem::class,
            'lab_order_item_uuid',
            'uuid'
        );
    }

    public function resultedBy(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'resulted_by_uuid',
            'uuid'
        );
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'validated_by_uuid',
            'uuid'
        );
    }

    public function scopeAbnormal(Builder $query): Builder
    {
        return $query->where('is_abnormal', true);
    }

    public function scopeCritical(Builder $query): Builder
    {
        return $query->where('abnormality_level',LabAbnormalityLevel::Critical->value);
    }

    public function scopeHigh(Builder $query): Builder
    {
        return $query->where('abnormality_level',LabAbnormalityLevel::High->value);
    }

    public function scopeLow(Builder $query): Builder
    {
        return $query->where('abnormality_level',LabAbnormalityLevel::Low->value);
    }

    public function isValidated(): bool
    {
        return $this->validated_at !== null
            && $this->validated_by_uuid !== null;
    }

    public function isCritical(): bool
    {
        return $this->abnormality_level === LabAbnormalityLevel::Critical;
    }
}