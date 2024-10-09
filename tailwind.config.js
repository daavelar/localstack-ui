/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./resources/js/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                dark: {
                    100: '#d1d5db',
                    200: '#9ca3af',
                    300: '#6b7280',
                    400: '#4b5563',
                    500: '#374151',
                    600: '#1f2937',
                    700: '#111827',
                    800: '#0f172a',
                    900: '#0a0e14',
                },
                gray: {
                    100: '#f5f5f5',
                    200: '#e5e5e5',
                    300: '#d4d4d4',
                    400: '#a3a3a3',
                    500: '#737373',
                    600: '#525252',
                    700: '#404040',
                    800: '#262626',
                    900: '#171717',
                },
            },
        },
    },
    plugins: [],
}
