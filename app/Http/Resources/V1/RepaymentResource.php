<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class RepaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'installment_id' => $this->installment_id,
            'applicant_id' => $this->applicant_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'transaction' => $this->transaction ? [
                'id' => $this->transaction->id,
                'loan_amount' => $this->transaction->loan_amount,
                'outstanding_amount' => $this->transaction->outstanding_amount,
            ] : null,
            'applicant' => $this->applicant ? [
                'id' => $this->applicant->id,
                'first_name' => $this->applicant->first_name,
                'last_name' => $this->applicant->last_name,
                'email' => $this->applicant->email,
            ] : null,
        ];
    }
}
