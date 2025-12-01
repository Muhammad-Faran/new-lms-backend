<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationInstallment extends Model
{
    protected $fillable = [
        'application_id', 'amount', 'outstanding', 'due_date', 'status'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

	public function repayment()
	{
	    return $this->hasOne(Repayment::class);
	}

}
