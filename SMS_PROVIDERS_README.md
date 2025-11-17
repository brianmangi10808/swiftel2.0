# Multi-Provider SMS System

This system allows you to configure and manage multiple SMS providers (Africa's Talking, Suftech, Simflix, Twilio, etc.) through the admin interface.

## Features

- **Multiple Provider Support**: Configure multiple SMS providers simultaneously
- **Dynamic Provider Addition**: Add new providers (like Suftech) without code changes
- **Custom Configuration**: Each provider has its own configuration fields (API URL, API Key, Sender ID, etc.)
- **Default Provider**: Set a default provider for SMS sending
- **Provider Management**: Enable/disable providers, test connections, and manage settings
- **Backward Compatible**: Maintains compatibility with legacy settings

## Available Providers

1. **Simflix** - Kenya-based SMS gateway
2. **Africa's Talking** - Pan-African SMS service
3. **Twilio** - Global SMS platform
4. **Suftech** - Custom SMS provider with flexible configuration

## Installation & Setup

### 1. Run Migrations

```bash
php artisan migrate
```

This creates the `sms_providers` table to store provider configurations.

### 2. (Optional) Seed Example Providers

```bash
php artisan db:seed --class=SmsProviderSeeder
```

This will create example providers that you can configure with your actual credentials.

## Usage

### Admin Interface

Navigate to **Settings > SMS Providers** in the Filament admin panel.

#### Adding a New Provider

1. Click "New SMS Provider"
2. Fill in the provider information:
   - **Provider Name**: A friendly name (e.g., "Africa's Talking Production")
   - **Provider Type**: Select from available providers
   - **Description**: Optional description
   - **Active**: Enable/disable the provider
   - **Set as Default**: Mark this provider as the default for sending SMS

3. Configure provider-specific settings:
   - The form dynamically shows fields based on the selected provider type
   - Each provider requires different configuration (API keys, URLs, etc.)

4. Click "Create" to save the provider

#### Testing a Provider

Click the "Test" button (signal icon) next to any provider to test the connection. This will:
- Verify API credentials
- Check connectivity
- Display account balance (if available)

#### Setting a Default Provider

Toggle the "Set as Default" option when creating/editing a provider. Only one provider can be default at a time.

## Provider Configuration Details

### Africa's Talking

Required fields:
- **API Key**: Your Africa's Talking API Key
- **Username**: Your Africa's Talking username (e.g., "sandbox")
- **Sender ID**: Sender ID (max 11 characters)

### Suftech

Required fields:
- **API URL**: Base URL for Suftech API (default: `https://api.suftech.com/v1`)
- **API Key**: Your Suftech API Key
- **Sender ID**: Sender ID (max 11 characters)
- **Client ID**: Optional client ID for tracking

### Simflix

Required fields:
- **API Key**: Your Simflix API Key
- **Sender ID**: Sender ID (max 11 characters)
- **Service ID**: Service ID (default: 0)

### Twilio

Required fields:
- **Account SID**: Your Twilio Account SID
- **Auth Token**: Your Twilio Auth Token
- **From Number**: Your Twilio phone number (e.g., +1234567890)

## Programmatic Usage

### Sending SMS with Default Provider

```php
use App\Services\SmsProviders\SmsProviderFactory;

// Get default provider
$provider = SmsProviderFactory::make();

// Send SMS
$success = $provider->send('0712345678', 'Your message here');
```

### Sending SMS with Specific Provider

```php
// By provider ID
$provider = SmsProviderFactory::make(1);

// By provider type
$provider = SmsProviderFactory::make('suftech');

// Send SMS
$success = $provider->send('0712345678', 'Your message here');
```

### Sending Bulk SMS

```php
$provider = SmsProviderFactory::make();

$recipients = [
    ['phone' => '0712345678', 'message' => 'Message 1'],
    ['phone' => '0723456789', 'message' => 'Message 2'],
];

$results = $provider->sendBulk($recipients);
```

### Getting All Active Providers

```php
use App\Services\SmsProviders\SmsProviderFactory;

$providers = SmsProviderFactory::getAllActive();

foreach ($providers as $id => $provider) {
    $instance = $provider['instance'];
    $model = $provider['model'];

    // Use the provider
    $instance->send('0712345678', 'Test message');
}
```

### Checking Provider Balance

```php
$provider = SmsProviderFactory::make();
$balance = $provider->getBalance();

echo "Balance: KES " . number_format($balance, 2);
```

## Adding a New Provider

To add a new SMS provider:

1. **Create Provider Class**

Create a new file in `app/Services/SmsProviders/` (e.g., `NewProviderName.php`):

```php
<?php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Http;

class NewProviderName extends BaseSmsProvider
{
    public function getName(): string
    {
        return 'New Provider';
    }

    public function getConfigFields(): array
    {
        return [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'required' => true,
                'placeholder' => 'Enter your API Key',
                'help' => 'Your API Key',
            ],
            // Add more fields as needed
        ];
    }

    public function send(string $phoneNumber, string $message): bool
    {
        // Implement SMS sending logic
    }

    public function sendBulk(array $recipients): array
    {
        // Implement bulk SMS sending logic
    }

    public function getBalance(): ?float
    {
        // Implement balance checking logic
    }

    public function testConnection(): array
    {
        // Implement connection testing logic
    }
}
```

2. **Register Provider**

Add the provider to `SmsProviderFactory::getAvailableProviders()`:

```php
return [
    'simflix' => SimflixProvider::class,
    'africastalking' => AfricasTalkingProvider::class,
    'twilio' => TwilioProvider::class,
    'suftech' => SuftechProvider::class,
    'newprovider' => NewProviderName::class, // Add your provider
];
```

3. **Add to Model**

Add the provider to `SmsProvider::getAvailableTypes()`:

```php
return [
    'simflix' => 'Simflix',
    'africastalking' => "Africa's Talking",
    'twilio' => 'Twilio',
    'suftech' => 'Suftech',
    'newprovider' => 'New Provider', // Add your provider
];
```

4. **Use in Admin**

The provider will now be available in the admin interface dropdown and can be configured with its custom fields.

## Database Schema

### `sms_providers` Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Display name for the provider |
| provider_type | string | Provider type (simflix, africastalking, etc.) |
| configuration | json | Provider-specific configuration |
| is_active | boolean | Whether provider is enabled |
| is_default | boolean | Whether this is the default provider |
| description | text | Optional description |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Update timestamp |

## Backward Compatibility

The system maintains backward compatibility with the legacy `sms_settings` table. If no providers are configured in the database, the factory will fall back to using settings from the `sms_settings` table.

## Security Considerations

- API keys and sensitive credentials are stored in the database as JSON
- Consider encrypting the `configuration` column in production
- Use environment variables for sensitive data where possible
- Implement proper access controls for the SMS Providers admin interface

## Troubleshooting

### Provider Not Showing in Dropdown

1. Check that the provider is registered in `SmsProviderFactory::getAvailableProviders()`
2. Check that the provider is added to `SmsProvider::getAvailableTypes()`
3. Clear cache: `php artisan cache:clear`

### SMS Not Sending

1. Test the provider connection using the "Test" button
2. Check provider credentials
3. Verify the provider is active and set as default (if intended)
4. Check application logs for error messages

### Configuration Not Saving

1. Ensure the `configuration` column can accept JSON
2. Check file permissions on the database
3. Verify form validation is passing

## API Response Formats

Each provider may return different response formats. Refer to the provider's implementation class for specific response handling:

- **Simflix**: Status code `1000` indicates success
- **Africa's Talking**: `status: 'Success'` in recipients array
- **Twilio**: HTTP 200/201 for successful sending
- **Suftech**: `status: 'success'` in response body

## Support

For issues or questions:
1. Check the application logs
2. Review provider documentation
3. Test provider connection in admin panel
4. Contact provider support for API-specific issues
