export default {
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
    ],
    theme: {
        extend: {
            // Mobile-first responsive breakpoints
            screens: {
                'xs': '475px',
            },
            // Touch-friendly spacing for mobile devices
            spacing: {
                'touch': '44px', // Minimum touch target size (iOS/Android guidelines)
                'safe-top': 'env(safe-area-inset-top)',
                'safe-bottom': 'env(safe-area-inset-bottom)',
            },
        },
    },
}
