<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChargeRequest;
use App\Models\Charge;
use App\Traits\ResourcePermissions;
use Illuminate\Http\Request;
use App\Http\Resources\V1\ChargeResource;
use App\Http\Resources\V1\ChargeCollection;
use App\Filters\V1\ChargeFilter;
use Illuminate\Support\Facades\DB;

class ChargeController extends Controller
{

    use ResourcePermissions;

    // Provide the key that is used in permissions
    protected $permission_key = 'charges';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $filter = new ChargeFilter();

    $query = Charge::query();
    
    $charges = $filter->filter($query, $request);

    return new ChargeCollection($charges);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChargeRequest $chargeRequest)
{
    \DB::transaction(function () use ($chargeRequest) {
        $validatedData = $chargeRequest->validated();

        // Create the charge
        $charge = Charge::create($validatedData);
        
    });

    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}



    public function show(Charge $charge)
    {
        return new ChargeResource($charge);
    }


   public function update(Charge $charge, ChargeRequest $chargeRequest)
{
    \DB::transaction(function () use ($charge, $chargeRequest) {
        $validatedData = $chargeRequest->validated();

        // Update the charge
        $charge->update($validatedData);

    });

    return new ChargeResource($charge);
}



    public function destroy(Charge $charge)
{
    \DB::transaction(function () use ($charge) {
        // Now delete the charge
        $charge->delete();
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
