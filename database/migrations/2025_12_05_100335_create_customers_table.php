<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('status')->nullable();
            $table->boolean('enable')->default(true);
            $table->unsignedBigInteger('sector_id')->nullable();
            $table->unsignedBigInteger('premise_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->boolean('allow_mac')->default(false);
            $table->integer('simultaneous_use')->default(1);
            $table->string('Calling_Station_Id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->decimal('credit', 10, 2)->default(0);
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->text('comment')->nullable();
            $table->text('attribute')->nullable();
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at

            // Foreign keys
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
            $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('set null');
            $table->foreign('premise_id')->references('id')->on('premises')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

  

