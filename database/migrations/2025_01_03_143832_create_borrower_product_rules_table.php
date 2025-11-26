<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicant_product_rules', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('applicant_id'); // Foreign key referencing applicants table
            $table->unsignedBigInteger('product_id'); // Foreign key referencing products table
            $table->enum('charge_unit', ['percentage', 'fixed'])->nullable(); // Enum field for charge unit
            $table->decimal('charge_value', 10, 2)->nullable(); // Custom charge value, nullable
            $table->timestamps(); // created_at and updated_at timestamps

            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->unique(['applicant_id', 'product_id']); // Ensure unique entry for each applicant-product pair
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applicant_product_rules');
    }
};

