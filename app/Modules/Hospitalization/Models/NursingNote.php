<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Hr\Models\Employee;

class NursingNote extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'hospitalization_uuid', 'nurse_uuid', 'type', 'note', 'noted_at',
    ];

    protected $casts = [
        'noted_at' => 'datetime',
    ];

    public function hospitalization(): BelongsTo
    {
        return $this->belongsTo(Hospitalization::class, 'hospitalization_uuid', 'uuid');
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nurse_uuid', 'uuid');
    }
}
