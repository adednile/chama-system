<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Chama Gold') }} - Automated Financial Ledger & Loan Management</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800;900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ffffff',
                        secondary: '#f8fafc',
                        gold: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            500: '#d97706',
                            600: '#b45309',
                            700: '#78350f',
                        },
                        brand: {
                            navy: '#f1f5f9',
                            dark: '#ffffff',
                            gold: '#b45309',
                            goldlight: '#d97706',
                            emerald: '#059669',
                            rose: '#e11d48',
                            slate: '#475569'
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
            background-color: #f1f5f9;
            color: #334155;
        }
        .hero-bg {
            background: radial-gradient(circle at 70% 30%, rgba(217, 119, 6, 0.05) 0%, transparent 60%),
                        radial-gradient(circle at 10% 80%, rgba(5, 150, 105, 0.02) 0%, transparent 50%),
                        #f1f5f9;
        }
        .premium-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }
        .gold-gradient-text {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-btn {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            box-shadow: 0 4px 20px rgba(180, 83, 9, 0.2);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .gold-btn:hover {
            transform: translateY(-2px);
            opacity: 0.95;
            box-shadow: 0 6px 24px rgba(180, 83, 9, 0.3);
        }
        .pulse-emerald {
            animation: pulse-emerald-anim 2s infinite;
        }
        @keyframes pulse-emerald-anim {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.08); opacity: 0.8; }
        }
        .float-delayed {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
    </style>
</head>
<body class="font-sans antialiased selection:bg-gold-500/20 selection:text-gold-600">

    <!-- Header Navigation -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/60 transition-all">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-gold-500 to-gold-600 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-md">G</div>
                <div>
                    <span class="text-lg font-title font-extrabold text-slate-800 tracking-tight leading-none">Chama Gold</span>
                    <p class="text-[9px] text-gold-600 font-bold tracking-widest uppercase mt-0.5">Wealth &amp; Trust</p>
                </div>
            </a>
            
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-500">
                <a href="#features" class="hover:text-slate-800 transition">Platform Features</a>
                <a href="#benefits" class="hover:text-slate-800 transition">Chama Benefits</a>
                <a href="#stats" class="hover:text-slate-800 transition">Impact &amp; Security</a>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="gold-btn px-6 py-2.5 rounded-xl font-bold text-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-800 font-bold text-sm transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="gold-btn px-6 py-2.5 rounded-xl font-bold text-sm">Register Chama</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg min-h-screen flex items-center pt-24 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-6 py-20 w-full relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <div class="lg:col-span-7">
                <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2 rounded-full text-xs font-semibold mb-6">
                    <span class="w-2.5 h-2.5 bg-emerald-600 rounded-full pulse-emerald"></span>
                    Smart Ledger System for Kenyan Chamas
                </div>
                <h1 class="text-4xl md:text-6xl font-title font-black text-slate-800 leading-[1.1] mb-6">
                    Wealth Building, <br>
                    <span class="gold-gradient-text">Automated &amp; Trusted.</span>
                </h1>
                <p class="text-lg text-slate-500 mb-8 max-w-xl leading-relaxed font-medium">
                    Say goodbye to manual books, spreadsheets, and calculations. Automate your Chama’s ledger bookkeeping, credit scoring, late penalties, and M-Pesa SMS mapping.
                </p>
                
                <div class="flex flex-wrap gap-4 items-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="gold-btn px-8 py-4 rounded-xl font-bold inline-flex items-center gap-2">
                            Go to Member Portal <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="gold-btn px-8 py-4 rounded-xl font-bold inline-flex items-center gap-2 shadow-md">
                            Register Your Group <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                        <a href="#features" class="border border-slate-200 hover:bg-slate-100 bg-white text-slate-700 px-8 py-4 rounded-xl font-bold transition shadow-sm">
                            Explore Features
                        </a>
                    @endauth
                </div>

                <div class="mt-12 flex items-center gap-8">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center font-bold text-slate-600 text-xs">AM</div>
                        <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center font-bold text-slate-600 text-xs">KW</div>
                        <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center font-bold text-slate-600 text-xs">JO</div>
                        <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center font-bold text-slate-600 text-xs">SM</div>
                    </div>
                    <p class="text-xs text-slate-500 font-semibold leading-relaxed">
                        Helping <span class="text-slate-800 font-bold">50+ local savings circles</span> manage group liquidity, minimize delays, and secure member savings.
                    </p>
                </div>
            </div>

            <!-- Visual Dashboard Preview -->
            <div class="lg:col-span-5 relative">
                <div class="float-delayed">
                    <div class="premium-card rounded-2xl p-6 shadow-xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-500 to-gold-600"></div>
                        
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            </div>
                            <span class="text-[10px] bg-slate-100 border border-slate-200 text-slate-500 px-3 py-1 rounded-full font-bold">Live Preview</span>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                <span class="text-xs text-slate-400 block mb-1 font-semibold">Chama Account Balance</span>
                                <div class="flex justify-between items-end">
                                    <span class="text-2xl font-title font-extrabold text-slate-800">Ksh 1,248,500</span>
                                    <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full font-bold">+14.2%</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                                    <span class="text-[10px] text-slate-400 block mb-1 font-semibold">Active Loans</span>
                                    <span class="text-sm font-bold text-slate-800">Ksh 480,000</span>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                                    <span class="text-[10px] text-slate-400 block mb-1 font-semibold">Overdue Fines</span>
                                    <span class="text-sm font-bold text-rose-700">Ksh 3,500</span>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-slate-500 font-semibold">Smart Scoring Engine</span>
                                    <span class="font-bold text-gold-600">8.4 / 10</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-1.5">
                                    <div class="bg-gradient-to-r from-gold-500 to-gold-600 h-1.5 rounded-full" style="width: 84%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white border-t border-slate-200/50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-xs uppercase font-bold text-gold-600 tracking-widest mb-3">Complete System Features</h2>
                <p class="text-3xl md:text-4xl font-title font-extrabold text-slate-800">Designed Specifically for the Needs of Kenyan Chamas</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="premium-card p-8 rounded-2xl">
                    <span class="material-symbols-outlined text-4xl text-gold-500 mb-6" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
                    <h3 class="text-lg font-bold font-title text-slate-800 mb-2">Centralized Digital Ledger</h3>
                    <p class="text-slate-500 text-sm leading-relaxed font-medium">Immutable transaction mapping logging all member contributions, repayments, and penalties. Zero-variance balance integrity matches computed pool totals.</p>
                </div>
                
                <div class="premium-card p-8 rounded-2xl">
                    <span class="material-symbols-outlined text-4xl text-gold-500 mb-6" style="font-variation-settings: 'FILL' 1;">sms</span>
                    <h3 class="text-lg font-bold font-title text-slate-800 mb-2">SMS M-Pesa Parser</h3>
                    <p class="text-slate-500 text-sm leading-relaxed font-medium">Paste raw Safaricom M-Pesa SMS confirmation messages. The engine auto-extracts the amount, sender, date, and reference code for rapid ledger mapping.</p>
                </div>

                <div class="premium-card p-8 rounded-2xl">
                    <span class="material-symbols-outlined text-4xl text-gold-500 mb-6" style="font-variation-settings: 'FILL' 1;">credit_score</span>
                    <h3 class="text-lg font-bold font-title text-slate-800 mb-2">1-10 Credit Scoring Model</h3>
                    <p class="text-slate-500 text-sm leading-relaxed font-medium">Objective credit scoring calculated dynamically from savings consistency, repayment punctuality, and meeting attendance. Custom scoring weights.</p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>