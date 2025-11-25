<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'device_ip_address',
        'device_cookie',
        'device_expiry',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
