<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

trait HasPermissions
{
    public function givePermissionsTo(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if ($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    public function withdrawPermissionsTo(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }

    public function refreshPermissions(...$permissions)
    {
        $this->permissions()->detach();
        return $this->givePermissionsTo($permissions);
    }

    public function hasPermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        return ($this->hasPermissionThroughRole($permission) || $this->hasPermission($permission) || $this->hasPermissionAsAdmin());
    }

    protected function hasPermission($permission)
    {
        return (bool) $this->permissions()->where('slug', $permission->slug)->count();
    }

    public function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->roles()->get()->contains($role)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermissionAsAdmin()
    {
        if ($this->is_admin) {
            return true;
        }

        return false;
    }

    public function hasRole(...$roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    public function roles()
{
    return $this->belongsToMany(Role::class, 'user_role');
}

public function getRoleAttribute()
{
    return $this->roles()->first();
}


public function getAllPermissionSlugs()
{
    if ($this->is_admin) {
        return Permission::pluck('slug')->toArray();
    }

    $directPermissions = $this->permissions()->pluck('slug')->toArray();

    $rolePermissions = $this->roles()
        ->with('permissions')
        ->get()
        ->pluck('permissions.*.slug')
        ->flatten()
        ->toArray();

    $allPermissions = array_unique(array_merge($directPermissions, $rolePermissions));

    return $allPermissions;
}




    public function permissions()
{
    return $this->belongsToMany(Permission::class, 'user_permission');
}


    protected function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('slug', $permissions)->get();
    }
}
