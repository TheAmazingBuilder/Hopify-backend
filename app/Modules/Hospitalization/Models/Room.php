<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Foundation\Models\Department;

class Room extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'department_uuid', 'name', 'type', 'floor', 'capacity', 'is_active',
    ];

    protected $casts = [
        'floor' => 'integer',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_uuid', 'uuid');
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class, 'room_uuid', 'uuid');
    }

    public function activeBeds(): HasMany
    {
        return $this->beds()->where('status', 'available');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
