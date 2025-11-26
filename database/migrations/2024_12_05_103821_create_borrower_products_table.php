<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantProductsTable extends Migration
{
    public function up()
    {
        Schema::create('applicant_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Ensure no duplicate applicant-product combinations
            $table->unique(['applicant_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('applicant_products');
    }
}
