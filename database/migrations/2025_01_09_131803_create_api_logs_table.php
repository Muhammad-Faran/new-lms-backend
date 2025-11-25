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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();  // Foreign key for user if available
            $table->string('url');
            $table->string('method');
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->json('response_body')->nullable();
            $table->integer('response_status')->default(200);
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
