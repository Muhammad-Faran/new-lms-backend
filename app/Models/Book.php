<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelChanges;

class Book extends Model
{
	use LogsModelChanges;

    protected $fillable = ['name', 'max_allowed_amount', 'min_allowed_amount', 'status'];

    // Relationship with ProductBooks
    public function productBooks()
    {
        return $this->hasMany(ProductBook::class);
    }
}

