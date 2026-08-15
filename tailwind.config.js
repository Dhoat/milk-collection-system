import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                dairy: {
                    50: '#EAF4FF',
                    100: '#D5E8FF',
                    200: '#B0D3FF',
                    300: '#7CB7FF',
                    400: '#3D94FF',
                    500: '#0072E5',
                    600: '#005BAC', // Primary Dairy Blue
                    700: '#004788',
                    800: '#003B73', // Dark Blue
                    900: '#00264D',
                    950: '#00142C',
                },
                'dairy-bg': '#F5F8FC',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

