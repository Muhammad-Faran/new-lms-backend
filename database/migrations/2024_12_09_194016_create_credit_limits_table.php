<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditLimitsTable extends Migration
{
    public function up()
    {
        Schema::create('credit_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id')->unique(); // One-to-one relationship
            $table->decimal('credit_limit', 15, 2); // Total credit limit
            $table->decimal('available_limit', 15, 2); // Available credit limit
            $table->enum('status', ['active', 'inactive']); // Limit status
            $table->date('date_assigned'); // Date assigned
            $table->timestamps();

            // Foreign key relationship
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_limits');
    }
}

