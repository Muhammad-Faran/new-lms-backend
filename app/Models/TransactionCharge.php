<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCharge extends Model
{
    protected $fillable = [
        'transaction_id', 'product_tier_id', 'product_charge_id', 
        'charge_amount', 'apply_fed', 'fed_amount', 'charge_condition'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function productTier()
    {
        return $this->belongsTo(ProductTier::class);
    }

    public function productCharge()
    {
        return $this->belongsTo(ProductCharge::class);
    }
}
