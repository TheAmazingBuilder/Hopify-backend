<?php

namespace App\Modules\Foundation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'slug', 'description', 'stripe_id', 'price_monthly', 'price_yearly', 'features', 'is_active'];
    protected $casts = ['features' => 'json', 'is_active' => 'boolean'];

}