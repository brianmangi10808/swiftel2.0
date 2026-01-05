<div class="p-6 bg-white dark:bg-gray-800 shadow rounded-xl space-y-4">

    <h3 class="text-xl font-semibold">SMS Provider Configuration</h3>

    <x-filament::input.wrapper>
        <x-filament::input.label>Provider Name</x-filament::input.label>
        <input type="text" wire:model="provider_name" class="w-full" />
    </x-filament::input.wrapper>

    <x-filament::input.wrapper>
        <x-filament::input.label>API URL</x-filament::input.label>
        <input type="text" wire:model="api_url" class="w-full" />
    </x-filament::input.wrapper>

    <x-filament::input.wrapper>
        <x-filament::input.label>API Key</x-filament::input.label>
        <input type="text" wire:model="api_key" class="w-full" />
    </x-filament::input.wrapper>

    <x-filament::input.wrapper>
        <x-filament::input.label>Sender ID</x-filament::input.label>
        <input type="text" wire:model="sender_id" class="w-full" />
    </x-filament::input.wrapper>

    <x-filament::button wire:click="save" icon="heroicon-o-check">
        Save
    </x-filament::button>

    @if (session()->has('success'))
        <div class="text-green-600">{{ session('success') }}</div>
    @endif
</div>

