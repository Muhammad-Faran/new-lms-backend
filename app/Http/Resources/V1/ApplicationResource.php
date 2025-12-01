<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'applicant_id' => $this->applicant_id,
            'product_id' => $this->product_id,
            'plan_id' => $this->plan_id,
            'loan_amount' => $this->loan_amount,
            'total_charges' => $this->total_charges,
            'disbursed_amount' => $this->disbursed_amount,
            'status' => $this->status,
            'order_amount' => $this->order_amount,
            'order_number' => $this->order_number,
            'outstanding_amount' => $this->outstanding_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'applicant' => $this->applicant ? [
                'id' => $this->applicant->id,
                'first_name' => $this->applicant->first_name,
                'last_name' => $this->applicant->last_name,
                'cnic' => $this->applicant->cnic,
                'mobile_no' => $this->applicant->mobile_no,
                'email' => $this->applicant->email,
                'status' => $this->applicant->status,
            ] : null,
            'charges' => $this->charges->map(function ($charge) {
                return [
                    'id' => $charge->id,
                    'name' => $charge->productCharge && $charge->productCharge->charge ? $charge->productCharge->charge->name : null ,
                    'status' => $charge->status,
                    'application_id' => $charge->application_id,
                    'product_tier_id' => $charge->product_tier_id,
                    'product_charge_id' => $charge->product_charge_id,
                    'charge_amount' => $charge->charge_amount,
                    'apply_fed' => $charge->apply_fed,
                    'fed_amount' => $charge->fed_amount,
                    'charge_condition' => $charge->charge_condition,
                    'created_at' => $charge->created_at,
                    'updated_at' => $charge->updated_at,
                ];
            }),
            'installments' => $this->installments->map(function ($installment) {
                return [
                    'id' => $installment->id,
                    'application_id' => $installment->application_id,
                    'amount' => $installment->amount,
                    'outstanding' => $installment->outstanding,
                    'due_date' => $installment->due_date,
                    'status' => $installment->status,
                    'created_at' => $installment->created_at,
                    'updated_at' => $installment->updated_at,
                ];
            }),
        ];
    }
}
