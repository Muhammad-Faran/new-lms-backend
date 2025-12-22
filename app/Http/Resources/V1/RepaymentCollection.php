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
                'application_id' => $repayment->application_id,
                'installment_id' => $repayment->installment_id,
                'applicant_id' => $repayment->applicant_id,
                'amount' => $repayment->amount,
                'status' => $repayment->status,
                'paid_at' => $repayment->paid_at,
                'created_at' => $repayment->created_at,
                'updated_at' => $repayment->updated_at,
                'application' => $repayment->application ? [
                    'id' => $repayment->application->id,
                    'product' => $repayment->application->product ? $repayment->application->product->name : null  ,
                    'product_id' => $repayment->application->product ? $repayment->application->product->id : null  ,
                    'product_code' => $repayment->application->product ? $repayment->application->product->code : null  ,
                    'product_plan' => $repayment->application->plan ? $repayment->application->plan->name : null  ,
                    'product_plan_duration' => $repayment->application->plan ? $repayment->application->plan->duration_value : null  ,
                    'product_plan_duration_unit' => $repayment->application->plan ? $repayment->application->plan->duration_unit : null  ,
                    'loan_amount' => $repayment->application->loan_amount,
                    'disbursed_amount' => $repayment->application->disbursed_amount,
                    'total_charges' => $repayment->application->total_charges,
                    'status' => $repayment->application->status,
                    'outstanding_amount' => $repayment->application->outstanding_amount,
                ] : null,
                'installment' => $repayment->installment ? [
                        'id' => $repayment->installment->id,
                        'application_id' => $repayment->installment->application_id,
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
