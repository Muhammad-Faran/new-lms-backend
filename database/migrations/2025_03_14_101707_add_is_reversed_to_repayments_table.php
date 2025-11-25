<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('repayments', function (Blueprint $table) {
            // Add 'is_reversed' column after 'status'
            $table->boolean('is_reversed')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('repayments', function (Blueprint $table) {
            // Remove 'is_reversed' column if rollback is needed
            $table->dropColumn('is_reversed');
        });
    }
};
