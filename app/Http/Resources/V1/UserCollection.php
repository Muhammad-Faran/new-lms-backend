<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => "{$user->first_name} {$user->last_name}",
                'is_admin' => $user->is_admin,
                'email' => $user->email,
                'phone' => $user->phone,

                // Include permissions as a separate array
                'permissions' => $user->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'slug' => $permission->slug,
                    ];
                }),
            ];
        });
    }
}
