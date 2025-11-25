<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('borrower_thresholds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('borrower_id')->unique(); // One-to-One relationship
            $table->decimal('order_threshold', 15, 2)->nullable(); // Order threshold
            $table->decimal('fixed_threshold_charges', 15, 2)->nullable(); // Fixed threshold charges
            $table->timestamps();

            // Foreign key relationship
            $table->foreign('borrower_id')->references('id')->on('borrowers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('borrower_thresholds');
    }
};
