<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Inventory
        </x-slot>
        
        <x-slot name="description">
            Your collected items and equipment
        </x-slot>
        
        @php
            $data = $this->getViewData();
            $items = $data['items'];
            $maxSlots = $data['maxSlots'];
        @endphp
        
        <div class="inventory-grid">
            @foreach(range(1, $maxSlots) as $slotNumber)
                @php
                    $item = $items->firstWhere('slot', $slotNumber);
                @endphp
                
                <div class="inventory-slot {{ $item ? 'occupied' : '' }}" 
                     title="{{ $item ? $item->item->name : 'Empty slot' }}">
                    @if($item)
                        <div class="flex flex-col items-center justify-center p-1">
                            @if($item->item->icon)
                                <img src="{{ $item->item->icon }}" 
                                     alt="{{ $item->item->name }}" 
                                     class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                            @else
                                <x-heroicon-o-cube class="w-8 h-8 sm:w-10 sm:h-10 text-blue-600 dark:text-blue-400" />
                            @endif
                            
                            @if($item->quantity > 1)
                                <span class="text-xs font-bold mt-1">{{ $item->quantity }}</span>
                            @endif
                        </div>
                    @else
                        <x-heroicon-o-square-3-stack-3d class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400 dark:text-gray-600" />
                    @endif
                </div>
            @endforeach
        </div>
        
        @if($items->isEmpty())
            <div class="mt-4 text-center">
                <x-heroicon-o-inbox class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600" />
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Your inventory is empty. Start collecting items!
                </p>
            </div>
        @else
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                {{ $items->count() }} / {{ $maxSlots }} slots used
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
