<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;


class OverdueLoanCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($transaction) {
             $overdueInstallment = $transaction->installments
                ->where('due_date', '<', now())
                ->where('status', 'unpaid')
                ->sortBy('due_date')
                ->first();

            $dueDate = optional($overdueInstallment)->due_date;

            return [
                'id' => $transaction->id,
                'borrower_id' => $transaction->borrower_id,
                'product_id' => $transaction->product_id,
                'plan_id' => $transaction->plan_id,
                'loan_amount' => $transaction->loan_amount,
                'total_charges' => $transaction->total_charges,
                'order_amount' => $transaction->order_amount,
                'order_number' => $transaction->order_number,
                'disbursed_amount' => $transaction->disbursed_amount,
                'outstanding_amount' => $transaction->outstanding_amount,
                'status' => $transaction->status,
                'days_overdue' => abs($dueDate
                    ? now()->setTimezone('Asia/Karachi')->startOfDay()->diffInDays(
                        Carbon::parse($dueDate)->setTimezone('Asia/Karachi')->startOfDay()
                    )
                    : null),
                'next_due_date' => $transaction->next_due_date,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'borrower' => $transaction->borrower ? [
                    'id' => $transaction->borrower->id,
                    'first_name' => $transaction->borrower->first_name,
                    'last_name' => $transaction->borrower->last_name,
                    'cnic' => $transaction->borrower->cnic,
                    'cnic_issuance_date' => $transaction->borrower->cnic_issuance_date,
                    'cnic_front_image' => $transaction->borrower->cnic_front_image,
                    'cnic_back_image' => $transaction->borrower->cnic_back_image,
                    'wallet_id' => $transaction->borrower->wallet_id,
                    'shipper_id' => $transaction->borrower->shipper_id,
                    'shipper_name' => $transaction->borrower->shipper_name,
                    'father_name' => $transaction->borrower->father_name,
                    'mother_name' => $transaction->borrower->mother_name,
                    'address' => $transaction->borrower->address,
                    'city' => $transaction->borrower->city,
                    'dob' => $transaction->borrower->dob,
                    'mobile_no' => $transaction->borrower->mobile_no,
                    'email' => $transaction->borrower->email,
                    'status' => $transaction->borrower->status,
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
