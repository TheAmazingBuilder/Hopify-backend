<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'prescription_items';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'prescription_uuid',
        'medication_uuid',
        'dosage',
        'frequency',
        'route',
        'duration_days',
        'quantity',
        'instructions',
        'is_substitutable',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'quantity' => 'integer',
        'is_substitutable' => 'boolean',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(
            Prescription::class,
            'prescription_uuid',
            'uuid'
        );
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(
            Medication::class,
            'medication_uuid',
            'uuid'
        );
    }

    public function getEstimatedEndDateAttribute(): ?\Illuminate\Support\Carbon
    {
        if (! $this->prescription?->created_at || ! $this->duration_days) {
            return null;
        }

        return $this->prescription->created_at
            ->copy()
            ->addDays($this->duration_days);
    }
}