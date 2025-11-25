<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // E.g., App\Models\User
            $table->unsignedBigInteger('model_id'); // The model's primary key
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable for unauthenticated actions
            $table->string('action'); // create, update, delete
            $table->json('before_changes')->nullable(); // JSON snapshot of model state before change
            $table->json('after_changes')->nullable(); // JSON snapshot of model state after change
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}
