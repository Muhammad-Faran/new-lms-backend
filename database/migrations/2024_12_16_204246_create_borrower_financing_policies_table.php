<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrower_financing_policies', function (Blueprint $table) {
        $table->id(); // Primary key
        $table->foreignId('borrower_id')->constrained('borrowers')->onDelete('cascade'); // Borrower reference
        $table->decimal('financing_percentage', 5, 2); // Percentage allowed for financing (e.g., 70.00 for 70%)
        $table->timestamps(); // Created and updated timestamps
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrower_financing_policies');
    }
};
