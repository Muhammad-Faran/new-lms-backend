<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPlan extends Model
{
    protected $fillable = ['product_id', 'product_tier_id', 'name', 'duration_unit', 'duration_value', 'status'];

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with ProductTier
    public function productTier()
    {
        return $this->belongsTo(ProductTier::class);
    }
}

