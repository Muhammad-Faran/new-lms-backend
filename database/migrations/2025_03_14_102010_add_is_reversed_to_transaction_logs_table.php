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
        Schema::table('transaction_logs', function (Blueprint $table) {
            // Add 'is_reversed' column after 'type'
            $table->boolean('is_reversed')->default(0)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            // Remove 'is_reversed' column if rollback is needed
            $table->dropColumn('is_reversed');
        });
    }
};
