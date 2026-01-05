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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // Friendly name
            $table->string('ip_address');         // Router IP
            $table->string('username');
            $table->string('password');

            $table->foreignId('api_port')
                ->nullable()
                ->nullOnDelete();

            $table->string('location')->nullable();
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
