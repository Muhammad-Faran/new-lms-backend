<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class CreditLimit extends Model
{
    use LogsModelChanges;
    
    protected $fillable = [
        'applicant_id',
        'credit_limit',
        'available_limit',
        'status',
        'date_assigned',
    ];

    // One-to-One inverse relationship with applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
