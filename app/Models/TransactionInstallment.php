<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionInstallment extends Model
{
    protected $fillable = [
        'transaction_id', 'amount', 'outstanding', 'due_date', 'status'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

	public function repayment()
	{
	    return $this->hasOne(Repayment::class);
	}

}
