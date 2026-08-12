<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medication extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'medications';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'category_uuid',
        'name',
        'generic_name',
        'brand_name',
        'form',
        'strength',
        'unit',
        'dci_code',
        'atc_code',
        'is_controlled',
        'requires_prescription',
        'is_active',
        'contraindications',
        'side_effects',
    ];

    protected $casts = [
        'is_controlled' => 'boolean',
        'requires_prescription' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            MedicationCategory::class,
            'category_uuid',
            'uuid'
        );
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(
            PrescriptionItem::class,
            'medication_uuid',
            'uuid'
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePrescribable(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('requires_prescription', true);
    }

    public function scopeControlled(Builder $query): Builder
    {
        return $query->where('is_controlled', true);
    }

    public function scopeByGenericName(
        Builder $query,
        string $genericName
    ): Builder {
        return $query->where(
            'generic_name',
            'like',
            "%{$genericName}%"
        );
    }

    public function scopeByAtcCode(
        Builder $query,
        string $atcCode
    ): Builder {
        return $query->where('atc_code', $atcCode);
    }

    public function isAvailableForPrescription(): bool
    {
        return $this->is_active && $this->requires_prescription;
    }
}