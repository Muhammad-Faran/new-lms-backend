<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsLogsTable extends Migration
{
    public function up()
    {
        Schema::create('application_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->nullable()->constrained('applications')->onDelete('cascade');
            $table->foreignId('application_installment_id')->nullable()->constrained('application_installments')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['disbursement', 'collection' ,'charges', 'fed']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('application_logs');
    }
}
