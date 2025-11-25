<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Traits\ResourcePermissions;
use Illuminate\Http\Request;
use App\Http\Resources\V1\RoleResource;
use App\Http\Resources\V1\RoleCollection;
use App\Filters\V1\RoleFilter;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{

    use ResourcePermissions;

    // Provide the key that is used in permissions
    protected $permission_key = 'roles';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $filter = new RoleFilter();

    $query = Role::query();
    
    $roles = $filter->filter($query, $request);

    return new RoleCollection($roles);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $RoleRequest)
{
    \DB::transaction(function () use ($RoleRequest) {
        $validatedData = $RoleRequest->validated();

        // Create the role
        $role = Role::create($validatedData);

        // Check if permissions are provided and sync them with the role
        if (isset($validatedData['permissions'])) {
            $role->permissions()->sync($validatedData['permissions']);
        }
    });

    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}



    public function show(Role $role)
    {
        return new RoleResource($role);
    }


   public function update(Role $role, RoleRequest $RoleRequest)
{
    \DB::transaction(function () use ($role, $RoleRequest) {
        $validatedData = $RoleRequest->validated();

        // Update the role
        $role->update($validatedData);

        // Sync the permissions if provided
        if (isset($validatedData['permissions'])) {
            $role->permissions()->sync($validatedData['permissions']);
        }
    });

    return new RoleResource($role);
}



    public function destroy(Role $role)
{
    \DB::transaction(function () use ($role) {
        // Detach permissions from the role
        $role->permissions()->detach();

        // Now delete the role
        $role->delete();
    });

    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}


public function syncRolePermissions(Request $request)
{
    // Validate incoming request data
    $validated = $request->validate([
        'role_id' => 'required|exists:roles,id', // Validate the role_id
        'permissions' => 'nullable|array', // Permissions should be an array
        'permissions.*' => 'exists:permissions,id', // Validate each permission ID exists
    ]);

    // Find the role
    $role = Role::findOrFail($validated['role_id']);

    // Get the permissions from the request (if any)
    $permissions = $validated['permissions'] ?? [];

    // Sync the permissions with the role
    $role->permissions()->sync($permissions);

    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}


}
