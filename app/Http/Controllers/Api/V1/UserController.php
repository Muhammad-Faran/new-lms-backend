<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use App\Traits\ResourcePermissions;
use Illuminate\Http\Request;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\PermissionCollection;
use App\Http\Resources\V1\RoleCollection;
use App\Http\Resources\V1\UserCollection;
use App\Filters\V1\UsersFilter;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    use ResourcePermissions;

    // Provide the key that is used in permissions
    protected $permission_key = 'users';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = new UsersFilter();

        $query = User::where('is_admin', 0);
        $users = $filter->filter($query, $request);

        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $UserRequest) {
    // This line validates the request and throws an exception if validation fails
    $validatedData = $UserRequest->validated();

    // Only if validation passes, the transaction starts
    \DB::transaction(function () use ($validatedData) {
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = \Hash::make($validatedData['password']);
        }

        $user = User::create($validatedData);

        if (!empty($validatedData['role_id'])) {
            $user->roles()->attach($validatedData['role_id']);
        }
    });

    return response()->json([
        "success" => true,
        "data" => [],
    ], 200);

}



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // Global admin cannot be edited
        if ($user->is_admin && !auth()->user()->is_admin) {
            abort(403, 'This action is unauthorized.');
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, UserRequest $UserRequest)
{
    // Global admin cannot be edited
    if ($user->is_admin && !auth()->user()->is_admin) {
        abort(403, 'This action is unauthorized.');
    }

    DB::transaction(function () use ($user, $UserRequest) {
        $validatedData = $UserRequest->validated();

        // Update the user
        $user->update($validatedData);

        // Sync role if role_id is provided, otherwise detach existing roles
        if (!empty($validatedData['role_id'])) {
            $user->roles()->sync([$validatedData['role_id']]);
        } else {
            $user->roles()->detach();
        }
    });

    return new UserResource($user);
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
{
    // Check if the user is a global admin and the current user is not an admin
    if ($user->is_admin && !auth()->user()->is_admin) {
        abort(403, 'This action is unauthorized.');
    }

    \DB::transaction(function () use ($user) {
        $user->delete();
    });

        $user->permissions()->detach();
    
    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}


public function syncUserPermissions(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id', 
    ]);

    $user = User::findOrFail($validated['user_id']);

    $permissions = $validated['permissions'] ?? [];

    $user->permissions()->sync($permissions);

    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}



}
