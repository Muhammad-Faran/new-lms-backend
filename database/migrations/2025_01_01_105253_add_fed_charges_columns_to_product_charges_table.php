<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFedChargesColumnsToProductChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_charges', function (Blueprint $table) {
            $table->string('fed_charges_unit')->after('apply_fed');
            $table->decimal('fed_charges_value', 10, 2)->after('fed_charges_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_charges', function (Blueprint $table) {
            $table->dropColumn(['fed_charges_unit', 'fed_charges_value']);
        });
    }
}
