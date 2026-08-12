<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Hr\Models\Employee;

class BedAssignment extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'hospitalization_uuid', 'bed_uuid', 'assigned_at', 'released_at', 'assigned_by_uuid',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function hospitalization(): BelongsTo
    {
        return $this->belongsTo(Hospitalization::class, 'hospitalization_uuid', 'uuid');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'bed_uuid', 'uuid');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_by_uuid', 'uuid');
    }
}
