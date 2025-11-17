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
        Schema::create('sms_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Display name (e.g., "Africa's Talking Production")
            $table->string('provider_type'); // Provider type (e.g., "africastalking", "suftech", "simflix")
            $table->json('configuration'); // Provider-specific config (API keys, URLs, etc.)
            $table->boolean('is_active')->default(true); // Enable/disable provider
            $table->boolean('is_default')->default(false); // Default provider for SMS sending
            $table->text('description')->nullable(); // Optional description
            $table->timestamps();

            // Indexes
            $table->index('provider_type');
            $table->index('is_active');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_providers');
    }
};
