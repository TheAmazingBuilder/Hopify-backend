<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Shared\Enums\LabOrderItemStatus;

class LabOrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lab_order_items';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'lab_order_uuid',
        'lab_test_uuid',
        'status',
    ];

    protected $casts = [
        'status' => LabOrderItemStatus::class,
    ];

    public function labOrder(): BelongsTo
    {
        return $this->belongsTo(
            LabOrder::class,
            'lab_order_uuid',
            'uuid'
        );
    }

    public function labTest(): BelongsTo
    {
        return $this->belongsTo(
            LabTest::class,
            'lab_test_uuid',
            'uuid'
        );
    }

    public function result(): HasOne
    {
        return $this->hasOne(
            LabResult::class,
            'lab_order_item_uuid',
            'uuid'
        )->latestOfMany('resulted_at');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status',LabOrderItemStatus::Pending->value);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status',LabOrderItemStatus::Completed->value);
    }

    public function hasResult(): bool
    {
        return $this->result()->exists();
    }

    public function isPending(): bool
    {
        return $this->status === LabOrderItemStatus::Pending;
    }

    public function isCompleted(): bool
    {
        return $this->status === LabOrderItemStatus::Completed;
    }
}