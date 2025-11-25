<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBook extends Model
{
    protected $fillable = ['product_id', 'book_id', 'preference', 'status'];

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with Book
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}

