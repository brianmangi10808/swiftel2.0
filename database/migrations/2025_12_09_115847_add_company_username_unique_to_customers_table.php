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
        Schema::table('customers', function (Blueprint $table) {
            // Drop the old global unique index on username
            $table->dropUnique(['username']);

            // Add composite unique index for company_id + username
            $table->unique(['company_id', 'username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop the composite unique index
            $table->dropUnique(['company_id', 'username']);

            // Restore the old global unique index
            $table->unique('username');
        });
    }
};
