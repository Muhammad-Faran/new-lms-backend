<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class Charge extends Model
{
    use LogsModelChanges;
    
    protected $fillable = ['name'];

    // Relationship with ProductCharges
    public function productCharges()
    {
        return $this->hasMany(ProductCharge::class);
    }

    // Relationship with ProductTierCharges (via ProductCharges)
    public function productTierCharges()
    {
        return $this->hasManyThrough(ProductTierCharge::class, ProductCharge::class, 'charge_id', 'product_charge_id');
    }
}
