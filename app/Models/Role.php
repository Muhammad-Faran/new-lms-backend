<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;

class Role extends Model
{
    use LogsModelChanges;
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    protected $fillable = ['slug'];


    public $timestamps = false;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

}
