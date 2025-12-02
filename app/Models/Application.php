<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'applicant_id', 'product_id', 'plan_id', 'loan_amount', 
        'total_charges', 'outstanding_amount','disbursed_amount', 'status'
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function plan()
    {
        return $this->belongsTo(ProductPlan::class);
    }

    public function charges()
    {
        return $this->hasMany(ApplicationCharge::class);
    }

    public function installments()
    {
        return $this->hasMany(ApplicationInstallment::class);
    }

    public function getNextDueDateAttribute()
    {
        $unpaidInstallment = $this->installments()
            ->where('status', 'unpaid') // Adjust if using 'pending' or other status
            ->orderBy('due_date', 'asc')
            ->first();

        return $unpaidInstallment ? $unpaidInstallment->due_date : null;
    }

    
}
