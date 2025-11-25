<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBorrowerProductsTable extends Migration
{
    public function up()
    {
        Schema::create('borrower_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('borrower_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('borrower_id')->references('id')->on('borrowers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Ensure no duplicate borrower-product combinations
            $table->unique(['borrower_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('borrower_products');
    }
}
