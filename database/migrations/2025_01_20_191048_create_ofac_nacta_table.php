<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOFACNACTATable extends Migration
{
    public function up()
    {
        Schema::create('ofac_nacta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            $table->json('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ofac_nacta');
    }
}
