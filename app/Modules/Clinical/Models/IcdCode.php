<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IcdCode extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'icd_codes';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'description',
        'category',
        'chapter',
        'version',
        'is_billable',
    ];

    protected $casts = [
        'is_billable' => 'boolean',
    ];

    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class, 'icd_code_uuid', 'uuid');
    }

    public function scopeBillable(Builder $query): Builder
    {
        return $query->where('is_billable', true);
    }

    public function scopeForVersion(Builder $query, string $version): Builder
    {
        return $query->where('version', $version);
    }
}