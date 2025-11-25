<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCharge extends Model
{
    protected $fillable = ['product_id', 'charge_id', 'apply_fed','fed_charges_unit','fed_charges_value', 'charge_condition'];

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with Charge
    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }
}

