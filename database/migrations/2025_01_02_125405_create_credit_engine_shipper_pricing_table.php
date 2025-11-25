<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditEngineShipperPricingTable extends Migration
{
    public function up()
    {
        Schema::create('credit_engine_shipper_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipper_id')->unique();
            $table->unsignedBigInteger('borrower_id');
            $table->foreign('borrower_id')->references('id')->on('borrowers')->onDelete('cascade');
            $table->json('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_engine_shipper_pricing');
    }
}
