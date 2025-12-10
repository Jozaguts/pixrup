export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#6E33FF',
                info: '#16B1FF',
                success: '#56CA00',
                warning: '#FFB400',
                error: '#FF4C51',
                background: '#f4f5fa',
                surface: '#FFFFFF',
                dark: '#2E263D',
            },
        },
    },
    plugins: [
        require('@tailwindcss/line-clamp'),
    ],
}
