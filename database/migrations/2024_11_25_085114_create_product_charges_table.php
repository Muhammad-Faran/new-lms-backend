<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_charges', function (Blueprint $table) {
            $table->id(); // Primary key

            // Foreign key referencing products table
            $table->foreignId('product_id')
                  ->constrained('products') // Automatically references 'products' table
                  ->onDelete('cascade'); // Deletes product charges if the associated product is deleted

            // Foreign key referencing charges table
            $table->foreignId('charge_id')  // Changed 'charges_id' to 'charge_id' for consistency
                  ->constrained('charges') // Automatically references 'charges' table
                  ->onDelete('cascade'); // Deletes product charges if the associated charge is deleted

            $table->boolean('apply_fed')->default(false); // Flag to apply FED
            $table->enum('charge_condition', ['installments', 'Requested Amount', 'Percentage of full amount']); // Charge condition

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
        // Drop the 'product_charges' table if it exists
        Schema::dropIfExists('product_charges');
    }
}
