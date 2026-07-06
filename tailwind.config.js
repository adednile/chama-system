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
                // Stitch UI Tokens
                "surface-container-highest": "#d3e4fe",
                "on-primary-fixed": "#2f1500",
                "surface-container-lowest": "#ffffff",
                "primary-fixed-dim": "#ffb77d",
                "surface-bright": "#f8f9ff",
                "surface-container-low": "#eff4ff",
                "on-secondary-container": "#5c647a",
                "primary": "#8d4b00",
                "surface-dim": "#cbdbf5",
                "on-tertiary-fixed-variant": "#005137",
                "on-background": "#0b1c30",
                "inverse-surface": "#213145",
                "on-tertiary-fixed": "#002114",
                "error": "#ba1a1a",
                "inverse-on-surface": "#eaf1ff",
                "tertiary-fixed": "#85f8c4",
                "background": "#f8f9ff",
                "on-surface": "#0b1c30",
                "secondary-fixed": "#dae2fd",
                "tertiary-container": "#00855d",
                "secondary-container": "#dae2fd",
                "surface-container-high": "#dce9ff",
                "surface-tint": "#904d00",
                "inverse-primary": "#ffb77d",
                "on-secondary-fixed-variant": "#3f465c",
                "on-tertiary": "#ffffff",
                "primary-fixed": "#ffdcc3",
                "on-tertiary-container": "#f5fff7",
                "on-error": "#ffffff",
                "outline-variant": "#dbc2b0",
                "error-container": "#ffdad6",
                "on-primary-fixed-variant": "#6e3900",
                "on-primary": "#ffffff",
                "on-primary-container": "#fffbff",
                "secondary-fixed-dim": "#bec6e0",
                "secondary": "#565e74",
                "primary-container": "#b15f00",
                "on-surface-variant": "#554336",
                "on-secondary": "#ffffff",
                "on-secondary-fixed": "#131b2e",
                "surface-container": "#e5eeff",
                "tertiary-fixed-dim": "#68dba9",
                "outline": "#887364",
                "surface": "#f8f9ff",
                "surface-variant": "#d3e4fe",
                "tertiary": "#006948",
                "on-error-container": "#93000a",
                "slate-custom": "#f1f5f9",
                "gold-gradient-start": "#0066ff",
                "gold-gradient-end": "#0052cc",

                "digital-blue": {
                    "50": "#e5f0ff",
                    "100": "#cce0ff",
                    "200": "#99c2ff",
                    "300": "#66a3ff",
                    "400": "#3385ff",
                    "500": "#0066ff",
                    "600": "#0052cc",
                    "700": "#003d99",
                    "800": "#002966",
                    "900": "#001433",
                    "950": "#000e24"
                },

                // Legacy compatibility tokens
                gold: {
                    50: '#e5f0ff',
                    100: '#cce0ff',
                    200: '#99c2ff',
                    300: '#66a3ff',
                    400: '#3385ff',
                    500: '#0066ff',
                    600: '#0052cc',
                    700: '#003d99',
                    800: '#002966',
                    900: '#001433',
                    950: '#000e24',
                },
                brand: {
                    navy: '#f1f5f9',
                    dark: '#ffffff',
                    gold: '#0052cc',
                    goldlight: '#0066ff',
                    emerald: '#059669',
                    rose: '#e11d48',
                    slate: '#475569'
                }
            },
            borderRadius: {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
            spacing: {
                "container-max": "1280px",
                "stack-sm": "0.5rem",
                "gutter": "1.5rem",
                "margin-desktop": "2.5rem",
                "stack-md": "1rem",
                "stack-xs": "0.25rem",
                "margin-mobile": "1rem",
                "stack-lg": "2rem"
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif', ...defaultTheme.fontFamily.sans],
                title: ['Outfit', 'sans-serif'],
                "label-md": ["Inter"],
                "label-sm": ["Inter"],
                "body-lg": ["Inter"],
                "headline-lg": ["Outfit"],
                "headline-lg-mobile": ["Outfit"],
                "headline-md": ["Outfit"],
                "headline-xl": ["Outfit"],
                "body-md": ["Inter"]
            },
            fontSize: {
                "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500"}],
                "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "600"}],
                "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                "headline-xl": ["40px", {"lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}]
            }
        },
    },

    plugins: [forms],
};
