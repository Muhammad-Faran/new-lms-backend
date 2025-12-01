<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'application_installment_id',
        'amount',
        'type',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function applicationInstallment()
    {
        return $this->belongsTo(ApplicationInstallment::class);
    }
}
