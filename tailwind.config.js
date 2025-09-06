import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        'bg-transparent',
        'bg-white',
        'shadow-md',
        'text-white',
        'text-gray-800'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Janson Text"', ...defaultTheme.fontFamily.sans],
                serif: ['"Janson Text"', ...defaultTheme.fontFamily.serif],
            },
            keyframes: {
                fadeSlide: {
                    "0%": { opacity: 0, transform: "translateY(10px)" },
                    "100%": { opacity: 1, transform: "translateY(0)" },
                },
            },
            animation: {
                fadeSlide: "fadeSlide 0.4s ease-out",
            },
        },
    },

    plugins: [forms],
};
