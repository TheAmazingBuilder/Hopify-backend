<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Bed extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'room_uuid', 'name', 'type', 'status', 'status_changed_at',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_uuid', 'uuid');
    }

    public function currentHospitalization(): HasOne
    {
        return $this->hasOne(Hospitalization::class, 'bed_uuid', 'uuid')
            ->whereNull('discharged_at');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    public function scopeByRoom(Builder $query, string $roomUuid): Builder
    {
        return $query->where('room_uuid', $roomUuid);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
