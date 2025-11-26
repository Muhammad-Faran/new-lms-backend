<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class Product extends Model
{
    use LogsModelChanges;
    
    protected $fillable = [
        'name', 'code', 'tnc', 'description', 'max_requested_amount', 
        'min_requested_amount','per_user_availability', 'eligibility_criteria' ,'disable_loan_on_avail', 'default_status', 'status'
    ];

    // Relationship with ProductBooks
    public function productBooks()
    {
        return $this->hasMany(ProductBook::class);
    }

    // Relationship with ProductPlans
    public function productPlans()
    {
        return $this->hasMany(ProductPlan::class);
    }

    // Relationship with ProductTiers
    public function productTiers()
    {
        return $this->hasMany(ProductTier::class);
    }

    // Relationship with ProductCharges
    public function productCharges()
    {
        return $this->hasMany(ProductCharge::class);
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 1; // Returns true if active, false if inactive
    }

     public function applicants()
    {
        return $this->belongsToMany(Applicant::class, 'applicant_products');
    }
}
