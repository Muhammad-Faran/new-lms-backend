<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class Permission extends Model
{
    use LogsModelChanges;
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id', 'pivot'];

    protected $fillable = ['slug'];

    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission');
    }

}
