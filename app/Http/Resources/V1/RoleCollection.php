<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($role) {
            // Get the permissions for this role
            $permissions = $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'slug' => $permission->slug,
                ];
            });

            return [
                'id' => $role->id,
                'slug' => $role->slug,
                'permissions' => $permissions, // Add permissions to the response
            ];
        })->toArray();
    }
}


