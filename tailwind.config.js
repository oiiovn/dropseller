import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/crm/**/*.{js,jsx}',
        './resources/js/admin/**/*.{js,jsx}',
        './resources/js/shared/**/*.{js,jsx}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    silver: '#e4e4e7',
                    ink: '#0f172a',
                    blue: '#2563eb',
                },
            },
        },
    },

    plugins: [forms],
};
