<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromProductTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_tiers', function (Blueprint $table) {
            $table->dropColumn(['down_payment', 'fed_charges_unit', 'fed_charges_value']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_tiers', function (Blueprint $table) {
            $table->boolean('down_payment')->default(false);
            $table->string('fed_charges_unit');
            $table->decimal('fed_charges_value', 10, 2);
        });
    }
}
