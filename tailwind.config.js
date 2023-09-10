const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        'bg-green-100',
        'text-green-800',
        'bg-red-100',
        'text-red-800',
        'bg-gray-100',
        'text-gray-800',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
    mode: process.env.NODE_ENV ? 'jit' : undefined,
}
