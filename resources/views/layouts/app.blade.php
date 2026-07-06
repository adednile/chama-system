<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chama Gold & Trust')</title>

    <!-- Local Assets via Vite (Tailwind CSS & Alpine.js compiled offline) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts (Google CDN with local fallbacks) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        @font-face {
            font-family: 'Material Symbols Outlined';
            font-style: normal;
            font-weight: 100 700;
            src: url('/fonts/material-symbols-outlined.woff2') format('woff2');
        }
        [x-cloak] { display: none !important; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        body {
            background-color: #f1f5f9;
            color: #0b1c30;
            font-family: 'Inter', sans-serif;
        }
        .premium-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .gold-gradient {
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%);
        }
        .gold-glow:focus {
            box-shadow: 0 0 0 2px rgba(0, 102, 255, 0.2);
            border-color: #0066ff;
        }
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }
        .sidebar-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .sidebar-link::after {
            content: '';
            position: absolute;
            left: 0;
            top: 15%;
            height: 70%;
            width: 3px;
            background: linear-gradient(180deg, #0066ff 0%, #0052cc 100%);
            border-radius: 0 4px 4px 0;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .sidebar-link.active::after {
            opacity: 1;
        }
        .sidebar-link.active {
            background: rgba(0, 82, 204, 0.08);
            color: #0052cc;
            font-weight: 600;
        }
        .gold-gradient-text {
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-gradient-btn {
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%);
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .gold-gradient-btn:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 82, 204, 0.2);
        }
        .gold-gradient-btn:active {
            transform: translateY(0);
        }
        .fade-in {
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script>
        // Dispatch a custom event so Alpine's @open-sms-modal.window handler picks it up.
        // This avoids the race condition where direct class manipulation conflicts with
        // Alpine's @click.outside listener on the modal card.
        window.openSmsModal = () => {
            window.dispatchEvent(new CustomEvent('open-sms-modal'));
        };
    </script>
    @stack('styles')
</head>
<body class="bg-slate-100 min-h-screen text-slate-700">

@auth
<!-- Sidebar -->
<aside class="h-screen fixed left-0 top-0 bg-white border-r border-slate-200 z-40 hidden md:flex flex-col py-6 px-4 shadow-sm transition-all duration-300 group ease-in-out w-20 hover:w-64">
    <div class="mb-8 px-3">
        <a href="/" class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Chama Gold Logo" class="w-10 h-10 object-contain rounded-xl shadow-md border border-slate-100 flex-shrink-0" />
            <div class="{{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : 'opacity-100' }}">
                <h1 class="text-lg font-title font-extrabold text-slate-800 tracking-tight leading-none">Chama Gold</h1>
                <p class="text-[10px] text-[#b45309] font-semibold tracking-widest uppercase mt-1">Wealth &amp; Trust</p>
            </div>
        </a>
    </div>
    
    <nav class="flex-1 space-y-1">
        @php
            $currentRoute = request()->route()->getName();
        @endphp
        <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ $currentRoute == 'dashboard' ? 'active' : '' }}">
            <span class="material-symbols-outlined text-lg flex-shrink-0">dashboard</span>
            <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Dashboard</span>
        </a>
        
        @if(auth()->user()->role === 'member')
            <a href="{{ route('member.contributions') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.contributions') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">payments</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Contributions</span>
            </a>
            <a href="{{ route('member.loans') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.loans') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">account_balance</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Loans</span>
            </a>
            <a href="{{ route('member.fines') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.fines') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">gavel</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Fines</span>
            </a>
            <a href="{{ route('member.attendance') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.attendance') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">event_available</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Attendance</span>
            </a>
        @endif

        @if(auth()->user()->role === 'treasurer')
            <div class="pt-4 pb-2 px-4 {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Administration</span>
            </div>
            <a href="{{ route('treasurer.meetings') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.meetings') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">event_available</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Meetings</span>
            </a>
            <a href="{{ route('treasurer.loans.pending') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.loans.pending') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">account_balance</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Loans</span>
            </a>
            <a href="{{ route('treasurer.sms-parser') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.sms-parser') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">sms</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">SMS Parser</span>
            </a>
            <a href="{{ route('treasurer.penalties') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.penalties') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">gavel</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Penalties</span>
            </a>
            <a href="{{ route('reports.treasurer') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'reports.treasurer') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">assessment</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Reports</span>
            </a>
            <a href="{{ route('treasurer.chama.config') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.chama.config') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg flex-shrink-0">settings</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Group Config</span>
            </a>
        @endif
    </nav>

    <div class="mt-auto pt-6 border-t border-slate-100 space-y-1">
        <a href="{{ route('profile.edit') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ $currentRoute == 'profile.edit' ? 'active' : '' }}">
            <span class="material-symbols-outlined text-lg flex-shrink-0">account_circle</span>
            <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">My Profile</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-rose-600 hover:text-rose-700 hover:bg-rose-50 w-full text-left">
                <span class="material-symbols-outlined text-lg flex-shrink-0">logout</span>
                <span class="text-sm font-medium {{ auth()->check() ? 'opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap' : '' }}">Logout</span>
            </button>
        </form>
    </div>
</aside>
@endauth

<!-- Main wrapper -->
<div class="{{ auth()->check() ? 'md:ml-20' : '' }} min-h-screen flex flex-col transition-all duration-300 ease-in-out">

    <!-- Top Bar -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-30 border-b border-slate-200 shadow-sm">
        <div class="flex justify-between items-center px-6 h-16 max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                @auth
                <button class="md:hidden text-slate-600 hover:text-slate-900" onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                @endauth
                <span class="text-lg font-title font-bold text-slate-800 tracking-wide">@yield('title', 'Chama Gold')</span>
            </div>
            
            <div class="flex items-center gap-4">
                @auth
                <span class="hidden sm:inline-block bg-digital-blue-50 border border-digital-blue-200 text-digital-blue-700 text-xs font-semibold px-3 py-1.5 rounded-xl">
                    {{ auth()->user()->chama->name ?? 'Kenya Chama' }}
                </span>
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 text-slate-600 hover:text-slate-900 focus:outline-none py-1.5 px-2 rounded-xl hover:bg-slate-100 transition">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-digital-blue-500 to-digital-blue-600 text-white flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <span class="text-xs font-semibold hidden md:inline">{{ auth()->user()->name }}</span>
                        <span class="material-symbols-outlined text-sm">keyboard_arrow_down</span>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-xl py-1 border border-slate-200 z-50 text-sm">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-slate-700 hover:text-slate-900 hover:bg-slate-50">Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-rose-600 hover:text-rose-700 hover:bg-rose-50">Logout</button>
                        </form>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Log in</a>
                    <a href="{{ route('register') }}" class="text-xs font-bold gold-gradient-btn text-white px-4 py-2.5 rounded-xl shadow-sm">Get Started</a>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="p-6 max-w-7xl mx-auto w-full flex-1 fade-in">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xl font-bold">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-rose-600">error</span>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-xl font-bold">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-outlined text-rose-600">warning</span>
                    <p class="text-sm font-bold">Please correct the following errors:</p>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 opacity-90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-sm text-slate-500 text-center">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <span class="font-bold text-slate-800 tracking-wide font-title">Chama Gold &amp; Trust</span>
                <span class="mx-2">·</span>
                <span>© {{ date('Y') }} Safe &amp; Secure Bookkeeping</span>
            </div>
            <div class="flex gap-6">
                <a href="{{ route('trust.info', ['tab' => 'privacy']) }}" class="hover:text-slate-800 transition">Privacy</a>
                <a href="{{ route('trust.info', ['tab' => 'terms']) }}" class="hover:text-slate-800 transition">Terms</a>
                <a href="{{ route('trust.info', ['tab' => 'support']) }}" class="hover:text-slate-800 transition">Support</a>
            </div>
        </div>
    </footer>
</div>

@auth
<!-- Mobile Sidebar -->
<div id="mobile-sidebar" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden md:hidden" onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white w-72 h-full p-6 overflow-y-auto shadow-2xl flex flex-col border-r border-slate-200">
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Chama Gold Logo" class="w-9 h-9 object-contain rounded-lg shadow-md border border-slate-100 flex-shrink-0" />
                <span class="text-lg font-title font-extrabold text-slate-800">Chama Gold</span>
            </div>
            <button onclick="document.getElementById('mobile-sidebar').classList.add('hidden')" class="text-2xl text-slate-500 hover:text-slate-800">&times;</button>
        </div>
        <nav class="space-y-2 flex-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ $currentRoute == 'dashboard' ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                <span class="material-symbols-outlined">dashboard</span>Dashboard
            </a>
            
            @if(auth()->user()->role === 'member')
                <a href="{{ route('member.contributions') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.contributions') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">payments</span>Contributions
                </a>
                <a href="{{ route('member.loans') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.loans') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">account_balance</span>Loans
                </a>
                <a href="{{ route('member.fines') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.fines') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">gavel</span>Fines
                </a>
                <a href="{{ route('member.attendance') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.attendance') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">event_available</span>Attendance
                </a>
            @endif
            
            @if(auth()->user()->role === 'treasurer')
                <div class="pt-4 pb-2 px-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Admin</span>
                </div>
                <a href="{{ route('treasurer.meetings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.meetings') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">event_available</span>Meetings
                </a>
                <a href="{{ route('treasurer.loans.pending') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.loans.pending') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">account_balance</span>Loans
                </a>
                <a href="{{ route('treasurer.sms-parser') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.sms-parser') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">sms</span>SMS Parser
                </a>
                <a href="{{ route('treasurer.penalties') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.penalties') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">gavel</span>Penalties
                </a>
                <a href="{{ route('reports.treasurer') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'reports.treasurer') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">assessment</span>Reports
                </a>
                <a href="{{ route('treasurer.chama.config') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.chama.config') ? 'bg-digital-blue-50 text-digital-blue-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">settings</span>Group Config
                </a>
            @endif
        </nav>
    </div>
</div>

@php
    // Resolve active loan for the payment-type selector in the SMS modal.
    $smsModalActiveLoan = $activeLoan ?? null;
    if (is_null($smsModalActiveLoan) && auth()->check() && auth()->user()->role === 'member') {
        $smsModalActiveLoan = \App\Models\Loan::where('user_id', auth()->id())
            ->whereIn('status', ['active', 'overdue'])
            ->first();
    }
@endphp
@include('partials.sms-modal', ['activeLoan' => $smsModalActiveLoan])
@endauth

@stack('scripts')
</body>
</html>