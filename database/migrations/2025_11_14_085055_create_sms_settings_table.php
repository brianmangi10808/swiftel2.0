<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default settings
        DB::table('sms_settings')->insert([
            // General Settings
            [
                'key' => 'company_name',
                'value' => config('app.name', 'RADMAN'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'system_email',
                'value' => 'admin@radman.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'paybill',
                'value' => '566518',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'till_number',
                'value' => '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // SMS Gateway Settings
            [
                'key' => 'sms_provider',
                'value' => 'simflix',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sms_api_key',
                'value' => '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sms_sender_id',
                'value' => 'RADMAN',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sms_service_id',
                'value' => '0',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Notification Settings
            [
                'key' => 'payment_confirmation_enabled',
                'value' => '1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'payment_confirmation_template',
                'value' => 'Dear customer, you have successfully subscribed to @package_name. Your subscription will expire on @expiry_date.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'expiry_notification_enabled',
                'value' => '1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'expiry_notification_template',
                'value' => 'Dear @username, your package has expired. Kindly pay using the paybill @paybill and account number @account_number to continue using the internet.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'expiry_reminder_enabled',
                'value' => '1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'expiry_reminder_template',
                'value' => 'Dear @username, your package will expire in @days_left. Kindly pay using the paybill @paybill and account number @account_number to continue using the internet.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};