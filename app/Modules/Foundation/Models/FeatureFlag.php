<?php

namespace App\Modules\Foundation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeatureFlag extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['tenant_id', 'feature_name', 'description', 'is_enabled', 'config'];
    protected $casts = ['is_enabled' => 'boolean', 'config' => 'json'];

}