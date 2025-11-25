<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFedInclusiveToProductTierChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_tier_charges', function (Blueprint $table) {
            $table->boolean('is_fed_inclusive')->after('charges_value')->default(false); // Adding the new column with default value
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_tier_charges', function (Blueprint $table) {
            $table->dropColumn('is_fed_inclusive'); // Dropping the column
        });
    }
}
