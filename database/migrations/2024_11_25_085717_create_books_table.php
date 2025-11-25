<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Name of the book
            $table->boolean('status')->default(true); // Status of the book (active/inactive), default is true
            $table->integer('max_allowed_amount'); // Maximum requested amount
            $table->integer('min_allowed_amount'); // Minimum requested amount
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
        Schema::dropIfExists('books');
    }
}
