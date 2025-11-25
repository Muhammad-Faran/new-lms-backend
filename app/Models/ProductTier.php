<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTier extends Model
{
    protected $fillable = [
        'product_id', 'name', 
        'penalty_charges_unit', 'penalty_charges_value','order_threshold','fixed_threshold_charges', 'installment_grace_period', 
        'installment_defaulter_days', 'penalty_type', 'penalty_schedule', 'status'
    ];

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with ProductPlans
    public function productPlans()
    {
        return $this->hasMany(ProductPlan::class);
    }

    // Relationship with ProductTierCharges
    public function productTierCharges()
    {
        return $this->hasMany(ProductTierCharge::class);
    }
}
