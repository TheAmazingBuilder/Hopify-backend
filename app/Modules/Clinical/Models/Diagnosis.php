<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Shared\Enums\DiagnosisType;

class Diagnosis extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'diagnoses';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'consultation_uuid',
        'icd_code_uuid',
        'type',
        'notes',
    ];

    protected $casts = [
        'type' => DiagnosisType::class,
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class,'consultation_uuid','uuid');
    }

    public function icdCode(): BelongsTo
    {
        return $this->belongsTo(IcdCode::class,'icd_code_uuid','uuid');
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('type', DiagnosisType::Primary->value);
    }

    public function scopeSecondary(Builder $query): Builder
    {
        return $query->where('type', DiagnosisType::Secondary->value);
    }

    public function scopeDifferential(Builder $query): Builder
    {
        return $query->where('type', DiagnosisType::Differential->value);
    }
}