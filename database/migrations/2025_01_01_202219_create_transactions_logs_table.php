<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsLogsTable extends Migration
{
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('cascade');
            $table->foreignId('transaction_installment_id')->nullable()->constrained('transaction_installments')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['disbursement', 'collection' ,'charges', 'fed']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_logs');
    }
}
