<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('sms_providers', function (Blueprint $table) {
        $table->id();
        $table->string('provider_name');
        $table->string('api_url');
        $table->string('api_key')->nullable();
        $table->string('sender_id')->nullable();
        $table->boolean('active')->default(true);
        $table->timestamps();
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
