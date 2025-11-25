<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTierChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_tier_charges', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('product_charge_id')->constrained('product_charges')->onDelete('cascade'); // Foreign key referencing product_charges table
            $table->foreignId('product_tier_id')->constrained('product_tiers')->onDelete('cascade'); // Foreign key referencing product_tiers table
            $table->string('charges_unit'); // Unit of the charge (e.g., percentage, fixed)
            $table->double('charges_value', 15, 6); // Value of the charge with precision
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
        Schema::dropIfExists('product_tier_charges');
    }
}
