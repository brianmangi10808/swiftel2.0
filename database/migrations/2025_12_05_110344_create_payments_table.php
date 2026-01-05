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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type')->nullable();
            $table->string('trans_id')->unique();
            $table->dateTime('trans_time')->nullable();
            $table->decimal('trans_amount', 10, 2)->default(0.00);
            $table->string('business_short_code')->nullable();
            $table->string('bill_ref_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('org_account_balance', 10, 2)->nullable();
            $table->string('third_party_trans_id')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
