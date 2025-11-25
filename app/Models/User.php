<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\LogsModelChanges;
use App\Traits\HasPermissions;


class User extends Authenticatable
{
    
    use HasFactory, HasApiTokens, HasPermissions, LogsModelChanges;

   	protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_otp',
        'email_otp_expiry',
        'email_otp_attempts'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }
}
