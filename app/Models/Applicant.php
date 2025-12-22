<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class Applicant extends Model
{
    use HasFactory, LogsModelChanges;

    protected $fillable = [
        'first_name',
        'last_name',
        'cnic',
        'cnic_front_image',
        'cnic_back_image',
        'cnic_issuance_date',
        'mobile_no',
        'email',
        'father_name',
        'mother_name',
        'address',
        'city',
        'dob',
        'status'
    ];

     public function products()
    {
        return $this->belongsToMany(Product::class, 'applicant_products')->withTimestamps();
    }

    public function applicantThreshold()
    {
        return $this->hasOne(ApplicantThreshold::class);
    }

     public function productRules()
    {
        return $this->hasMany(ApplicantProductRule::class);
    }

     public function creditLimit()
    {
        return $this->hasOne(CreditLimit::class);
    }

    public function financingPolicy()
    {
        return $this->hasOne(ApplicantFinancingPolicy::class);
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 1; // Returns true if active, false if inactive
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    public function ofacNacta()
    {
        return $this->hasOne(OFACNACTA::class, 'applicant_id', 'id');
    }


}
