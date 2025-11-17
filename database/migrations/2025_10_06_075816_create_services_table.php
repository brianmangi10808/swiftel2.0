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
         Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. Home Basic 5M/5M
            $table->decimal('price', 10, 2)->default(0);
            $table->string('upload_limit'); // e.g. 5M
            $table->string('download_limit'); // e.g. 5M
            $table->integer('fup_limit')->default(0); // in MB or GB, your choice
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
