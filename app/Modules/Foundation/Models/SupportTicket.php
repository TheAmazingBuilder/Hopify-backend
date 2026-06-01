<?php

namespace App\Modules\Foundation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportTicket extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['tenant_id', 'subject', 'message', 'status', 'priority', 'assigned_to', 'resolved_at'];
    protected $casts = ['resolved_at' => 'datetime'];

}