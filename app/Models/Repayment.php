<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    protected $fillable = [
        'transaction_id',
        'installment_id',
        'applicant_id',
        'amount',
        'paid_at',
        'status',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function installment()
    {
        return $this->belongsTo(TransactionInstallment::class);
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}

