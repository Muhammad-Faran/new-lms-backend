<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class BorrowerProductRule extends Model
{
    use HasFactory, LogsModelChanges;

    protected $fillable = ['borrower_id', 'product_id', 'charge_unit', 'charge_value'];

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
