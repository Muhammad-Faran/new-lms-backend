<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditEngineShipperPricing extends Model
{
    protected $table = 'credit_engine_shipper_pricing';
    protected $fillable = ['shipper_id', 'applicant_id', 'data'];
    protected $casts = ['data' => 'array'];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
