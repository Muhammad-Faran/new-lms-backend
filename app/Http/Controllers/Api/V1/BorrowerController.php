<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkBorrowersRequest;
use App\Http\Requests\BorrowerRequest;
use App\Http\Resources\V1\BorrowerResource;
use App\Http\Resources\V1\BorrowerCollection;
use App\Models\CreditLimit;
use App\Models\Transaction;
use App\Models\BorrowerThreshold;
use App\Models\BorrowerFinancingPolicy;
use App\Models\BorrowerProductRule;
use App\Models\OFACNACTA;
use App\Models\Borrower;
use App\Filters\V1\BorrowerFilter;
use Illuminate\Http\Request;
use App\Models\CreditEngineShipperInfo;
use App\Models\CreditEngineShipperKyc;
use App\Models\CreditEngineShipperPricing;
use App\Models\CreditEngineShipperCreditScore;
use Illuminate\Support\Facades\Http;
use DB;

class BorrowerController extends Controller
{

    public function index(Request $request)
{
    $filter = new BorrowerFilter();

    $query = Borrower::with([
        'products.productTiers',
        'creditLimit',
        'financingPolicy',
        'creditEngineShipperInfo',
        'creditEngineShipperKyc',
        'creditEngineShipperPricing',
        'creditEngineShipperCreditScore',
        'ofacNacta',
        'borrowerThreshold',
        'ProductRules' // Add this line
    ]);

    $borrowers = $filter->filter($query, $request);

    return new BorrowerCollection($borrowers);
}

    public function addBorrower(BorrowerRequest $request)
{
    DB::beginTransaction();

    try {

        $validatedData = $request->validated();
        $validatedData['status'] = 0;

        $borrower = Borrower::create($validatedData);

        // Base URL, endpoints, and headers
        // $baseUrl = config('credit_engine.base_url');
        // $ofacNactaUrl = config('credit_engine.ofac_nacta_url');
        // $endpoints = config('credit_engine.endpoints');
        // $headers = config('credit_engine.headers');
        // $shipperId = $borrower->shipper_id;

        // // Fetch and save Shipper Info
        // $shipperInfoResponse = Http::withHeaders($headers)->get($baseUrl . $shipperId . $endpoints['info']);
        // CreditEngineShipperInfo::create([
        //     'shipper_id' => $shipperId,
        //     'borrower_id' => $borrower->id,
        //     'data' => $shipperInfoResponse->json(),
        // ]);

        // // Fetch and save Shipper KYC
        // $shipperKycResponse = Http::withHeaders($headers)->get($baseUrl . $shipperId . $endpoints['kyc']);
        // CreditEngineShipperKyc::create([
        //     'shipper_id' => $shipperId,
        //     'borrower_id' => $borrower->id,
        //     'data' => $shipperKycResponse->json(),
        // ]);

        // // Fetch and save Shipper Pricing
        // $shipperPricingResponse = Http::withHeaders($headers)->get($baseUrl . $shipperId . $endpoints['pricing']);
        // CreditEngineShipperPricing::create([
        //     'shipper_id' => $shipperId,
        //     'borrower_id' => $borrower->id,
        //     'data' => $shipperPricingResponse->json(),
        // ]);

        // // Fetch and save Shipper Credit Score
        // $shipperCreditScoreResponse = Http::withHeaders($headers)->get($baseUrl . $shipperId . $endpoints['credit_score']);
        // CreditEngineShipperCreditScore::create([
        //     'shipper_id' => $shipperId,
        //     'borrower_id' => $borrower->id,
        //     'data' => $shipperCreditScoreResponse->json(),
        // ]);

        // // Fetch and save OFAC/NACTA matches
        // $dob = $borrower->dob;
        // $yob = date('Y', strtotime($dob));

        // // Set headers and send a POST request
        // $ofacNactaResponse = Http::withHeaders([
        //     'Content-Type' => 'application/json',
        // ])->post($ofacNactaUrl, [
        //     'cnic' => $borrower->cnic,
        //     'name' => $borrower->first_name . ' ' . $borrower->last_name,
        //     'yob' => $yob,
        //     'country' => 'Pakistan',
        // ]);


        // OFACNACTA::create([
        //     'shipper_id' => $shipperId,
        //     'borrower_id' => $borrower->id,
        //     'data' => $ofacNactaResponse->json(),
        // ]);

        DB::commit();

        return response()->json(['message' => 'Borrower added successfully', 'data' => $borrower], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to add borrower: ' . $e->getMessage()], 500);
    }
}




    public function addBorrowersBulk(BulkBorrowersRequest $request)
{
    DB::beginTransaction();

    try {
        $borrowers = collect($request->borrowers)->map(function ($borrowerData) {
            return Borrower::create($borrowerData);
        });

        DB::commit();

        return response()->json([
            'message' => 'Borrowers added successfully',
            'data' => $borrowers,
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to add borrowers: ' . $e->getMessage()], 500);
    }
}


    public function show(Borrower $borrower)
    {
        return new BorrowerResource($borrower);
    }


   public function borrowerByWalletId(Request $request)
{
    // Validate wallet_id
    $request->validate([
        'wallet_id' => 'required|string|exists:borrowers,wallet_id',
    ]);

    $walletId = $request->get('wallet_id');  // Retrieve wallet_id from query parameters

    $borrower = Borrower::with([
        'products.productTiers',
        'creditLimit',
        'financingPolicy',
        'creditEngineShipperInfo',
        'creditEngineShipperKyc',
        'creditEngineShipperPricing',
        'creditEngineShipperCreditScore',
        'productRules',
    ])->where('wallet_id', $walletId)->first();

    if (!$borrower) {
        return response()->json(['message' => 'Borrower not found for the given wallet ID'], 404);
    }

    return new BorrowerResource($borrower);  // Return borrower resource
}

    public function update(Borrower $borrower, BorrowerRequest $request)
    {
        DB::transaction(function () use ($borrower, $request) {
            $validatedData = $request->validated();

            // Update the borrower record
            $borrower->update($validatedData);
        });

        return new BorrowerResource($borrower);
    }

 public function syncBorrowerProducts(Request $request, $borrowerId)
{
    $request->validate([
        'product_ids' => 'array', 
        'product_ids.*.id' => 'exists:products,id',
        'product_ids.*.charge_unit' => 'nullable|string|in:percentage,fixed',
        'product_ids.*.charge_value' => 'nullable|numeric|min:0',
        'order_threshold' => 'nullable|numeric|min:0', // Optional order threshold
        'fixed_threshold_charges' => 'nullable|numeric|min:0', // Optional fixed threshold charges
    ]);

    $borrower = Borrower::findOrFail($borrowerId);

    $productSyncData = [];
    $borrowerRules = [];
    $productsWithNoRules = []; 

    foreach ($request->product_ids as $productData) {
        $productId = $productData['id'];
        $chargeUnit = $productData['charge_unit'] ?? null;
        $chargeValue = $productData['charge_value'] ?? null;

        $productSyncData[] = $productId;

        if (!is_null($chargeUnit) && !is_null($chargeValue)) {
            $borrowerRules[] = [
                'borrower_id' => $borrower->id,
                'product_id' => $productId,
                'charge_unit' => $chargeUnit,
                'charge_value' => $chargeValue,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } else {
            $productsWithNoRules[] = $productId;
        }
    }

    // Sync products
    $borrower->products()->sync($productSyncData);

    // Delete borrower product rules for products where rules are set to null
    BorrowerProductRule::where('borrower_id', $borrower->id)
        ->whereIn('product_id', $productsWithNoRules)
        ->delete();

    // Insert or update borrower product rules for products with custom charges
    foreach ($borrowerRules as $rule) {
        BorrowerProductRule::updateOrCreate(
            ['borrower_id' => $rule['borrower_id'], 'product_id' => $rule['product_id']],
            $rule
        );
    }

    // Handle borrower threshold (insert/update if provided)
    // Check if both fields are explicitly set to NULL
    if ($request->has('order_threshold') && is_null($request->order_threshold) &&
        $request->has('fixed_threshold_charges') && is_null($request->fixed_threshold_charges)) {

        // Delete the threshold row if both values are null
        BorrowerThreshold::where('borrower_id', $borrower->id)->delete();

    } else {
        // Otherwise, update or create the record
        BorrowerThreshold::updateOrCreate(
            ['borrower_id' => $borrower->id],
            [
                'order_threshold' => $request->order_threshold,
                'fixed_threshold_charges' => $request->fixed_threshold_charges,
                'updated_at' => now(),
            ]
        );
    }


    return response()->json([
        'message' => 'Borrower products and threshold synced successfully.',
        'borrower' => $borrower->load('products', 'borrowerThreshold'), // Load the threshold data
    ], 200);
}

    public function updateStatus(Request $request)
{
    $validatedData = $request->validate([
        'status' => 'required|boolean', 
        'wallet_id' => 'required|exists:borrowers,wallet_id',
    ]);

    $updated = Borrower::where('wallet_id', $validatedData['wallet_id'])
        ->update(['status' => $validatedData['status']]);

    if ($updated) {
        return response()->json([
            'message' => 'Borrower status updated successfully.',
        ], 200);
    }

    return response()->json([
        'message' => 'Failed to update borrower status.',
    ], 500);
}

    
    public function assignCreditLimit(Request $request, Borrower $borrower)
{
    // Validate the request
    $request->validate([
        'credit_limit' => 'required|numeric|min:1000'
    ]);

    // Check if the borrower is active
    if (!$borrower->is_active) {
        return response()->json([
            'message' => 'Cannot assign a credit limit to an inactive borrower.',
        ], 403);
    }

    // Check if the borrower already has a credit limit record
    $existingCreditLimit = CreditLimit::where('borrower_id', $borrower->id)->first();

    // If a credit limit exists, calculate the available limit
    if ($existingCreditLimit) {
        $newAvailableLimit = $request->credit_limit - ($existingCreditLimit->credit_limit - $existingCreditLimit->available_limit);
    } else {
        $newAvailableLimit = $request->credit_limit;
    }

    // Create or update the credit limit for the borrower
    $creditLimit = CreditLimit::updateOrCreate(
        ['borrower_id' => $borrower->id], // Match by borrower_id
        [
            'credit_limit' => $request->credit_limit,
            'available_limit' => $newAvailableLimit,
            'status' => 'active', // Default status is active
            'date_assigned' => now(), // Assign the current date and time
        ]
    );

    return response()->json([
        'message' => 'Credit limit assigned successfully.',
        'credit_limit' => $creditLimit,
    ], 200);
}


public function assignFinancingPolicy(Request $request, Borrower $borrower)
{
    // Validate the request
    $request->validate([
        'financing_percentage' => 'required|numeric|min:0|max:100', // Must be a valid percentage
    ]);

    // Check if the borrower is active
    if (!$borrower->is_active) {
        return response()->json([
            'message' => 'Cannot assign a financing policy to an inactive borrower.',
        ], 403);
    }

    // Create or update the financing policy for the borrower
    $financingPolicy = BorrowerFinancingPolicy::updateOrCreate(
        ['borrower_id' => $borrower->id], // Match by borrower_id
        [
            'financing_percentage' => $request->financing_percentage,
            'updated_at' => now(), // Log the update timestamp
        ]
    );

    return response()->json([
        'message' => 'Financing policy assigned successfully.',
        'financing_policy' => $financingPolicy,
    ], 200);
}

/**
     * API to get loan details.
     */
    public function getLoanDetails(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:borrowers,wallet_id',
        ]);

        $borrower = Borrower::where('wallet_id', $request->wallet_id)->firstOrFail();

        $transactions = Transaction::with(['installments'])
            ->where('borrower_id', $borrower->id)
            ->where('status', 'disbursed')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'message' => 'Loan details retrieved successfully.',
            'transactions' => $transactions,
        ]);
    }

    public function getLoanDetailsListing(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:borrowers,wallet_id',
        ]);

        $borrower = Borrower::where('wallet_id', $request->wallet_id)->firstOrFail();

        $transactions = Transaction::where('borrower_id', $borrower->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'message' => 'Loan details retrieved successfully.',
            'transactions' => $transactions,
        ]);
    }

    public function refreshOfacNacta(Request $request)
{
    $request->validate([
        'borrower_id' => 'required|exists:borrowers,id',
    ]);

    try {
        $borrower = Borrower::findOrFail($request->borrower_id);

        // OFAC/NACTA URL and configuration
        $ofacNactaUrl = config('credit_engine.ofac_nacta_url');
        $yob = date('Y', strtotime($borrower->dob));

        // Make the API call
        $ofacNactaResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($ofacNactaUrl, [
            'cnic' => $borrower->cnic,
            'name' => $borrower->first_name . ' ' . $borrower->last_name,
            'yob' => $yob,
            'country' => 'Pakistan',
        ]);

        // Update or create OFACNACTA record
        $ofacNacta = OFACNACTA::updateOrCreate(
            ['borrower_id' => $borrower->id],
            ['shipper_id' => $borrower->shipper_id, 'data' => $ofacNactaResponse->json()]
        );

        return response()->json(['message' => 'OFAC NACTA refreshed successfully', 'data' => $ofacNacta], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to refresh OFAC NACTA: ' . $e->getMessage()], 500);
    }
}

    
    public function refreshCreditEngineShipperCreditScore(Request $request)
{
    $request->validate([
        'borrower_id' => 'required|exists:borrowers,id',
    ]);

    try {

        $borrower = Borrower::findOrFail($request->borrower_id);


        $baseUrl = config('credit_engine.base_url');
        $endpoints = config('credit_engine.endpoints');
        $headers = config('credit_engine.headers');
        $shipperId = $borrower->shipper_id;

        $shipperCreditScoreResponse = Http::withHeaders($headers)->get($baseUrl . $shipperId . $endpoints['credit_score']);

        $shipperCreditScore = CreditEngineShipperCreditScore::updateOrCreate(
            ['borrower_id' => $borrower->id],
            ['shipper_id' => $borrower->shipper_id, 'data' => $shipperCreditScoreResponse->json()]
        );

        return response()->json(['message' => 'Credit Score refreshed successfully', 'data' => $shipperCreditScore], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to refresh Credit Score: ' . $e->getMessage()], 500);
    }
}

    public function refreshCreditEngineShipperInfo(Request $request)
{
    $request->validate([
        'borrower_id' => 'required|exists:borrowers,id',
    ]);

    try {

        $borrower = Borrower::findOrFail($request->borrower_id);


        $baseUrl = config('credit_engine.base_url');
        $endpoints = config('credit_engine.endpoints');
        $headers = config('credit_engine.headers');
        $shipperId = $borrower->shipper_id;

        $shipperInfoResponse = Http::withHeaders($headers)->get($baseUrl . $shipperId . $endpoints['info']);

        $shipperInfo = CreditEngineShipperInfo::updateOrCreate(
            ['borrower_id' => $borrower->id],
            ['shipper_id' => $borrower->shipper_id, 'data' => $shipperInfoResponse->json()]
        );

        return response()->json(['message' => 'Shipper info refreshed successfully', 'data' => $shipperInfo], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to refresh Shipper info: ' . $e->getMessage()], 500);
    }
}
    
    public function refreshCreditEngineShipperKyc(Request $request)
{
    $request->validate([
        'borrower_id' => 'required|exists:borrowers,id',
    ]);

    try {

        $borrower = Borrower::findOrFail($request->borrower_id);

        $baseUrl = config('credit_engine.base_url');
        $endpoints = config('credit_engine.endpoints');
        $headers = config('credit_engine.headers');
        $shipperId = $borrower->shipper_id;

        $shipperKycResponse = Http::withHeaders($headers)->get($baseUrl . $shipperId . $endpoints['kyc']);

        $shipperKyc = CreditEngineShipperKyc::updateOrCreate(
            ['borrower_id' => $borrower->id],
            ['shipper_id' => $borrower->shipper_id, 'data' => $shipperKycResponse->json()]
        );

        return response()->json(['message' => 'Shipper kyc refreshed successfully', 'data' => $shipperKyc], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to refresh Shipper kyc: ' . $e->getMessage()], 500);
    }
}

    public function refreshCreditEngineShipperPricing(Request $request)
{
    $request->validate([
        'borrower_id' => 'required|exists:borrowers,id',
    ]);

    try {

        $borrower = Borrower::findOrFail($request->borrower_id);

        $baseUrl = config('credit_engine.base_url');
        $endpoints = config('credit_engine.endpoints');
        $headers = config('credit_engine.headers');
        $shipperId = $borrower->shipper_id;

        $shipperPricingResponse = Http::withHeaders($headers)->get($baseUrl . $shipperId . $endpoints['pricing']);

        $shipperPricing = CreditEngineShipperPricing::updateOrCreate(
            ['borrower_id' => $borrower->id],
            ['shipper_id' => $borrower->shipper_id, 'data' => $shipperPricingResponse->json()]
        );

        return response()->json(['message' => 'Shipper pricing refreshed successfully', 'data' => $shipperPricing], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to refresh Shipper pricing: ' . $e->getMessage()], 500);
    }
}

public function getUniqueShipperNames()
{
    $uniqueShipperNames = Borrower::whereNotNull('shipper_name')
        ->distinct()
        ->pluck('shipper_name');

    return response()->json([
        'shipper_names' => $uniqueShipperNames,
    ], 200);
}

}
