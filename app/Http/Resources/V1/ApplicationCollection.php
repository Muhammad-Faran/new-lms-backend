<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ApplicationCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($application) {
            return [

                'id' => $application->id,
                'applicant_id' => $application->applicant_id,
                'product_id' => $application->product_id,
                'plan_id' => $application->plan_id,
                'loan_amount' => $application->loan_amount,
                'total_charges' => $application->total_charges,
                'order_amount' => $application->order_amount,
                'order_number' => $application->order_number,
                'disbursed_amount' => $application->disbursed_amount,
                'outstanding_amount' => $application->outstanding_amount,
                'status' => $application->status,
                'next_due_date' => $application->next_due_date,
                'created_at' => $application->created_at,
                'updated_at' => $application->updated_at,
                'applicant' => $application->applicant ? [
                    'id' => $application->applicant->id,
                    'first_name' => $application->applicant->first_name,
                    'last_name' => $application->applicant->last_name,
                    'cnic' => $application->applicant->cnic,
                    'cnic_issuance_date' => $application->applicant->cnic_issuance_date,
                    'cnic_front_image' => $application->applicant->cnic_front_image,
                    'cnic_back_image' => $application->applicant->cnic_back_image,
                    'wallet_id' => $application->applicant->wallet_id,
                    'shipper_id' => $application->applicant->shipper_id,
                    'shipper_name' => $application->applicant->shipper_name,
                    'father_name' => $application->applicant->father_name,
                    'mother_name' => $application->applicant->mother_name,
                    'address' => $application->applicant->address,
                    'city' => $application->applicant->city,
                    'dob' => $application->applicant->dob,
                    'mobile_no' => $application->applicant->mobile_no,
                    'email' => $application->applicant->email,
                    'status' => $application->applicant->status,
                ] : null,
                'product' => $application->product ? [
                    'id' => $application->product->id,
                    'name' => $application->product->name,
                    'code' => $application->product->code,
                    'status' => $application->product->status,
                ] : null,
                'charges' => $application->charges->map(function ($charge) {
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
                'installments' => $application->installments->map(function ($installment) {
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
        });
    }
}
