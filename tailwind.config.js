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
        'bg-green-600',
        'bg-green-700',
        'bg-green-900',
        'hover:bg-green-700',
        'focus:bg-green-700',
        'active:bg-green-900',
        'ring-green-500',
        'focus:ring-green-500',
        'text-green-600',
        'text-green-800',
        'bg-green-50',
        'bg-red-50',
        'text-red-600',
        'text-red-800',
        'bg-gray-50',
        'text-gray-900',
        'bg-indigo-600',
        'bg-indigo-700',
        'hover:bg-indigo-700',
        'focus:bg-indigo-700',
        'active:bg-indigo-900',
        'ring-indigo-500',
        'focus:ring-indigo-500',
        'text-indigo-600',
        'text-indigo-800',
        'bg-indigo-50',
        'bg-indigo-100',
        'bg-orange-600',
        'bg-orange-700',
        'hover:bg-orange-700',
        'focus:bg-orange-700',
        'active:bg-orange-900',
        'ring-orange-500',
        'focus:ring-orange-500',
        'bg-blue-600',
        'bg-blue-700',
        'hover:bg-blue-700',
        'focus:bg-blue-700',
        'active:bg-blue-900',
        'ring-blue-500',
        'focus:ring-blue-500',
        'text-blue-600',
        'text-blue-800',
        'bg-blue-50',
        'bg-blue-100'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
