<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Game Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Player Dashboard -->
            <div class="mb-8">
                @livewire('player-dashboard')
            </div>

            <!-- Quests and Inventory Side by Side -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div>
                    @livewire('quest-board')
                </div>
                <div>
                    @livewire('player-inventory')
                </div>
            </div>

            <!-- Guild Panel -->
            <div>
                @livewire('guild-panel')
            </div>
        </div>
    </div>
</x-app-layout>
