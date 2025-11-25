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
            $table->unsignedBigInteger('shipper_id');
            $table->unsignedBigInteger('borrower_id');
            $table->foreign('borrower_id')->references('id')->on('borrowers')->onDelete('cascade');
            $table->json('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ofac_nacta');
    }
}
