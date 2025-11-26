<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('repayments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
        $table->foreignId('installment_id')->constrained('transaction_installments')->onDelete('cascade');
        $table->foreignId('applicant_id')->constrained('applicants')->onDelete('cascade');
        $table->decimal('amount', 15, 2);
        $table->timestamp('paid_at')->nullable();
        $table->string('status')->default('paid'); // 'paid', 'partial', 'failed'
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('repayments');
}

};
