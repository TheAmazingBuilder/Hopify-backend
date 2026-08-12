<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationCategory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'medication_categories';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
    ];

    public function medications(): HasMany
    {
        return $this->hasMany(
            Medication::class,
            'category_uuid',
            'uuid'
        );
    }
}