<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Actions
        </x-slot>

        <x-slot name="description">
            Common administrative tasks
        </x-slot>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($this->getActions() as $action)
                <a
                    href="{{ $action['url'] }}"
                    class="flex flex-col items-center justify-center rounded-lg border border-gray-300 p-6 text-center transition hover:border-{{ $action['color'] }}-500 hover:bg-{{ $action['color'] }}-50 dark:border-gray-700 dark:hover:border-{{ $action['color'] }}-500 dark:hover:bg-{{ $action['color'] }}-900/10"
                >
                    <div class="mb-3">
                        @svg($action['icon'], 'h-8 w-8 text-' . $action['color'] . '-500')
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $action['label'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
