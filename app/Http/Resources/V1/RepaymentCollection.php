<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RepaymentCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($repayment) {
            return [
                'id' => $repayment->id,
                'transaction_id' => $repayment->transaction_id,
                'installment_id' => $repayment->installment_id,
                'borrower_id' => $repayment->borrower_id,
                'amount' => $repayment->amount,
                'status' => $repayment->status,
                'paid_at' => $repayment->paid_at,
                'created_at' => $repayment->created_at,
                'updated_at' => $repayment->updated_at,
                'transaction' => $repayment->transaction ? [
                    'id' => $repayment->transaction->id,
                    'product' => $repayment->transaction->product ? $repayment->transaction->product->name : null  ,
                    'product_id' => $repayment->transaction->product ? $repayment->transaction->product->id : null  ,
                    'product_code' => $repayment->transaction->product ? $repayment->transaction->product->code : null  ,
                    'product_plan' => $repayment->transaction->plan ? $repayment->transaction->plan->name : null  ,
                    'product_plan_duration' => $repayment->transaction->plan ? $repayment->transaction->plan->duration_value : null  ,
                    'product_plan_duration_unit' => $repayment->transaction->plan ? $repayment->transaction->plan->duration_unit : null  ,
                    'order_number' => $repayment->transaction->order_number,
                    'loan_amount' => $repayment->transaction->loan_amount,
                    'disbursed_amount' => $repayment->transaction->disbursed_amount,
                    'total_charges' => $repayment->transaction->total_charges,
                    'status' => $repayment->transaction->status,
                    'outstanding_amount' => $repayment->transaction->outstanding_amount,
                ] : null,
                'installment' => $repayment->installment ? [
                        'id' => $repayment->installment->id,
                        'transaction_id' => $repayment->installment->transaction_id,
                        'amount' => $repayment->installment->amount,
                        'outstanding' => $repayment->installment->outstanding,
                        'due_date' => $repayment->installment->due_date,
                        'status' => $repayment->installment->status,
                        'created_at' => $repayment->installment->created_at,
                        'updated_at' => $repayment->installment->updated_at,
                ] : null,
                'borrower' => $repayment->borrower ? [
                    'id' => $repayment->borrower->id,
                    'first_name' => $repayment->borrower->first_name,
                    'last_name' => $repayment->borrower->last_name,
                    'cnic' => $repayment->borrower->cnic,
                    'cnic_issuance_date' => $repayment->borrower->cnic_issuance_date,
                    'wallet_id' => $repayment->borrower->wallet_id,
                    'shipper_id' => $repayment->borrower->shipper_id,
                    'shipper_name' => $repayment->borrower->shipper_name,
                    'father_name' => $repayment->borrower->father_name,
                    'mother_name' => $repayment->borrower->mother_name,
                    'address' => $repayment->borrower->address,
                    'city' => $repayment->borrower->city,
                    'dob' => $repayment->borrower->dob,
                    'mobile_no' => $repayment->borrower->mobile_no,
                    'email' => $repayment->borrower->email,
                    'status' => $repayment->borrower->status,
                ] : null,
            ];
        });
    }
}
