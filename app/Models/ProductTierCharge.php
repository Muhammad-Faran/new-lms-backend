<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTierCharge extends Model
{
    protected $fillable = ['product_tier_id', 'product_charge_id', 'charges_unit','is_fed_inclusive', 'charges_value'];

    // Relationship with ProductTier
    public function productTier()
    {
        return $this->belongsTo(ProductTier::class);
    }

    // Relationship with ProductCharge
    public function productCharge()
    {
        return $this->belongsTo(ProductCharge::class);
    }
}
