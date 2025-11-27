<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantsTable extends Migration
{
    public function up()
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('wallet_id', 15)->unique();
            $table->string('mobile_no', 15)->unique();
            $table->string('email', 100)->unique()->nullable();
            $table->string('cnic', 15)->unique();
            $table->string('cnic_front_image')->nullable();
            $table->string('cnic_back_image')->nullable();
            $table->date('cnic_issuance_date')->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 50)->nullable();
            $table->date('dob')->nullable();
            $table->boolean('status')->default(1); // Add the status column with a default value
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('applicants');
    }
}
