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
    Schema::create('expiry_snapshots', function (Blueprint $table) {
        $table->id();
        $table->date('snapshot_date');
        $table->integer('currently_expired');       
        $table->integer('renewed_today');      
        $table->integer('new_expiries_today');      
        $table->integer('active_users');  
           $table->integer('expired_yesterday')->nullable();         
        $table->timestamps();
    });
   

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expiry_snapshots');
         

    }
};
