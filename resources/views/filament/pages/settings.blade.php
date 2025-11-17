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
                <div class="space-y-6">
                    {{-- Company Information --}}
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-building-office class="w-5 h-5" />
                                Company Information
                            </div>
                        </x-slot>

                        <div class="space-y-4">
                            {{-- Logo Upload --}}
                            <div>
                                <label class="block text-sm font-medium mb-2">Company Logo</label>
                                <div class="flex items-start gap-4">
                                    @if($company_logo)
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('storage/' . $company_logo) }}" alt="Company Logo" class="w-32 h-32 object-contain rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2">
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <input type="file" wire:model="company_logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/20 dark:file:text-primary-400">
                                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        @if($company_logo)
                                            <button type="button" wire:click="deleteLogo" class="mt-2 text-xs text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                Delete Logo
                                            </button>
                                        @endif
                                        @if($errors->has('company_logo'))
                                            <p class="mt-1 text-xs text-red-600">{{ $errors->first('company_logo') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Company Name</label>
                                    <input type="text" wire:model="company_name" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Your Company Name">
                                    @error('company_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">System Email</label>
                                    <input type="email" wire:model="system_email" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="admin@company.com">
                                    @error('system_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- Payment Settings --}}
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-credit-card class="w-5 h-5" />
                                Payment Settings
                            </div>
                        </x-slot>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Payment Method</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" wire:model.live="payment_method" value="paybill" class="mr-2">
                                        <span class="text-sm">Paybill</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" wire:model.live="payment_method" value="till" class="mr-2">
                                        <span class="text-sm">Till Number</span>
                                    </label>
                                </div>
                                @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($payment_method === 'paybill')
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Paybill Number</label>
                                        <input type="text" wire:model="paybill" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="566518">
                                        @error('paybill') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @else
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Till Number</label>
                                        <input type="text" wire:model="till_number" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Till Number">
                                        @error('till_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium mb-2">Phone Number</label>
                                    <input type="text" wire:model="phone_number" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="+254 712 345 678">
                                    @error('phone_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- Support Settings --}}
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-phone class="w-5 h-5" />
                                Support Information
                            </div>
                        </x-slot>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Support Number</label>
                                <input type="text" wire:model="support_number" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="+254 700 000 000">
                                @error('support_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Support Email</label>
                                <input type="email" wire:model="support_email" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="support@company.com">
                                @error('support_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- Appearance Settings --}}
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-paint-brush class="w-5 h-5" />
                                Appearance & Branding
                            </div>
                        </x-slot>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Primary Color</label>
                                    <div class="flex gap-2">
                                        <input type="color" wire:model.live="primary_color" class="h-10 w-20 rounded cursor-pointer">
                                        <input type="text" wire:model="primary_color" class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="#f59e0b">
                                    </div>
                                    @error('primary_color') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Secondary Color</label>
                                    <div class="flex gap-2">
                                        <input type="color" wire:model.live="secondary_color" class="h-10 w-20 rounded cursor-pointer">
                                        <input type="text" wire:model="secondary_color" class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="#6b7280">
                                    </div>
                                    @error('secondary_color') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Font Family</label>
                                    <select wire:model="font_family" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        <option value="Inter">Inter</option>
                                        <option value="Roboto">Roboto</option>
                                        <option value="Open Sans">Open Sans</option>
                                        <option value="Lato">Lato</option>
                                        <option value="Montserrat">Montserrat</option>
                                        <option value="Poppins">Poppins</option>
                                        <option value="Arial">Arial</option>
                                        <option value="Helvetica">Helvetica</option>
                                        <option value="Times New Roman">Times New Roman</option>
                                        <option value="Georgia">Georgia</option>
                                    </select>
                                    @error('font_family') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Color Preview --}}
                            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="text-sm font-medium mb-3">Preview</p>
                                <div class="flex gap-3">
                                    <div class="flex-1 p-4 rounded-lg text-white text-center font-medium" style="background-color: {{ $primary_color }}; font-family: {{ $font_family }}">
                                        Primary Color
                                    </div>
                                    <div class="flex-1 p-4 rounded-lg text-white text-center font-medium" style="background-color: {{ $secondary_color }}; font-family: {{ $font_family }}">
                                        Secondary Color
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-filament::section>

                    <div class="flex gap-2">
                        <x-filament::button type="submit" color="success">
                            <x-heroicon-o-check class="w-4 h-4 mr-1" />
                            Save All Settings
                        </x-filament::button>
                    </div>
                </div>
            </form>
        @endif

        {{-- SMS Providers Tab --}}
        @if($activeTab === 'sms')
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5" />
                            SMS Gateway Providers
                        </div>
                        <a href="{{ route('filament.admin.resources.sms-providers.index') }}"
                           class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                            Manage Providers →
                        </a>
                    </div>
                </x-slot>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            <div>
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                    SMS providers are now managed separately
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                    Click "Manage Providers" to configure multiple SMS gateways (Simflix, Africa's Talking, Twilio, Suftech)
                                </p>
                            </div>
                        </div>
                    </div>

                    @php
                        $providers = $this->getSmsProviders();
                    @endphp

                    @if($providers->isEmpty())
                        <div class="text-center py-12">
                            <x-heroicon-o-exclamation-triangle class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                No SMS Providers Configured
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                You need to configure at least one SMS provider to send messages to customers.
                            </p>
                            <a href="{{ route('filament.admin.resources.sms-providers.create') }}">
                                <x-filament::button color="primary">
                                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                                    Add Your First Provider
                                </x-filament::button>
                            </a>
                        </div>
                    @else
                        <div class="space-y-3">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                Configured Providers ({{ $providers->count() }})
                            </h4>

                            @foreach($providers as $provider)
                                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @if($provider->is_default)
                                                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                                    <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                                                </div>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                                    <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $provider->name }}
                                                </h5>
                                                @if($provider->is_default)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                        Default
                                                    </span>
                                                @endif
                                                @if(!$provider->is_active)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ ucfirst($provider->provider_type) }}
                                                @if($provider->description)
                                                    · {{ $provider->description }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('filament.admin.resources.sms-providers.edit', ['record' => $provider->id]) }}">
                                            <x-filament::button size="sm" color="gray" outlined>
                                                <x-heroicon-o-pencil class="w-3 h-3 mr-1" />
                                                Edit
                                            </x-filament::button>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-4">
                            <a href="{{ route('filament.admin.resources.sms-providers.create') }}">
                                <x-filament::button color="gray" outlined>
                                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                                    Add Another Provider
                                </x-filament::button>
                            </a>
                        </div>
                    @endif
                </div>
            </x-filament::section>
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