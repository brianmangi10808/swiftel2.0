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
    Schema::create('traffic_samples', function (Blueprint $table) {
        $table->id();
        $table->string('pppoe_account');   // username or interface
        $table->float('upload_mbps');
        $table->float('download_mbps');
        $table->timestamp('logged_at');   // time sample was taken
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_samples');
    }
};
