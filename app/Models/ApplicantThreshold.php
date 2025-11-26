<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'order_threshold',
        'fixed_threshold_charges',
    ];

    // Relationship with applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
