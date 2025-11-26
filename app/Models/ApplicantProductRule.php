<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class ApplicantProductRule extends Model
{
    use HasFactory, LogsModelChanges;

    protected $fillable = ['applicant_id', 'product_id', 'charge_unit', 'charge_value'];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
