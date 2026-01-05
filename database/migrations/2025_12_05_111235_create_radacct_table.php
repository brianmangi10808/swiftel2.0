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
        Schema::create('radacct', function (Blueprint $table) {
            $table->bigIncrements('radacctid');
            $table->string('acctsessionid', 64);
            $table->string('acctuniqueid', 32)->unique();
            $table->string('username', 64)->nullable();
            $table->string('nasipaddress', 15);
            $table->dateTime('acctstarttime')->nullable();
            $table->dateTime('acctstoptime')->nullable();
            $table->integer('acctsessiontime')->nullable();
            $table->string('acctterminatecause', 32)->nullable();
            $table->string('nasportid', 50)->nullable();
            $table->string('realm', 255)->nullable();
            $table->string('nasporttype', 255)->nullable();
            $table->dateTime('acctupdatetime')->nullable();
            $table->integer('acctinterval')->nullable();
            $table->string('acctauthentic', 32)->nullable();
            $table->string('connectinfo_start', 128)->nullable();
            $table->string('connectinfo_stop', 128)->nullable();
            $table->bigInteger('acctinputoctets')->nullable();
            $table->bigInteger('acctoutputoctets')->nullable();
            $table->string('calledstationid', 50);
            $table->string('callingstationid', 50);
            $table->string('servicetype', 32)->nullable();
            $table->string('framedprotocol', 32)->nullable();
            $table->string('framedipaddress', 15);
            $table->string('framedipv6address', 45);
            $table->string('framedipv6prefix', 45);
            $table->string('framedinterfaceid', 44);
            $table->string('delegatedipv6prefix', 45);
            $table->string('class', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radacct');
    }
};
