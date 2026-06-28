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
            background: url('/images/hero-bg.png') no-repeat center center;
            background-size: cover;
        }
        .glass-hero-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
        }
        .premium-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }
        .gold-gradient-text {
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-btn {
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%);
            color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 102, 255, 0.2);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .gold-btn:hover {
            transform: translateY(-2px);
            opacity: 0.95;
            box-shadow: 0 6px 24px rgba(0, 102, 255, 0.3);
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
        .fade-in-text {
            animation: fadeInSlide 6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .fade-in-text-slow {
            animation: fadeInSlide 10s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        @keyframes fadeInSlide {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .feature-card {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        background-color 0.4s ease,
                        border-color 0.4s ease,
                        box-shadow 0.4s ease;
        }
        .feature-card.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .feature-card:hover {
            background-color: #e5f0ff !important;
            border-color: #99c2ff !important;
            box-shadow: 0 20px 40px -15px rgba(0, 102, 255, 0.12) !important;
        }
        .feature-card .feature-desc-wrap {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1),
                        opacity 0.3s ease-out,
                        margin-top 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            margin-top: 0;
        }
        .feature-card:hover .feature-desc-wrap {
            max-height: 120px;
            opacity: 1;
            margin-top: 1rem;
        }
    </style>
</head>
<body class="font-sans antialiased selection:bg-gold-500/20 selection:text-gold-600">

    <!-- Header Navigation -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/60 transition-all">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-[#d97706] to-[#b45309] rounded-xl flex items-center justify-center text-white font-black text-xl shadow-md">G</div>
                <div>
                    <span class="text-lg font-title font-extrabold text-slate-800 tracking-tight leading-none">Chama Gold</span>
                    <p class="text-[9px] text-[#b45309] font-bold tracking-widest uppercase mt-0.5">Wealth &amp; Trust</p>
                </div>
            </a>
            
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-500">
                <a href="#stats" class="hover:text-slate-800 transition">Impact</a>
                <a href="#features" class="hover:text-slate-800 transition">Features</a>
                <a href="#benefits" class="hover:text-slate-800 transition">Benefits</a>
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
            
            <div class="lg:col-span-7 glass-hero-panel p-8 md:p-12 rounded-3xl">
                <h1 class="text-4xl md:text-6xl font-title font-black text-slate-800 leading-[1.1] mb-6">
                    Wealth Building, <br>
                    <span class="gold-gradient-text">Automated &amp; Trusted.</span>
                </h1>
                <p class="text-lg text-slate-500 mb-8 max-w-xl leading-relaxed font-medium fade-in-text">
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

                <div class="mt-12 flex items-center gap-8 fade-in-text-slow">
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

    <!-- Impact Section -->
    <section id="stats" class="py-24 bg-slate-50 border-t border-slate-200/50">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <!-- Text Content -->
            <div class="lg:col-span-6 space-y-6">
                <div>
                    <h2 class="text-xs uppercase font-bold text-gold-600 tracking-widest mb-3">Our Impact</h2>
                    <h3 class="text-xl font-black text-slate-800 tracking-tight leading-none mb-4">Mission: "Tujenge Pamoja."</h3>
                    <p class="text-3xl font-title font-extrabold text-slate-800 leading-tight">Impact on Kenyan Chamas</p>
                </div>
                <div class="space-y-4">
                    <p class="text-base text-slate-600 leading-relaxed font-medium">
                        Chama Gold aims to increase efficiency in Kenyan Chamas by replacing error-prone manual bookkeeping with a secure, centralized digital ledger.
                    </p>
                    <p class="text-base text-slate-600 leading-relaxed font-medium">
                        It automates transaction logging via an M-Pesa SMS parser, de-biases credit allocation using objective 1–10 credit scoring, and enforces financial discipline through an automated penalty engine.
                    </p>
                </div>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-3 gap-4 pt-4">
                    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm text-center feature-card">
                        <span class="block text-2xl font-black text-gold-600 font-title" data-target="99.9" data-suffix="%">0%</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Accuracy</span>
                    </div>
                    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm text-center feature-card">
                        <span class="block text-2xl font-black text-gold-600 font-title" data-target="10" data-suffix="x">0x</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Speedup</span>
                    </div>
                    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm text-center feature-card">
                        <span class="block text-2xl font-black text-gold-600 font-title" data-target="0" data-suffix="">0</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Disputes</span>
                    </div>
                </div>
            </div>

            <!-- Infographic Visual representation -->
            <div class="lg:col-span-6">
                <div class="premium-card feature-card p-6 md:p-8 rounded-3xl bg-white relative overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="font-title font-bold text-slate-800 text-lg">Chama Efficiency Trajectory</h4>
                            <p class="text-xs text-slate-400 font-medium">Comparative analysis of manual vs. digital automation</p>
                        </div>
                        <span class="text-[10px] bg-emerald-50 border border-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full">+75% Gain</span>
                    </div>

                    <!-- Line Chart Infographic (Pure CSS/SVG inline) -->
                    <div class="relative w-full aspect-[4/3] min-h-[220px]">
                        <svg class="w-full h-full" viewBox="0 0 500 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#0066ff" stop-opacity="0.15"/>
                                    <stop offset="100%" stop-color="#0066ff" stop-opacity="0.0"/>
                                </linearGradient>
                            </defs>

                            <!-- Horizontal Gridlines -->
                            <line x1="50" y1="50" x2="450" y2="50" stroke="#f1f5f9" stroke-width="1.5" />
                            <line x1="50" y1="100" x2="450" y2="100" stroke="#f1f5f9" stroke-width="1.5" />
                            <line x1="50" y1="150" x2="450" y2="150" stroke="#f1f5f9" stroke-width="1.5" />
                            <line x1="50" y1="200" x2="450" y2="200" stroke="#f1f5f9" stroke-width="1.5" />
                            <line x1="50" y1="250" x2="450" y2="250" stroke="#e2e8f0" stroke-width="1.5" />

                            <!-- Y-Axis Labels -->
                            <text x="25" y="54" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">100%</text>
                            <text x="25" y="104" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">80%</text>
                            <text x="25" y="154" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">60%</text>
                            <text x="25" y="204" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">40%</text>
                            <text x="25" y="254" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">20%</text>

                            <!-- X-Axis Labels -->
                            <text x="50" y="275" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">Month 1</text>
                            <text x="150" y="275" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">Month 2</text>
                            <text x="250" y="275" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">Month 3</text>
                            <text x="350" y="275" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">Month 4</text>
                            <text x="450" y="275" class="fill-slate-400 font-bold text-[9px]" text-anchor="middle">Month 5</text>

                            <!-- Area shading -->
                            <path d="M 50 200 L 150 170 Q 200 130, 250 80 L 350 60 L 450 45 L 450 250 L 50 250 Z" fill="url(#chartGradient)" />

                            <!-- Manual ledger line -->
                            <path d="M 50 220 L 150 215 C 190 215, 210 228, 250 210 L 350 220 L 450 205" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-dasharray="4,4" />

                            <!-- Chama Gold line -->
                            <path d="M 50 200 L 150 170 Q 200 130, 250 80 L 350 60 L 450 45" fill="none" stroke="#0066ff" stroke-width="3.5" stroke-linecap="round" />

                            <!-- Dot markers for Chama Gold -->
                            <circle cx="50" cy="200" r="5" fill="#ffffff" stroke="#0066ff" stroke-width="2.5" />
                            <circle cx="150" cy="170" r="5" fill="#ffffff" stroke="#0066ff" stroke-width="2.5" />
                            <circle cx="250" cy="80" r="5" fill="#ffffff" stroke="#0066ff" stroke-width="2.5" />
                            <circle cx="350" cy="60" r="5" fill="#ffffff" stroke="#0066ff" stroke-width="2.5" />
                            <circle cx="450" cy="45" r="5" fill="#ffffff" stroke="#0066ff" stroke-width="2.5" />

                            <!-- Dot markers for Manual -->
                            <circle cx="50" cy="220" r="4.5" fill="#ffffff" stroke="#94a3b8" stroke-width="2" />
                            <circle cx="150" cy="215" r="4.5" fill="#ffffff" stroke="#94a3b8" stroke-width="2" />
                            <circle cx="250" cy="210" r="4.5" fill="#ffffff" stroke="#94a3b8" stroke-width="2" />
                            <circle cx="350" cy="220" r="4.5" fill="#ffffff" stroke="#94a3b8" stroke-width="2" />
                            <circle cx="450" cy="205" r="4.5" fill="#ffffff" stroke="#94a3b8" stroke-width="2" />
                        </svg>
                    </div>

                    <!-- Legend -->
                    <div class="flex justify-center gap-6 mt-4 text-[11px] font-bold text-slate-500">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-1.5 rounded-full bg-[#0066ff]"></span>
                            <span>Chama Gold (Automated)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-0.5 bg-slate-400 border-t border-dashed border-slate-400"></span>
                            <span>Traditional Chama (Manual)</span>
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
                <h2 class="text-xs uppercase font-bold text-gold-600 tracking-widest mb-3">Core Features</h2>
                <p class="text-3xl md:text-4xl font-title font-extrabold text-slate-800">Tailored for Kenyan Chamas</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="premium-card feature-card p-8 rounded-2xl cursor-pointer">
                    <span class="material-symbols-outlined text-4xl text-gold-500 mb-6" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
                    <h3 class="text-lg font-bold font-title text-slate-800">Centralized Digital Ledger</h3>
                    <div class="feature-desc-wrap">
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">Track all member contributions and loans in real-time, eliminating manual calculation errors and resolving group financial disputes instantly</p>
                    </div>
                </div>
                
                <div class="premium-card feature-card p-8 rounded-2xl cursor-pointer">
                    <span class="material-symbols-outlined text-4xl text-gold-500 mb-6" style="font-variation-settings: 'FILL' 1;">sms</span>
                    <h3 class="text-lg font-bold font-title text-slate-800">SMS M-Pesa Parser</h3>
                    <div class="feature-desc-wrap">
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">Save your treasurer hours of manual entry by instantly turning copied transaction texts into complete financial ledger records.</p>
                    </div>
                </div>

                <div class="premium-card feature-card p-8 rounded-2xl cursor-pointer">
                    <span class="material-symbols-outlined text-4xl text-gold-500 mb-6" style="font-variation-settings: 'FILL' 1;">credit_score</span>
                    <h3 class="text-lg font-bold font-title text-slate-800">1-10 Credit Scoring Model</h3>
                    <div class="feature-desc-wrap">
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">Calculate fair, objective loan limits for your members instantly based on their actual savings habits and meeting attendance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-24 bg-slate-50 border-t border-slate-200/50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-xs uppercase font-bold text-gold-600 tracking-widest mb-3">Chama Benefits</h2>
                <p class="text-3xl md:text-4xl font-title font-extrabold text-slate-800">What do you stand to gain?</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Benefit 1 -->
                <div class="premium-card feature-card p-8 rounded-2xl cursor-pointer bg-white">
                    <span class="material-symbols-outlined text-4xl text-gold-500 mb-6" style="font-variation-settings: 'FILL' 1;">error_outline</span>
                    <h3 class="text-lg font-bold font-title text-slate-800">Eliminate Manual Errors and Disputes</h3>
                    <div class="feature-desc-wrap">
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">Track group savings and loans automatically in real-time, completely replacing messy physical record books forever.</p>
                    </div>
                </div>

                <!-- Benefit 2 -->
                <div class="premium-card feature-card p-8 rounded-2xl cursor-pointer bg-white">
                    <span class="material-symbols-outlined text-4xl text-gold-500 mb-6" style="font-variation-settings: 'FILL' 1;">schedule</span>
                    <h3 class="text-lg font-bold font-title text-slate-800">Save Administrative Time</h3>
                    <div class="feature-desc-wrap">
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">Reduce tedious data entry hours to seconds by instantly converting copied M-Pesa texts into records.</p>
                    </div>
                </div>

                <!-- Benefit 3 -->
                <div class="premium-card feature-card p-8 rounded-2xl cursor-pointer bg-white">
                    <span class="material-symbols-outlined text-4xl text-gold-500 mb-6" style="font-variation-settings: 'FILL' 1;">gavel</span>
                    <h3 class="text-lg font-bold font-title text-slate-800">Ensure Fair and Objective Lending</h3>
                    <div class="feature-desc-wrap">
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">Remove favoritism completely by instantly calculating customized, objective loan limits using credit scoring data.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function animateCounter(el) {
                const target = parseFloat(el.getAttribute('data-target'));
                const suffix = el.getAttribute('data-suffix') || '';
                const isFloat = target % 1 !== 0;
                let current = 0;
                const duration = 1500; // 1.5 seconds animation duration
                const start = performance.now();
                
                function update(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const ease = progress * (2 - progress); // Ease out quad
                    
                    current = ease * target;
                    
                    if (isFloat) {
                        el.textContent = current.toFixed(1) + suffix;
                    } else {
                        el.textContent = Math.floor(current) + suffix;
                    }
                    
                    if (progress < 1) {
                        requestAnimationFrame(update);
                    } else {
                        if (isFloat) {
                            el.textContent = target.toFixed(1) + suffix;
                        } else {
                            el.textContent = target + suffix;
                        }
                    }
                }
                
                requestAnimationFrame(update);
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const cards = entry.target.querySelectorAll('.feature-card');
                        cards.forEach((card, index) => {
                            setTimeout(() => {
                                card.classList.add('visible');
                                // Trigger count up animation for children with data-target
                                const counters = card.querySelectorAll('[data-target]');
                                counters.forEach(counter => {
                                    animateCounter(counter);
                                });
                            }, index * 200);
                        });
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            const targetSections = document.querySelectorAll('#stats, #features, #benefits');
            targetSections.forEach(section => {
                observer.observe(section);
            });
        });
    </script>

</body>
</html>