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
                'applicant_id' => $repayment->applicant_id,
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
                'applicant' => $repayment->applicant ? [
                    'id' => $repayment->applicant->id,
                    'first_name' => $repayment->applicant->first_name,
                    'last_name' => $repayment->applicant->last_name,
                    'cnic' => $repayment->applicant->cnic,
                    'cnic_issuance_date' => $repayment->applicant->cnic_issuance_date,
                    'wallet_id' => $repayment->applicant->wallet_id,
                    'shipper_id' => $repayment->applicant->shipper_id,
                    'shipper_name' => $repayment->applicant->shipper_name,
                    'father_name' => $repayment->applicant->father_name,
                    'mother_name' => $repayment->applicant->mother_name,
                    'address' => $repayment->applicant->address,
                    'city' => $repayment->applicant->city,
                    'dob' => $repayment->applicant->dob,
                    'mobile_no' => $repayment->applicant->mobile_no,
                    'email' => $repayment->applicant->email,
                    'status' => $repayment->applicant->status,
                ] : null,
            ];
        });
    }
}
