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
    {Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
    $table->text('description')->nullable();
    $table->string('status')->default('open'); // e.g., open, in-progress, closed
    $table->string('severity')->nullable(); // e.g., low, medium, high
    $table->text('comment')->nullable();
    $table->text('resolution_notes')->nullable();
    $table->timestamp('resolved_at')->nullable();
    $table->timestamps(); // adds created_at and updated_at automatically
});
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
