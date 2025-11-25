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
        Schema::create('transaction_installments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('transaction_id');
        $table->decimal('amount', 10, 2);
        $table->decimal('outstanding', 10, 2);
        $table->date('due_date');
        $table->enum('status', ['unpaid', 'paid', 'partial_payment', 'defaulter'])
                  ->default('unpaid'); // Default status of the product
        $table->timestamps();

        $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_installments');
    }
};
