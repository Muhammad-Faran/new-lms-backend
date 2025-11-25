<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'borrower_id' => $this->borrower_id,
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
            'borrower' => $this->borrower ? [
                'id' => $this->borrower->id,
                'first_name' => $this->borrower->first_name,
                'last_name' => $this->borrower->last_name,
                'cnic' => $this->borrower->cnic,
                'mobile_no' => $this->borrower->mobile_no,
                'email' => $this->borrower->email,
                'status' => $this->borrower->status,
            ] : null,
            'charges' => $this->charges->map(function ($charge) {
                return [
                    'id' => $charge->id,
                    'name' => $charge->productCharge && $charge->productCharge->charge ? $charge->productCharge->charge->name : null ,
                    'status' => $charge->status,
                    'transaction_id' => $charge->transaction_id,
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
                    'transaction_id' => $installment->transaction_id,
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
