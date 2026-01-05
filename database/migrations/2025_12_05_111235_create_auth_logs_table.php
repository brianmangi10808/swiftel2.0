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
        Schema::create('auth_logs', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64);
            $table->string('action', 255); 
            $table->string('status', 50); 
            $table->string('ip_address', 45)->nullable(); 
            $table->string('session_id', 128)->nullable();
            $table->text('reason')->nullable(); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_logs');
    }
};
