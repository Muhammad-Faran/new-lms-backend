<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateApplicationChargesTableAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_charges', function (Blueprint $table) {
            $table->enum('status', ['paid', 'unpaid'])
                  ->default('paid')
                  ->after('charge_condition'); // Add the column after 'charge_condition'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_charges', function (Blueprint $table) {
            $table->dropColumn('status'); // Remove the 'status' column
        });
    }
}
