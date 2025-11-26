<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'cnic' => $this->cnic,
            'mobile_no' => $this->mobile_no,
            'shipper_id' => $this->shipper_id,
            'shipper_name' => $this->shipper_name,
            'wallet_id' => $this->wallet_id,
            'email' => $this->email,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'address' => $this->address,
            'city' => $this->city,
            'dob' => $this->dob,
            'status' => $this->status,
            'cnic_front_image' => $this->cnic_front_image,
            'cnic_back_image' => $this->cnic_back_image,
            'cnic_issuance_date' => $this->cnic_issuance_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => $this->products->map(function ($product) {
                $applicantRule = $this->productRules->firstWhere('product_id', $product->id);
                $productTier = $product->productTiers->first(); // Assuming you want the first product tier

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'eligibility_criteria' => $product->eligibility_criteria,
                    'charge_unit' => $applicantRule?->charge_unit,
                    'charge_value' => $applicantRule?->charge_value,
                    'order_threshold' => $this->applicantThreshold?->order_threshold ?? $productTier?->order_threshold,
                    'fixed_threshold_charges' => $this->applicantThreshold?->fixed_threshold_charges ?? $productTier?->fixed_threshold_charges,

                    'plans' => $product->productPlans->map(function ($plan) {
                        return [
                            'id' => $plan->id,
                            'name' => $plan->name,
                            'duration_unit' => $plan->duration_unit,
                            'duration_value' => $plan->duration_value,
                            'status' => $plan->status,
                            'charges' => $plan->productTier->productTierCharges->map(function ($charge) {
                                return [
                                    'charges_unit' => $charge->charges_unit,
                                    'charges_value' => $charge->charges_value,
                                    'is_fed_inclusive' => $charge->is_fed_inclusive,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
            'credit_limit' => $this->creditLimit ? [
                'id' => $this->creditLimit->id,
                'credit_limit' => $this->creditLimit->credit_limit,
                'available_limit' => $this->creditLimit->available_limit,
                'status' => $this->creditLimit->status,
                'date_assigned' => $this->creditLimit->date_assigned,
            ] : null,
            'financing_policy' => $this->financingPolicy ? [
                'id' => $this->financingPolicy->id,
                'financing_percentage' => $this->financingPolicy->financing_percentage,
            ] : null,
        ];
    }
}
