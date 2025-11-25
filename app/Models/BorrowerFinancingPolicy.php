<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class BorrowerFinancingPolicy extends Model
{
	use LogsModelChanges;
	
    protected $fillable = ['borrower_id', 'financing_percentage'];

    // Relationship with Borrower
    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }
}
