<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ApplicantCollection extends ResourceCollection
{
    public function toArray($request)
{
    return $this->collection->map(function ($applicant) {
        return [
            'id' => $applicant->id,
            'first_name' => $applicant->first_name,
            'last_name' => $applicant->last_name,
            'cnic' => $applicant->cnic,
            'wallet_id' => $applicant->wallet_id,
            'cnic_front_image' => $applicant->cnic_front_image,
            'cnic_back_image' => $applicant->cnic_back_image,
            'cnic_issuance_date' => $applicant->cnic_issuance_date,
            'mobile_no' => $applicant->mobile_no,
            'email' => $applicant->email,
            'father_name' => $applicant->father_name,
            'mother_name' => $applicant->mother_name,
            'address' => $applicant->address,
            'city' => $applicant->city,
            'dob' => $applicant->dob,
            'status' => $applicant->status,
            'created_at' => $applicant->created_at,
            'updated_at' => $applicant->updated_at,
            'products' => $applicant->products->map(function ($product) use ($applicant) {
                $applicantRule = $applicant->productRules->firstWhere('product_id', $product->id);

                $productTier = $product->productTiers->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'charge_unit' => $applicantRule?->charge_unit,
                    'charge_value' => $applicantRule?->charge_value,
                    'order_threshold' => $productTier?->order_threshold,
                    'fixed_threshold_charges' => $productTier?->fixed_threshold_charges,
                ];
            }),
            'credit_limit' => $applicant->creditLimit ? [
                'id' => $applicant->creditLimit->id,
                'credit_limit' => $applicant->creditLimit->credit_limit,
                'available_limit' => $applicant->creditLimit->available_limit,
                'status' => $applicant->creditLimit->status,
                'date_assigned' => $applicant->creditLimit->date_assigned,
            ] : null,
            'financing_policy' => $applicant->financingPolicy ? [
                'id' => $applicant->financingPolicy->id,
                'financing_percentage' => $applicant->financingPolicy->financing_percentage,
            ] : null,
             'applicant_threshold' => $applicant->applicantThreshold ? [
                'order_threshold' => $applicant->applicantThreshold->order_threshold,
                'fixed_threshold_charges' => $applicant->applicantThreshold->fixed_threshold_charges,
            ] : null,
        ];
    });
}

}

