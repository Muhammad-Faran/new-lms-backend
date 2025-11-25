<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_tiers', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Foreign key referencing products table
            $table->string('name'); // Name of the tiers
            $table->string('fed_charges_unit'); // Unit for FED charges (e.g., percentage, flat)
            $table->decimal('fed_charges_value', 10, 2); // Value for FED charges
            $table->string('penalty_charges_unit'); // Unit for penalty charges (e.g., percentage, flat)
            $table->decimal('penalty_charges_value', 10, 2); // Value for penalty charges
            $table->integer('installment_grace_period'); // Grace period for installments in days
            $table->integer('installment_defaulter_days'); // Days after which a customer is considered a defaulter
            $table->string('penalty_type')->nullable(); // Type of penalty (e.g., fixed, recurring)
            $table->string('penalty_schedule')->nullable(); // Schedule for penalties (e.g., daily, weekly, monthly)
            $table->boolean('down_payment')->default(false); // Down payment amount
            $table->boolean('status')->default(true); // Active/inactive status
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
        Schema::dropIfExists('product_tiers');
    }
}
