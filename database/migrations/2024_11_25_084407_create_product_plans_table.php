<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_plans', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Foreign key referencing products table
            $table->foreignId('product_tier_id')->constrained('product_tiers')->onDelete('cascade'); // Foreign key referencing product_tiers table
            $table->string('name'); // Name of the plan
            $table->string('duration_unit'); // Duration unit (e.g., days, weeks, months, years)
            $table->integer('duration_value'); // Duration value (e.g., 30, 6, 12)
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
        Schema::dropIfExists('product_plans');
    }
}
