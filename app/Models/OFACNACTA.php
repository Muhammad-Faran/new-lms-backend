<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;


class OFACNACTA extends Model
{
    use HasFactory, LogsModelChanges;

    protected $table = 'ofac_nacta';

    protected $fillable = [
        'applicant_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array', // Cast 'data' as JSON for easier manipulation
    ];

    /**
     * Relationship with applicant.
     */
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
