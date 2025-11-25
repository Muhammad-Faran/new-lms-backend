<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Name of the loan product
            $table->string('code')->unique(); // Unique code for the loan product
            $table->text('description')->nullable(); // Description of the product
            $table->text('tnc')->nullable(); // Terms and conditions of the loan product
            $table->enum('default_status', ['review', 'approve', 'reject']); // Default status of the product
            $table->integer('max_requested_amount'); // Maximum amount that can be requested
            $table->integer('min_requested_amount'); // Minimum amount that can be requested
            $table->text('disable_loan_on_avail')->nullable(); // Disable loan availability flag
            $table->boolean('status')->default(false); // Active/inactive status
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
        Schema::dropIfExists('products');
    }
}
