<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowerThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_id',
        'order_threshold',
        'fixed_threshold_charges',
    ];

    // Relationship with Borrower
    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }
}
