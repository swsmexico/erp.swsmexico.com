/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                sws: {
                    blue:       '#5CC8F2',
                    'blue-dark':'#3BA8D2',
                    gray:       '#F2F2F2',
                    'gray-mid': '#595959',
                    black:      '#0D0D0D',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui'],
            },
        },
    },
    plugins: [],
}
