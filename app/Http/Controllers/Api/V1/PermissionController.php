<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use App\Traits\ResourcePermissions;
use Illuminate\Http\Request;
use App\Http\Resources\V1\PermissionResource;
use App\Http\Resources\V1\PermissionCollection;
use App\Filters\V1\PermissionsFilter;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{

    use ResourcePermissions;

    // Provide the key that is used in permissions
    protected $permission_key = 'permissions';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $filter = new PermissionsFilter();

    $query = Permission::query();
    
    $permissions = $filter->filter($query, $request);

    return new PermissionCollection($permissions);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $PermissionRequest)
{
    \DB::transaction(function () use ($PermissionRequest) {
        $validatedData = $PermissionRequest->validated();

        $permission = Permission::create($validatedData);

    });

    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}


    public function show(Permission $permission)
    {
        return new PermissionResource($permission);
    }


    public function update(Permission $permission, PermissionRequest $PermissionRequest)
{
    
    DB::transaction(function () use ($permission, $PermissionRequest) {
        $validatedData = $PermissionRequest->validated();

        $permission->update($validatedData);
        
    });

    return new PermissionResource($permission);
}


    public function destroy(Permission $permission)
{

    \DB::transaction(function () use ($permission) {
        $permission->delete();
    });

    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}

}
