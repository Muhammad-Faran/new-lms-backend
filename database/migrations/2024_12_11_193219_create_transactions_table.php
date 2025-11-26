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
        Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('applicant_id');
        $table->unsignedBigInteger('product_id');
        $table->unsignedBigInteger('plan_id');
        $table->decimal('loan_amount', 10, 2);
        $table->decimal('total_charges', 10, 2);
        $table->decimal('disbursed_amount', 10, 2);
        $table->decimal('outstanding_amount', 10, 2);
        $table->enum('status', ['review','approve','reject','disbursed','completed','cancelled','reversed','hold']); // Default status of the product
        $table->timestamps();

        $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        $table->foreign('plan_id')->references('id')->on('product_plans')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
