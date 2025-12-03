<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkApplicantsRequest;
use App\Http\Requests\ApplicantRequest;
use App\Http\Resources\V1\ApplicantResource;
use App\Http\Resources\V1\ApplicantCollection;
use App\Models\CreditLimit;
use App\Models\Application;
use App\Models\ApplicantThreshold;
use App\Models\ApplicantFinancingPolicy;
use App\Models\ApplicantProductRule;
use App\Models\OFACNACTA;
use App\Models\Applicant;
use App\Filters\V1\ApplicantFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DB;

class ApplicantController extends Controller
{

    public function index(Request $request)
{
    $filter = new ApplicantFilter();

    $query = Applicant::with([
        'products.productTiers',
        'creditLimit',
        'financingPolicy',
        'ofacNacta',
        'applicantThreshold',
        'ProductRules' // Add this line
    ]);

    $applicants = $filter->filter($query, $request);

    return new ApplicantCollection($applicants);
}

    public function addApplicant(ApplicantRequest $request)
{
    DB::beginTransaction();

    try {

        $validatedData = $request->validated();
        $validatedData['status'] = 0;

        $applicant = Applicant::create($validatedData);
        DB::commit();

        return response()->json(['message' => 'Applicant added successfully', 'data' => $applicant], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to add applicant: ' . $e->getMessage()], 500);
    }
}




    public function addApplicantsBulk(BulkApplicantsRequest $request)
{
    DB::beginTransaction();

    try {
        $applicants = collect($request->applicants)->map(function ($applicantData) {
            return Applicant::create($applicantData);
        });

        DB::commit();

        return response()->json([
            'message' => 'Applicants added successfully',
            'data' => $applicants,
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to add Applicants: ' . $e->getMessage()], 500);
    }
}


    public function show(Applicant $applicant)
    {
        return new ApplicantResource($applicant);
    }


   public function applicantLoanDetails(Applicant $applicant)
{
    $applicant->load([
        'applications.product',
        'applications.plan',
        'applications.charges',
        'applications.installments',
    ]);

    return response()->json([
        'applicant'    => $applicant,
        'applications' => $applicant->applications,
    ]);
}


    public function update(Applicant $applicant, ApplicantRequest $request)
    {
        DB::transaction(function () use ($applicant, $request) {
            $validatedData = $request->validated();

            // Update the applicant record
            $applicant->update($validatedData);
        });

        return new ApplicantResource($applicant);
    }

 public function syncApplicantProducts(Request $request, $applicantId)
{
    $request->validate([
        'product_ids' => 'array', 
        'product_ids.*.id' => 'exists:products,id',
        'product_ids.*.charge_unit' => 'nullable|string|in:percentage,fixed',
        'product_ids.*.charge_value' => 'nullable|numeric|min:0',
        'order_threshold' => 'nullable|numeric|min:0', // Optional order threshold
        'fixed_threshold_charges' => 'nullable|numeric|min:0', // Optional fixed threshold charges
    ]);

    $applicant = Applicant::findOrFail($applicantId);

    $productSyncData = [];
    $applicantRules = [];
    $productsWithNoRules = []; 

    foreach ($request->product_ids as $productData) {
        $productId = $productData['id'];
        $chargeUnit = $productData['charge_unit'] ?? null;
        $chargeValue = $productData['charge_value'] ?? null;

        $productSyncData[] = $productId;

        if (!is_null($chargeUnit) && !is_null($chargeValue)) {
            $applicantRules[] = [
                'applicant_id' => $applicant->id,
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
    $applicant->products()->sync($productSyncData);

    // Delete applicant product rules for products where rules are set to null
    ApplicantProductRule::where('applicant_id', $applicant->id)
        ->whereIn('product_id', $productsWithNoRules)
        ->delete();

    // Insert or update applicant product rules for products with custom charges
    foreach ($applicantRules as $rule) {
        ApplicantProductRule::updateOrCreate(
            ['applicant_id' => $rule['applicant_id'], 'product_id' => $rule['product_id']],
            $rule
        );
    }

    // Handle applicant threshold (insert/update if provided)
    // Check if both fields are explicitly set to NULL
    if ($request->has('order_threshold') && is_null($request->order_threshold) &&
        $request->has('fixed_threshold_charges') && is_null($request->fixed_threshold_charges)) {

        // Delete the threshold row if both values are null
        ApplicantThreshold::where('applicant_id', $applicant->id)->delete();

    } else {
        // Otherwise, update or create the record
        ApplicantThreshold::updateOrCreate(
            ['applicant_id' => $applicant->id],
            [
                'order_threshold' => $request->order_threshold,
                'fixed_threshold_charges' => $request->fixed_threshold_charges,
                'updated_at' => now(),
            ]
        );
    }


    return response()->json([
        'message' => 'Applicant products and threshold synced successfully.',
        'applicant' => $applicant->load('products', 'applicantThreshold'), // Load the threshold data
    ], 200);
}

    public function updateStatus(Request $request)
{
    $validatedData = $request->validate([
        'status' => 'required|boolean', 
        'id' => 'required|exists:applicants,id',
    ]);

    $updated = Applicant::where('id', $validatedData['id'])
        ->update(['status' => $validatedData['status']]);

    if ($updated) {
        return response()->json([
            'message' => 'Applicant status updated successfully.',
        ], 200);
    }

    return response()->json([
        'message' => 'Failed to update applicant status.',
    ], 500);
}

    
    public function assignCreditLimit(Request $request, Applicant $applicant)
{
    // Validate the request
    $request->validate([
        'credit_limit' => 'required|numeric|min:1000'
    ]);

    // Check if the applicant is active
    if (!$applicant->is_active) {
        return response()->json([
            'message' => 'Cannot assign a credit limit to an inactive applicant.',
        ], 403);
    }

    // Check if the applicant already has a credit limit record
    $existingCreditLimit = CreditLimit::where('applicant_id', $applicant->id)->first();

    // If a credit limit exists, calculate the available limit
    if ($existingCreditLimit) {
        $newAvailableLimit = $request->credit_limit - ($existingCreditLimit->credit_limit - $existingCreditLimit->available_limit);
    } else {
        $newAvailableLimit = $request->credit_limit;
    }

    // Create or update the credit limit for the applicant
    $creditLimit = CreditLimit::updateOrCreate(
        ['applicant_id' => $applicant->id], // Match by applicant_id
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


public function assignFinancingPolicy(Request $request, Applicant $applicant)
{
    // Validate the request
    $request->validate([
        'financing_percentage' => 'required|numeric|min:0|max:100', // Must be a valid percentage
    ]);

    // Check if the applicant is active
    if (!$applicant->is_active) {
        return response()->json([
            'message' => 'Cannot assign a financing policy to an inactive applicant.',
        ], 403);
    }

    // Create or update the financing policy for the applicant
    $financingPolicy = ApplicantFinancingPolicy::updateOrCreate(
        ['applicant_id' => $applicant->id], // Match by applicant_id
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
            'wallet_id' => 'required|exists:applicants,wallet_id',
        ]);

        $applicant = Applicant::where('wallet_id', $request->wallet_id)->firstOrFail();

        $applications = Application::with(['installments'])
            ->where('applicant_id', $applicant->id)
            ->where('status', 'disbursed')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'message' => 'Loan details retrieved successfully.',
            'applications' => $applications,
        ]);
    }

    public function getLoanDetailsListing(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:applicants,wallet_id',
        ]);

        $applicant = Applicant::where('wallet_id', $request->wallet_id)->firstOrFail();

        $applications = Application::where('applicant_id', $applicant->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'message' => 'Loan details retrieved successfully.',
            'applications' => $applications,
        ]);
    }

    public function refreshOfacNacta(Request $request)
{
    $request->validate([
        'applicant_id' => 'required|exists:applicants,id',
    ]);

    try {
        $applicant = Applicant::findOrFail($request->applicant_id);

        // OFAC/NACTA URL and configuration
        $ofacNactaUrl = config('credit_engine.ofac_nacta_url');
        $yob = date('Y', strtotime($applicant->dob));

        // Make the API call
        $ofacNactaResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($ofacNactaUrl, [
            'cnic' => $applicant->cnic,
            'name' => $applicant->first_name . ' ' . $applicant->last_name,
            'yob' => $yob,
            'country' => 'Pakistan',
        ]);

        // Update or create OFACNACTA record
        $ofacNacta = OFACNACTA::updateOrCreate(
            ['applicant_id' => $applicant->id],
            ['shipper_id' => $applicant->shipper_id, 'data' => $ofacNactaResponse->json()]
        );

        return response()->json(['message' => 'OFAC NACTA refreshed successfully', 'data' => $ofacNacta], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to refresh OFAC NACTA: ' . $e->getMessage()], 500);
    }
}

    
   
}
