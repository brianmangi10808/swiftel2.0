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
    Schema::create('sms_gateways', function (Blueprint $table) {
        $table->id();
        $table->string('name');            // Display name of gateway
        $table->string('type');            // simflix, africastalking, afrokatt, custom
        $table->json('credentials')->nullable(); // Dynamic inputs stored here
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_gateways');
    }
};
