<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('applicant_thresholds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id')->unique(); // One-to-One relationship
            $table->decimal('order_threshold', 15, 2)->nullable(); // Order threshold
            $table->decimal('fixed_threshold_charges', 15, 2)->nullable(); // Fixed threshold charges
            $table->timestamps();

            // Foreign key relationship
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('applicant_thresholds');
    }
};
