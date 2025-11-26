<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($transaction) {
            return [

                'id' => $transaction->id,
                'applicant_id' => $transaction->applicant_id,
                'product_id' => $transaction->product_id,
                'plan_id' => $transaction->plan_id,
                'loan_amount' => $transaction->loan_amount,
                'total_charges' => $transaction->total_charges,
                'order_amount' => $transaction->order_amount,
                'order_number' => $transaction->order_number,
                'disbursed_amount' => $transaction->disbursed_amount,
                'outstanding_amount' => $transaction->outstanding_amount,
                'status' => $transaction->status,
                'next_due_date' => $transaction->next_due_date,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'applicant' => $transaction->applicant ? [
                    'id' => $transaction->applicant->id,
                    'first_name' => $transaction->applicant->first_name,
                    'last_name' => $transaction->applicant->last_name,
                    'cnic' => $transaction->applicant->cnic,
                    'cnic_issuance_date' => $transaction->applicant->cnic_issuance_date,
                    'cnic_front_image' => $transaction->applicant->cnic_front_image,
                    'cnic_back_image' => $transaction->applicant->cnic_back_image,
                    'wallet_id' => $transaction->applicant->wallet_id,
                    'shipper_id' => $transaction->applicant->shipper_id,
                    'shipper_name' => $transaction->applicant->shipper_name,
                    'father_name' => $transaction->applicant->father_name,
                    'mother_name' => $transaction->applicant->mother_name,
                    'address' => $transaction->applicant->address,
                    'city' => $transaction->applicant->city,
                    'dob' => $transaction->applicant->dob,
                    'mobile_no' => $transaction->applicant->mobile_no,
                    'email' => $transaction->applicant->email,
                    'status' => $transaction->applicant->status,
                ] : null,
                'product' => $transaction->product ? [
                    'id' => $transaction->product->id,
                    'name' => $transaction->product->name,
                    'code' => $transaction->product->code,
                    'status' => $transaction->product->status,
                ] : null,
                'charges' => $transaction->charges->map(function ($charge) {
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
                'installments' => $transaction->installments->map(function ($installment) {
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
        });
    }
}
