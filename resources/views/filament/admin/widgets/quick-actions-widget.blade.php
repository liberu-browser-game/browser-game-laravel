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
                @php
                    $colorClasses = [
                        'primary' => [
                            'border' => 'hover:border-primary-500 dark:hover:border-primary-500',
                            'bg' => 'hover:bg-primary-50 dark:hover:bg-primary-900/10',
                            'text' => 'text-primary-500',
                        ],
                        'success' => [
                            'border' => 'hover:border-green-500 dark:hover:border-green-500',
                            'bg' => 'hover:bg-green-50 dark:hover:bg-green-900/10',
                            'text' => 'text-green-500',
                        ],
                        'info' => [
                            'border' => 'hover:border-blue-500 dark:hover:border-blue-500',
                            'bg' => 'hover:bg-blue-50 dark:hover:bg-blue-900/10',
                            'text' => 'text-blue-500',
                        ],
                        'warning' => [
                            'border' => 'hover:border-yellow-500 dark:hover:border-yellow-500',
                            'bg' => 'hover:bg-yellow-50 dark:hover:bg-yellow-900/10',
                            'text' => 'text-yellow-500',
                        ],
                    ];
                    $classes = $colorClasses[$action['color']] ?? $colorClasses['primary'];
                @endphp
                <a
                    href="{{ $action['url'] }}"
                    class="flex flex-col items-center justify-center rounded-lg border border-gray-300 p-6 text-center transition {{ $classes['border'] }} {{ $classes['bg'] }} dark:border-gray-700"
                >
                    <div class="mb-3">
                        <x-filament::icon
                            :icon="$action['icon']"
                            class="h-8 w-8 {{ $classes['text'] }}"
                        />
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $action['label'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
