<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_books', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Foreign key referencing products table
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade'); // Foreign key referencing books table
            $table->string('preference'); // Preference for the product-book relation
            $table->boolean('status')->default(true); // Status of the relation (active/inactive), default is true
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_books');
    }
}
