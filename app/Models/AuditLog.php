<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'user_id',
        'action',
        'before_changes',
        'after_changes',
    ];

    protected $casts = [
        'before_changes' => 'array',
        'after_changes' => 'array',
    ];
}
