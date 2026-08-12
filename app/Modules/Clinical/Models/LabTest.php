<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabTest extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lab_tests';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'code',
        'category',
        'unit',
        'reference_range_male',
        'reference_range_female',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(
            LabOrderItem::class,
            'lab_test_uuid',
            'uuid'
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory(
        Builder $query,
        string $category
    ): Builder {
        return $query->where('category', $category);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}