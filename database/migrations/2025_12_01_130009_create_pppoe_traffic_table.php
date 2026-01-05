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
    Schema::create('pppoe_traffic', function (Blueprint $table) {
        $table->id();
        $table->string('interface');
        $table->float('upload_mbps');
        $table->float('download_mbps');
        $table->timestamp('logged_at')->index();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pppoe_traffic');
    }
};
