<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductBook;
use App\Models\ProductPlan;
use App\Models\ProductTier;
use App\Models\Charge;
use App\Http\Resources\V1\ProductCollection;
use App\Filters\V1\ProductsFilter;
use Illuminate\Http\Request;
use DB;
use Exception;

class ProductController extends Controller
{

    public function index(Request $request)
{
    $filter = new ProductsFilter();

    $query = Product::query()
        ->with([
        'productBooks.book',
        'productPlans.productTier',
        'productTiers.productTierCharges.productCharge.charge',
        'productCharges.charge'
    ]);

    $products = $filter->filter($query, $request);

    return new ProductCollection($products);
}


  public function addProduct(ProductRequest $request)
{
    // Begin the transaction
    DB::beginTransaction();

    try {
        // Step 1: Create the Product (using validated data from the request)
        $validatedData = $request->validated();
        
        // Create the product record
        $product = Product::create([
            'name' => $validatedData['name'],
            'code' => $validatedData['code'],
            'per_user_availability' => $validatedData['limit'],
            'tnc' => $validatedData['tnc'],
            'description' => $validatedData['description'],
            'max_requested_amount' => $validatedData['max_requested_amount'] ?? null,
            'min_requested_amount' => $validatedData['min_requested_amount'] ?? null,
            'eligibility_criteria' => $request->eligibility_criteria ?? null,
            'disable_loan_on_avail' => $validatedData['disabledLoans'] ?? null,
            'default_status' => $validatedData['default_status'],
            'status' => $validatedData['status']
        ]);

        // Step 4: Create Product Charges (selected charges from the front-end)
        $product_charges_ids_array = [];

        if (isset($validatedData['selected_charges'])) {
            foreach ($validatedData['selected_charges'] as $product_charges) {
                // Create ProductCharge record through relationship
                $productChargesId = $product->productCharges()->create([
                    'charge_id' => $product_charges['charge_id'],  // Charge id from the 'charges' table
                    'apply_fed' => $product_charges  ['apply_fed'] ?? false, // Set from request or default to false
                    'fed_charges_unit' => $product_charges  ['fed_charges_unit'] ?? "", // Set from request or default to false
                    'fed_charges_value' => $product_charges  ['fed_charges_value'] ?? 0, // Set from request or default to false
                    'charge_condition' => $product_charges  ['charge_condition'] ?? 'Requested Amount', // Set from request or default to 'Requested Amount'
                ])->id;
            $product_charges_ids_array[$product_charges['id']] = $productChargesId;
            }
        }

        // Step 2: Create Product Books (using the created product_id)
        if (isset($validatedData['product_books'])) {
            foreach ($validatedData['product_books'] as $product_book) {
                $product->productBooks()->create([
                    'book_id' =>  $product_book['book_id'],  // Charge id from the 'charges' table
                    'preference' => $product_book['preference'],
                    'status' => $product_book['status'],
                ]);
            }
        }

        $tierArray = [];
        // Step 3: Create Product Tiers (using the created product_id)
        if (isset($validatedData['product_tiers'])) {
    foreach ($validatedData['product_tiers'] as $product_tier) {
        // Create the Product Tier first
        $productTier = $product->productTiers()->create([
            'name' => $product_tier['name'],
            'order_threshold' => $product_tier['order_threshold'] ?? null ,
            'fixed_threshold_charges' => $product_tier['fixed_threshold_charges'] ?? null ,
            'penalty_charges_unit' => $product_tier['penalty_charges_unit'],
            'penalty_charges_value' => $product_tier['penalty_charges_value'],
            'installment_grace_period' => $product_tier['installment_grace_period'],
            'installment_defaulter_days' => $product_tier['installment_defaulter_days'],
            'status' => $product_tier['status']
        ]);

        // Step 3.1: Create Product Tier Charges (using the created product_tier_id)
        if (isset($product_tier['product_tier_charges'])) {
            foreach ($product_tier['product_tier_charges'] as $tier_charge) {
                // Create ProductTierCharge through the product tier relationship
                $productTier->productTierCharges()->create([
                    'product_charge_id' => $product_charges_ids_array[$tier_charge['charge_id']],
                    'charges_unit' => $tier_charge['charges_unit'], // Charge unit (e.g., percentage, fixed)
                    'charges_value' => $tier_charge['charges_value'], // Charge value with precision
                    'is_fed_inclusive' => $tier_charge['is_fed_inclusive'],
                ]);
            }
        }
            $tierArray[$product_tier['id']] = $productTier->id;
    }
}


        // Step 5: Create Product Plans (using the created product_id)
        if (isset($validatedData['product_plans'])) {
            foreach ($validatedData['product_plans'] as $product_plan) {
                $product->productPlans()->create([
                    'product_tier_id' => $tierArray[$product_plan['product_tier_id']],  // Use correct reference to tier
                    'name' => $product_plan['name'],
                    'duration_unit' => $product_plan['duration_unit'],
                    'duration_value' => $product_plan['duration_value'],
                    'status' => $product_plan['status'],
                ]);
            }
        }

        // Commit the transaction
        DB::commit();

        return response()->json(['message' => 'Product added successfully!'], 201);
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollBack();
        return response()->json(['error' => 'Failed to add product: ' . $e->getMessage()], 500);
    }
}

public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|boolean', // Ensure status is a boolean (1 or 0)
    ]);

    $product = Product::findOrFail($id); // Find the product by ID or fail with 404

    $product->update([
        'status' => $request->status, // Update only the status field
    ]);

    return response()->json([
        'message' => 'Product status updated successfully.',
        'product' => $product
    ], 200);
}




}
