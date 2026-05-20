<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Connect With Us
        </x-slot>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($links as $name => $url)
                @if(is_array($url))
                    @foreach($url as $subName => $subUrl)
                        <a href="{{ $subUrl }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="mobile-button bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <span class="text-sm">{{ $subName }}</span>
                        </a>
                    @endforeach
                @else
                    <a href="{{ $url }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="mobile-button bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                        @switch($name)
                            @case('GitHub')
                                <x-heroicon-o-code-bracket class="w-5 h-5 mr-2" />
                                @break
                            @case('Facebook')
                            @case('Facebook Page')
                                <x-heroicon-o-users class="w-5 h-5 mr-2" />
                                @break
                            @case('Twitter')
                                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 mr-2" />
                                @break
                            @case('YouTube')
                                <x-heroicon-o-play-circle class="w-5 h-5 mr-2" />
                                @break
                            @default
                                <x-heroicon-o-link class="w-5 h-5 mr-2" />
                        @endswitch
                        <span class="text-sm">{{ $name }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
