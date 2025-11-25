<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Change `charge_condition` to VARCHAR temporarily
        Schema::table('product_charges', function (Blueprint $table) {
            $table->string('charge_condition', 50)->change();
        });

        // Step 2: Update existing records to new default value
        DB::table('product_charges')->whereIn('charge_condition', ['installments', 'Requested Amount', 'Percentage of full amount'])
            ->update(['charge_condition' => 'Order Amount']);

        // Step 3: Change `charge_condition` back to ENUM with new values
        Schema::table('product_charges', function (Blueprint $table) {
            $table->enum('charge_condition', ['Order Amount', 'Financing Amount'])->default('Order Amount')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the enum values back to the original
        Schema::table('product_charges', function (Blueprint $table) {
            $table->string('charge_condition', 50)->change();
        });

        DB::table('product_charges')->whereIn('charge_condition', ['Order Amount', 'Financing Amount'])
            ->update(['charge_condition' => 'Requested Amount']);

        Schema::table('product_charges', function (Blueprint $table) {
            $table->enum('charge_condition', ['installments', 'Requested Amount', 'Percentage of full amount'])
                ->default('Requested Amount')
                ->change();
        });
    }
};
