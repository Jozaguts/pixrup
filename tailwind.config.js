export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#6E33FF',        // Futzo purple
                info: '#16B1FF',           // Blue info
                success: '#56CA00',        // Green success
                warning: '#FFB400',        // Yellow
                error: '#FF4C51',          // Red
                background: '#f4f5fa',     // Soft background
                surface: '#FFFFFF',
                dark: '#2E263D',
            },
        },
    },
    plugins: [],
}
