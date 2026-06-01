<?php

namespace App\Modules\Foundation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CentralUser extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'last_login_at'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['last_login_at' => 'datetime', 'is_active' => 'boolean'];

}