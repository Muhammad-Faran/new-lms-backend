<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'transaction_installment_id',
        'amount',
        'type',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function transactionInstallment()
    {
        return $this->belongsTo(TransactionInstallment::class);
    }
}
