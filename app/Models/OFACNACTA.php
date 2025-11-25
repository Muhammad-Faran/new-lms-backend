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
        'shipper_id',
        'borrower_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array', // Cast 'data' as JSON for easier manipulation
    ];

    /**
     * Relationship with Borrower.
     */
    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }
}
