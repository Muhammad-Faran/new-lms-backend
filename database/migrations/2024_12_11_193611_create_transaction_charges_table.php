<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_charges', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('transaction_id');
        $table->unsignedBigInteger('product_tier_id');
        $table->unsignedBigInteger('product_charge_id');
        $table->decimal('charge_amount', 10, 2);
        $table->boolean('apply_fed')->default(false);
        $table->decimal('fed_amount', 10, 2)->default(0);
        $table->string('charge_condition')->nullable();
        $table->timestamps();

        $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        $table->foreign('product_tier_id')->references('id')->on('product_tiers')->onDelete('cascade');
        $table->foreign('product_charge_id')->references('id')->on('product_charges')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_charges');
    }
};
