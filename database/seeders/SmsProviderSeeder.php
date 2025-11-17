<?php

namespace Database\Seeders;

use App\Models\SmsProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmsProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example Africa's Talking Provider
        SmsProvider::create([
            'name' => "Africa's Talking Production",
            'provider_type' => 'africastalking',
            'configuration' => [
                'api_key' => 'your_africastalking_api_key_here',
                'username' => 'your_username',
                'sender_id' => 'RADMAN',
            ],
            'description' => 'Africa\'s Talking SMS gateway for production',
            'is_active' => true,
            'is_default' => true, // Set as default
        ]);

        // Example Suftech Provider
        SmsProvider::create([
            'name' => 'Suftech Primary',
            'provider_type' => 'suftech',
            'configuration' => [
                'api_url' => 'https://api.suftech.com/v1',
                'api_key' => 'your_suftech_api_key_here',
                'sender_id' => 'RADMAN',
                'client_id' => 'optional_client_id',
            ],
            'description' => 'Suftech SMS gateway',
            'is_active' => false, // Not active by default
            'is_default' => false,
        ]);

        // Example Simflix Provider
        SmsProvider::create([
            'name' => 'Simflix Backup',
            'provider_type' => 'simflix',
            'configuration' => [
                'api_key' => 'your_simflix_api_key_here',
                'sender_id' => 'RADMAN',
                'service_id' => '0',
            ],
            'description' => 'Simflix SMS gateway as backup',
            'is_active' => false,
            'is_default' => false,
        ]);

        // Example Twilio Provider
        SmsProvider::create([
            'name' => 'Twilio International',
            'provider_type' => 'twilio',
            'configuration' => [
                'account_sid' => 'your_twilio_account_sid',
                'auth_token' => 'your_twilio_auth_token',
                'from_number' => '+1234567890',
            ],
            'description' => 'Twilio for international SMS',
            'is_active' => false,
            'is_default' => false,
        ]);
    }
}
