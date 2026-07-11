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
            fontFamily: {
                sans: ['"Segoe UI Variable"', '"Segoe UI"', 'Roboto', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                accent: {
                    DEFAULT: '#0078D4',
                    light: '#E8F0FE',
                    hover: '#106EBE',
                    pressed: '#005A9E',
                    50: '#F0F6FC',
                    100: '#DEEAF7',
                    200: '#BBD6F0',
                    300: '#8ABBE7',
                    400: '#5A9DD8',
                    500: '#0078D4',
                    600: '#106EBE',
                    700: '#005A9E',
                    800: '#004578',
                    900: '#003360',
                },
                neutral: {
                    50: '#FAFAFA',
                    100: '#F5F5F5',
                    200: '#E8E8E8',
                    300: '#D1D1D1',
                    400: '#ADADAD',
                    500: '#808080',
                    600: '#616161',
                    700: '#424242',
                    800: '#292929',
                    900: '#1F1F1F',
                },
                card: {
                    DEFAULT: '#FFFFFF',
                    secondary: '#F7F7F7',
                    tertiary: '#F0F0F0',
                },
                surface: {
                    DEFAULT: '#F3F3F3',
                    elevated: '#F9F9F9',
                },
            },
            borderRadius: {
                'fluent': '8px',
                'fluent-lg': '12px',
                'fluent-xl': '16px',
            },
            boxShadow: {
                'fluent-sm': '0 1px 2px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.06)',
                'fluent': '0 2px 4px rgba(0,0,0,0.04), 0 4px 8px rgba(0,0,0,0.06)',
                'fluent-md': '0 4px 8px rgba(0,0,0,0.04), 0 8px 16px rgba(0,0,0,0.08)',
                'fluent-lg': '0 8px 16px rgba(0,0,0,0.04), 0 16px 32px rgba(0,0,0,0.08)',
                'fluent-xl': '0 12px 24px rgba(0,0,0,0.05), 0 24px 48px rgba(0,0,0,0.10)',
            },
            animation: {
                'fluent-in': 'fluentIn 0.3s cubic-bezier(0.33, 0, 0.67, 1)',
                'fluent-out': 'fluentOut 0.2s cubic-bezier(0.33, 0, 0.67, 1)',
                'reveal': 'reveal 0.15s ease-out',
            },
            keyframes: {
                fluentIn: {
                    '0%': { opacity: '0', transform: 'scale(0.96) translateY(4px)' },
                    '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                },
                fluentOut: {
                    '0%': { opacity: '1', transform: 'scale(1)' },
                    '100%': { opacity: '0', transform: 'scale(0.96)' },
                },
                reveal: {
                    '0%': { opacity: '0', transform: 'translateY(2px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};