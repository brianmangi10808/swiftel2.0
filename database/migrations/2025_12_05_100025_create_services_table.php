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
            $table->string('name')->unique(); 
            $table->decimal('price', 10, 2)->default(0);
            
            // Make upload_limit and download_limit nullable to avoid insert errors
            $table->string('upload_limit')->nullable(); 
            $table->string('download_limit')->nullable();

            $table->integer('fup_limit')->default(0); 

            $table->string('speed_limit')->nullable();
            $table->string('framed_pool')->nullable();
            $table->string('throttle_limit')->nullable();

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
