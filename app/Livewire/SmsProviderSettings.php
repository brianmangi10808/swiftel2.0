<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\SmsProvider;

class SmsProviderSettings extends Component
{
    public $provider_name;
    public $api_url;
    public $api_key;
    public $sender_id;

    public function mount()
    {
        $settings = SmsProvider::first();

        if ($settings) {
            $this->provider_name = $settings->provider_name;
            $this->api_url = $settings->api_url;
            $this->api_key = $settings->api_key;
            $this->sender_id = $settings->sender_id;
        }
    }

    public function save()
    {
        SmsProvider::updateOrCreate(
            ['id' => 1],
            [
                'provider_name' => $this->provider_name,
                'api_url' => $this->api_url,
                'api_key' => $this->api_key,
                'sender_id' => $this->sender_id,
            ]
        );

        session()->flash('success', 'Settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.sms-provider-settings');
    }
}
