<x-filament-panels::page>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">System Users</h1>

        <x-filament::button 
            tag="a"
            icon="heroicon-o-plus"
            color="success"
            :href="route('filament.resources.users.create')">
            Create User
        </x-filament::button>
    </div>

    {{-- Tabs --}}
    <div class="mb-4 border-b flex space-x-8">
        <button wire:click="switchTab('all')"
                class="{{ $activeTab === 'all' ? 'border-green-600 text-green-700 font-bold' : 'border-transparent text-gray-500' }} py-3 border-b-2">
            All Users
        </button>

        <button wire:click="switchTab('administrator')"
                class="{{ $activeTab === 'administrator' ? 'border-purple-600 text-purple-700 font-bold' : 'border-transparent text-gray-500' }} py-3 border-b-2">
            Administrators
        </button>

        <button wire:click="switchTab('technical_support')"
                class="{{ $activeTab === 'technical_support' ? 'border-blue-600 text-blue-700 font-bold' : 'border-transparent text-gray-500' }} py-3 border-b-2">
            Technical Support
        </button>
    </div>

    {{-- Render Resource Table --}}
    <div class="mt-4">
        {{ $this->getTable() }}
    </div>

</x-filament-panels::page>
