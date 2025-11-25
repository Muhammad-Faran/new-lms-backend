<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditEngineShipperCreditScore extends Model
{
    protected $table = 'credit_engine_shipper_credit_score';
    protected $fillable = ['shipper_id', 'borrower_id', 'data'];
    protected $casts = ['data' => 'array'];

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }
}
