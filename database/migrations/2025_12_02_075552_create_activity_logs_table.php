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
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('action');                 // created, updated, deleted, viewed, login, logout, etc.
        $table->string('model')->nullable();      // App\Models\Customer
        $table->unsignedBigInteger('model_id')->nullable();
        $table->string('url')->nullable();
        $table->string('ip_address')->nullable();
        $table->text('description')->nullable();
        $table->json('data')->nullable();         // optional — before/after
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
