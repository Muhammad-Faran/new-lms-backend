<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderThresholdAndFixedThresholdChargesToProductTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_tiers', function (Blueprint $table) {
            $table->decimal('order_threshold', 15, 2)->nullable()->after('penalty_schedule')->comment('The threshold for the order amount');
            $table->decimal('fixed_threshold_charges', 15, 2)->nullable()->after('order_threshold')->comment('Fixed charges applied when order amount is within threshold');
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
            $table->dropColumn(['order_threshold', 'fixed_threshold_charges']);
        });
    }
}
