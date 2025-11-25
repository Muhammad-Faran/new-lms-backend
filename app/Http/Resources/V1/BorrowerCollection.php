<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BorrowerCollection extends ResourceCollection
{
    public function toArray($request)
{
    return $this->collection->map(function ($borrower) {
        return [
            'id' => $borrower->id,
            'first_name' => $borrower->first_name,
            'last_name' => $borrower->last_name,
            'cnic' => $borrower->cnic,
            'wallet_id' => $borrower->wallet_id,
            'shipper_id' => $borrower->shipper_id,
            'shipper_name' => $borrower->shipper_name,
            'cnic_front_image' => $borrower->cnic_front_image,
            'cnic_back_image' => $borrower->cnic_back_image,
            'cnic_issuance_date' => $borrower->cnic_issuance_date,
            'mobile_no' => $borrower->mobile_no,
            'email' => $borrower->email,
            'father_name' => $borrower->father_name,
            'mother_name' => $borrower->mother_name,
            'address' => $borrower->address,
            'city' => $borrower->city,
            'dob' => $borrower->dob,
            'status' => $borrower->status,
            'created_at' => $borrower->created_at,
            'updated_at' => $borrower->updated_at,
            'products' => $borrower->products->map(function ($product) use ($borrower) {
                $borrowerRule = $borrower->productRules->firstWhere('product_id', $product->id);

                $productTier = $product->productTiers->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'charge_unit' => $borrowerRule?->charge_unit,
                    'charge_value' => $borrowerRule?->charge_value,
                    'order_threshold' => $productTier?->order_threshold,
                    'fixed_threshold_charges' => $productTier?->fixed_threshold_charges,
                ];
            }),
            'credit_limit' => $borrower->creditLimit ? [
                'id' => $borrower->creditLimit->id,
                'credit_limit' => $borrower->creditLimit->credit_limit,
                'available_limit' => $borrower->creditLimit->available_limit,
                'status' => $borrower->creditLimit->status,
                'date_assigned' => $borrower->creditLimit->date_assigned,
            ] : null,
            'financing_policy' => $borrower->financingPolicy ? [
                'id' => $borrower->financingPolicy->id,
                'financing_percentage' => $borrower->financingPolicy->financing_percentage,
            ] : null,
             'credit_engine_data' => [
                'ofac_nacta' => $borrower->ofacNacta ? $borrower->ofacNacta->data : null,
                'shipper_info' => $borrower->creditEngineShipperInfo ? $borrower->creditEngineShipperInfo->data : null,
                'shipper_kyc' => $borrower->creditEngineShipperKyc ? $borrower->creditEngineShipperKyc->data : null,
                'shipper_pricing' => $borrower->creditEngineShipperPricing ? $borrower->creditEngineShipperPricing->data : null,
                'shipper_credit_score' => $borrower->creditEngineShipperCreditScore ? $borrower->creditEngineShipperCreditScore->data : null,
            ],
             'borrower_threshold' => $borrower->borrowerThreshold ? [
                'order_threshold' => $borrower->borrowerThreshold->order_threshold,
                'fixed_threshold_charges' => $borrower->borrowerThreshold->fixed_threshold_charges,
            ] : null,
        ];
    });
}

}

