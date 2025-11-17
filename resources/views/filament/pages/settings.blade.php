<x-filament-panels::page>
    <x-filament::tabs>
        <x-filament::tabs.item 
            wire:click="$set('activeTab', 'general')"
            :active="$activeTab === 'general'"
            icon="heroicon-o-cog-6-tooth"
        >
            General Settings
        </x-filament::tabs.item>

        <x-filament::tabs.item 
            wire:click="$set('activeTab', 'sms')"
            :active="$activeTab === 'sms'"
            icon="heroicon-o-chat-bubble-left-right"
        >
            SMS Gateway
        </x-filament::tabs.item>

        <x-filament::tabs.item 
            wire:click="$set('activeTab', 'notifications')"
            :active="$activeTab === 'notifications'"
            icon="heroicon-o-bell"
        >
            SMS Notifications
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div class="mt-6">
        {{-- General Settings Tab --}}
        @if($activeTab === 'general')
            <form wire:submit="saveGeneral">
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                            General Settings
                        </div>
                    </x-slot>
                    
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Configure general system settings and preferences.
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Company Name</label>
                                <input type="text" wire:model="company_name" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Your Company">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">System Email</label>
                                <input type="email" wire:model="system_email" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="admin@company.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Paybill Number</label>
                                <input type="text" wire:model="paybill" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="566518">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Till Number (Optional)</label>
                                <input type="text" wire:model="till_number" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Till Number">
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <x-filament::button type="submit" color="success">
                                Save Settings
                            </x-filament::button>
                        </div>
                    </div>
                </x-filament::section>
            </form>
        @endif

        {{-- SMS Gateway Tab --}}
        @if($activeTab === 'sms')
            <form wire:submit="saveSmsGateway">
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5" />
                            SMS Gateway Configuration (Simflix)
                        </div>
                    </x-slot>
                    
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Configure your Simflix SMS gateway settings for sending messages to customers.
                        </p>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">API Key *</label>
                                <input type="text" wire:model="api_key" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 font-mono text-sm" placeholder="098989789786676767YGBTFVTFCDFCTFXXDX989...">
                                <p class="text-xs text-gray-500 mt-1">Your Simflix API key</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Sender ID *</label>
                                <input type="text" wire:model="sender_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="RADMAN" maxlength="11">
                                <p class="text-xs text-gray-500 mt-1">Sender ID (max 11 characters)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Service ID</label>
                                <input type="text" wire:model="service_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="0">
                                <p class="text-xs text-gray-500 mt-1">Usually 0 for default service</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <x-filament::button type="submit" color="success">
                                Save Configuration
                            </x-filament::button>
                            <x-filament::button type="button" wire:click="testSmsConnection" color="gray" outlined>
                                <x-heroicon-o-signal class="w-4 h-4 mr-1" />
                                Test Connection
                            </x-filament::button>
                        </div>
                    </div>
                </x-filament::section>
            </form>
        @endif

        {{-- SMS Notifications Tab --}}
        @if($activeTab === 'notifications')
            <form wire:submit="saveNotifications">
                <div class="space-y-6">
                    {{-- Payment Confirmation --}}
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-500" />
                                    Payment Confirmation SMS
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="payment_confirmation_enabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                                </label>
                            </div>
                        </x-slot>
                        
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Send SMS confirmation when customer makes a payment or account is created.
                            </p>
                            
                            <div>
                                <label class="block text-sm font-medium mb-2">Message Template</label>
                                <textarea wire:model="payment_confirmation_template" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Enter your payment confirmation message"></textarea>
                                <p class="text-xs text-gray-500 mt-2">
                                    <strong>Available variables:</strong> @username, @first_name, @password, @package_name, @company_name, @expiry_date, @days_left, @paybill, @till_number, @account_number, @amount
                                </p>
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- Expiry Notification --}}
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500" />
                                    Account Expired Notification
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="expiry_notification_enabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                                </label>
                            </div>
                        </x-slot>
                        
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Send SMS when customer's account expires.
                            </p>
                            
                            <div>
                                <label class="block text-sm font-medium mb-2">Message Template</label>
                                <textarea wire:model="expiry_notification_template" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Enter your expiry notification message"></textarea>
                                <p class="text-xs text-gray-500 mt-2">
                                    <strong>Available variables:</strong> @username, @first_name, @password, @package_name, @company_name, @expiry_date, @days_left, @paybill, @till_number, @account_number, @amount
                                </p>
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- Expiry Reminder --}}
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-bell-alert class="w-5 h-5 text-amber-500" />
                                    Expiry Reminder Notification
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="expiry_reminder_enabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                                </label>
                            </div>
                        </x-slot>
                        
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Send SMS reminders before account expiry (7, 3, and 1 day before).
                            </p>
                            
                            <div>
                                <label class="block text-sm font-medium mb-2">Message Template</label>
                                <textarea wire:model="expiry_reminder_template" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Enter your expiry reminder message"></textarea>
                                <p class="text-xs text-gray-500 mt-2">
                                    <strong>Available variables:</strong> @username, @first_name, @password, @package_name, @company_name, @expiry_date, @days_left, @paybill, @till_number, @account_number, @amount
                                </p>
                            </div>
                        </div>
                    </x-filament::section>

                    <div class="flex gap-2">
                        <x-filament::button type="submit" color="success">
                            Save Notification Settings
                        </x-filament::button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</x-filament-panels::page>