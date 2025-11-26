<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class ApplicantFinancingPolicy extends Model
{
	use LogsModelChanges;
	
    protected $fillable = ['applicant_id', 'financing_percentage'];

    // Relationship with applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
