import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Maintify Brand Colors
                primary: {
                    DEFAULT: '#410008',
                    50:  '#fff0f1',
                    100: '#ffe0e3',
                    200: '#ffc5cb',
                    300: '#ff9aa4',
                    400: '#ff5f71',
                    500: '#ff2d44',
                    600: '#ed1130',
                    700: '#c80926',
                    800: '#a50a23',
                    900: '#5E0B15',
                    950: '#410008',
                },
                // Dark Theme Base
                dark: {
                    bg:      '#121414',
                    surface: '#1E2020',
                    card:    '#252828',
                    border:  '#2E3030',
                    hover:   '#2A2D2D',
                    muted:   '#3A3D3D',
                },
                // Text Palette
                content: {
                    primary:   '#F4F4F5',
                    secondary: '#A1A1AA',
                    muted:     '#71717A',
                    inverse:   '#0A0A0B',
                },
                // State Colors
                success: {
                    DEFAULT: '#22C55E',
                    bg:      '#052E16',
                },
                warning: {
                    DEFAULT: '#F59E0B',
                    bg:      '#1C1400',
                },
                danger: {
                    DEFAULT: '#EF4444',
                    bg:      '#1F0707',
                },
                info: {
                    DEFAULT: '#3B82F6',
                    bg:      '#0A1628',
                },
            },
            borderRadius: {
                DEFAULT: '12px',
                sm:  '8px',
                md:  '12px',
                lg:  '16px',
                xl:  '20px',
                '2xl': '24px',
            },
            boxShadow: {
                'soft':    '0 2px 12px 0 rgba(0,0,0,0.25)',
                'card':    '0 4px 24px 0 rgba(0,0,0,0.35)',
                'primary': '0 4px 20px 0 rgba(65,0,8,0.4)',
                'glow':    '0 0 20px rgba(65,0,8,0.3)',
            },
            spacing: {
                'sidebar': '240px',
                'topbar':  '64px',
                'bottomnav': '64px',
            },
            screens: {
                'xs': '390px',
                ...defaultTheme.screens,
            },
            transitionDuration: {
                DEFAULT: '200ms',
            },
            animation: {
                'fade-in':     'fadeIn 0.3s ease-out',
                'slide-up':    'slideUp 0.3s ease-out',
                'slide-in':    'slideIn 0.3s ease-out',
                'pulse-soft':  'pulseSoft 2s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%':   { transform: 'translateY(12px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)',    opacity: '1' },
                },
                slideIn: {
                    '0%':   { transform: 'translateX(-12px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)',      opacity: '1' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%':      { opacity: '0.6' },
                },
            },
        },
    },

    plugins: [forms],
};
