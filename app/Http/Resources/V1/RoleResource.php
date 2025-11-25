<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            // Add the permissions related to the role
            'permissions' => $this->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'slug' => $permission->slug,
                ];
            }),
        ];
    }
}

