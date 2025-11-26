<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditEngineShipperCreditScoreTable extends Migration
{
    public function up()
    {
        Schema::create('credit_engine_shipper_credit_score', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipper_id')->unique();
            $table->unsignedBigInteger('applicant_id');
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            $table->json('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_engine_shipper_credit_score');
    }
}

