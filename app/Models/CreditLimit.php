<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class CreditLimit extends Model
{
    use LogsModelChanges;
    
    protected $fillable = [
        'borrower_id',
        'credit_limit',
        'available_limit',
        'status',
        'date_assigned',
    ];

    // One-to-One inverse relationship with Borrower
    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }
}
