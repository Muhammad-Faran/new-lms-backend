<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
{
    return $this->collection->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'description' => $product->description,
            'tnc' => $product->tnc,
            'default_status' => $product->default_status,
            'max_requested_amount' => $product->max_requested_amount,
            'eligibility_criteria' => $product->eligibility_criteria,
            'min_requested_amount' => $product->min_requested_amount,
            'disable_loan_on_avail' => $product->disable_loan_on_avail,
            'status' => $product->status,
            'product_books' => $product->productBooks->map(function ($book) {
                return [
                    'id' => $book->id,
                    'name' => $book->book->name,
                    'preference' => $book->preference,
                    'status' => $book->status,
                    'max_allowed_amount' => $book->book->max_allowed_amount,
                    'min_allowed_amount' => $book->book->min_allowed_amount,
                ];
            }),
            'product_plans' => $product->productPlans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'duration_unit' => $plan->duration_unit,
                    'duration_value' => $plan->duration_value,
                    'status' => $plan->status,
                    'product_tier' => [
                        'id' => $plan->productTier->id,
                        'name' => $plan->productTier->name,
                        'fed_charges_unit' => $plan->productTier->fed_charges_unit,
                        'fed_charges_value' => $plan->productTier->fed_charges_value,
                        'penalty_charges_unit' => $plan->productTier->penalty_charges_unit,
                        'penalty_charges_value' => $plan->productTier->penalty_charges_value,
                        'installment_grace_period' => $plan->productTier->installment_grace_period,
                        'installment_defaulter_days' => $plan->productTier->installment_defaulter_days,
                        'penalty_type' => $plan->productTier->penalty_type,
                        'penalty_schedule' => $plan->productTier->penalty_schedule,
                        'status' => $plan->productTier->status,
                    ],
                ];
            }),
            'product_tiers' => $product->productTiers->map(function ($tier) {
                return [
                    'id' => $tier->id,
                    'name' => $tier->name,
                    'fed_charges_unit' => $tier->fed_charges_unit,
                    'fed_charges_value' => $tier->fed_charges_value,
                    'penalty_charges_unit' => $tier->penalty_charges_unit,
                    'penalty_charges_value' => $tier->penalty_charges_value,
                    'installment_grace_period' => $tier->installment_grace_period,
                    'installment_defaulter_days' => $tier->installment_defaulter_days,
                    'penalty_type' => $tier->penalty_type,
                    'penalty_schedule' => $tier->penalty_schedule,
                    'status' => $tier->status,
                    'product_tier_charges' => $tier->productTierCharges->map(function ($tierCharge) {
                        return [
                            'id' => $tierCharge->id,
                            'is_fed_inclusive' => $tierCharge->is_fed_inclusive,
                            'charges_unit' => $tierCharge->charges_unit,
                            'charges_value' => $tierCharge->charges_value,
                            'charge' => optional($tierCharge->productCharge->charge)->name,
                        ];
                    }),
                ];
            }),
            'product_charges' => $product->productCharges->map(function ($charge) {
                return [
                    'id' => $charge->id,
                    'charge_name' => optional($charge->charge)->name,
                    'fed_charges_unit' => $charge->fed_charges_unit,
                    'fed_charges_value' => $charge->fed_charges_value,
                    'apply_fed' => $charge->apply_fed,
                    'charge_condition' => $charge->charge_condition,
                ];
            }),
        ];
    })->toArray();
}

}

