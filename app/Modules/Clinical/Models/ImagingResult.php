<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use App\Modules\Hr\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagingResult extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'imaging_results';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'imaging_order_uuid',
        'radiologist_uuid',
        'file_path',
        'thumbnail_path',
        'report',
        'impression',
        'is_critical',
        'resulted_at',
        'reported_at',
    ];

    protected $casts = [
        'is_critical' => 'boolean',
        'resulted_at' => 'datetime',
        'reported_at' => 'datetime',
    ];

    public function imagingOrder(): BelongsTo
    {
        return $this->belongsTo(
            ImagingOrder::class,
            'imaging_order_uuid',
            'uuid'
        );
    }

    public function radiologist(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'radiologist_uuid',
            'uuid'
        );
    }

    public function scopeCritical(Builder $query): Builder
    {
        return $query->where('is_critical', true);
    }

    public function scopeReported(Builder $query): Builder
    {
        return $query->whereNotNull('reported_at');
    }

    public function scopeUnreported(Builder $query): Builder
    {
        return $query->whereNull('reported_at');
    }

    public function isCritical(): bool
    {
        return $this->is_critical;
    }

    public function isReported(): bool
    {
        return $this->reported_at !== null;
    }
}