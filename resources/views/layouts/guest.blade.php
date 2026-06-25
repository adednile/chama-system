<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Chama Gold') }}</title>

    <!-- Tailwind + Fonts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#0b0f19',
                        dark: '#151d30',
                        gold: {
                            50: '#fefdf0',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        },
                        brand: {
                            navy: '#0b0f19',
                            dark: '#151d30',
                            gold: '#c59b27',
                            goldlight: '#e5c060',
                            emerald: '#10b981',
                            rose: '#f43f5e',
                            slate: '#334155'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        title: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0b0f19;
            color: #f1f5f9;
        }
        .hero-bg {
            background: radial-gradient(circle at 50% 10%, rgba(197, 155, 39, 0.08) 0%, transparent 60%), #0b0f19;
        }
        .premium-card {
            background: rgba(21, 29, 48, 0.65);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body class="font-sans antialiased hero-bg min-h-screen flex flex-col justify-center items-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
        <a href="/" class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gold-500 to-gold-700 rounded-2xl mb-4 shadow-lg">
            <span class="material-symbols-outlined text-brand-navy text-4xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
        </a>
        <h2 class="text-2xl font-title font-extrabold text-white tracking-tight">Chama Gold &amp; Trust</h2>
    </div>

    <div class="w-full sm:max-w-md px-4">
        <div class="premium-card py-8 px-6 sm:px-10 rounded-2xl border border-white/5 relative overflow-hidden text-slate-300">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-500 to-gold-700"></div>
            {{ $slot }}
        </div>
    </div>
</body>
</html>
