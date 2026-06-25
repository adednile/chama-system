<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chama Gold & Trust')</title>

    <!-- Tailwind + Fonts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            darkMode: "class",
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
                        "gold-gradient-start": "#d97706",
                        "gold-gradient-end": "#b45309",

                        // Legacy compatibility tokens
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
                        sans: ['Inter', 'sans-serif'],
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
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
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
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
        .gold-glow:focus {
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.2);
            border-color: #d97706;
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
            background: linear-gradient(180deg, #d97706 0%, #b45309 100%);
            border-radius: 0 4px 4px 0;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .sidebar-link.active::after {
            opacity: 1;
        }
        .sidebar-link.active {
            background: rgba(180, 83, 9, 0.08);
            color: #b45309;
            font-weight: 600;
        }
        .gold-gradient-text {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-gradient-btn {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .gold-gradient-btn:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(180, 83, 9, 0.2);
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

<!-- Sidebar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-white border-r border-slate-200 z-40 hidden md:flex flex-col py-6 px-4 shadow-sm">
    <div class="mb-8 px-3">
        <a href="/" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-br from-gold-500 to-gold-600 rounded-xl flex items-center justify-center text-white font-extrabold text-xl shadow-md">G</div>
            <div>
                <h1 class="text-lg font-title font-extrabold text-slate-800 tracking-tight leading-none">Chama Gold</h1>
                <p class="text-[10px] text-gold-600 font-semibold tracking-widest uppercase mt-1">Wealth &amp; Trust</p>
            </div>
        </a>
    </div>
    
    <nav class="flex-1 space-y-1">
        @php
            $currentRoute = request()->route()->getName();
        @endphp
        <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ $currentRoute == 'dashboard' ? 'active' : '' }}">
            <span class="material-symbols-outlined text-lg">dashboard</span>
            <span class="text-sm font-medium">Dashboard</span>
        </a>
        
        @if(auth()->user()->role === 'member')
            <a href="{{ route('member.contributions') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.contributions') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg">payments</span>
                <span class="text-sm font-medium">Contributions</span>
            </a>
            <a href="{{ route('member.loans') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.loans') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg">account_balance</span>
                <span class="text-sm font-medium">Loans</span>
            </a>
            <a href="{{ route('member.attendance') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.attendance') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg">event_available</span>
                <span class="text-sm font-medium">Attendance</span>
            </a>
        @endif

        @if(auth()->user()->role === 'treasurer')
            <div class="pt-4 pb-2 px-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Administration</span>
            </div>
            <a href="{{ route('treasurer.meetings') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.meetings') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg">event_available</span>
                <span class="text-sm font-medium">Meetings</span>
            </a>
            <a href="{{ route('treasurer.sms-parser') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.sms-parser') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg">sms</span>
                <span class="text-sm font-medium">SMS Parser</span>
            </a>
            <a href="{{ route('treasurer.penalties') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.penalties') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg">gavel</span>
                <span class="text-sm font-medium">Penalties</span>
            </a>
            <a href="{{ route('reports.treasurer') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'reports.treasurer') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg">assessment</span>
                <span class="text-sm font-medium">Reports</span>
            </a>
            <a href="{{ route('treasurer.chama.config') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.chama.config') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-lg">settings</span>
                <span class="text-sm font-medium">Group Config</span>
            </a>
        @endif
    </nav>

    <div class="mt-auto pt-6 border-t border-slate-100 space-y-1">
        <a href="{{ route('profile.edit') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ $currentRoute == 'profile.edit' ? 'active' : '' }}">
            <span class="material-symbols-outlined text-lg">account_circle</span>
            <span class="text-sm font-medium">My Profile</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-rose-600 hover:text-rose-700 hover:bg-rose-50 w-full text-left">
                <span class="material-symbols-outlined text-lg">logout</span>
                <span class="text-sm font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Main wrapper -->
<div class="md:ml-64 min-h-screen flex flex-col">

    <!-- Top Bar -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-30 border-b border-slate-200 shadow-sm">
        <div class="flex justify-between items-center px-6 h-16 max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                <button class="md:hidden text-slate-600 hover:text-slate-900" onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <span class="text-lg font-title font-bold text-slate-800 tracking-wide">@yield('title', 'Chama Gold')</span>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="hidden sm:inline-block bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-xl">
                    {{ auth()->user()->chama->name ?? 'Kenya Chama' }}
                </span>
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 text-slate-600 hover:text-slate-900 focus:outline-none py-1.5 px-2 rounded-xl hover:bg-slate-100 transition">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gold-500 to-gold-600 text-white flex items-center justify-center font-bold text-xs">
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
                <a href="#" class="hover:text-slate-800 transition">Privacy</a>
                <a href="#" class="hover:text-slate-800 transition">Terms</a>
                <a href="#" class="hover:text-slate-800 transition">Support</a>
            </div>
        </div>
    </footer>
</div>

<!-- Mobile Sidebar -->
<div id="mobile-sidebar" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden md:hidden" onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white w-72 h-full p-6 overflow-y-auto shadow-2xl flex flex-col border-r border-slate-200">
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 bg-gradient-to-br from-gold-500 to-gold-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">G</div>
                <span class="text-lg font-title font-extrabold text-slate-800">Chama Gold</span>
            </div>
            <button onclick="document.getElementById('mobile-sidebar').classList.add('hidden')" class="text-2xl text-slate-500 hover:text-slate-800">&times;</button>
        </div>
        <nav class="space-y-2 flex-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ $currentRoute == 'dashboard' ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                <span class="material-symbols-outlined">dashboard</span>Dashboard
            </a>
            
            @if(auth()->user()->role === 'member')
                <a href="{{ route('member.contributions') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.contributions') ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">payments</span>Contributions
                </a>
                <a href="{{ route('member.loans') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.loans') ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">account_balance</span>Loans
                </a>
                <a href="{{ route('member.attendance') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'member.attendance') ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">event_available</span>Attendance
                </a>
            @endif
            
            @if(auth()->user()->role === 'treasurer')
                <div class="pt-4 pb-2 px-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Admin</span>
                </div>
                <a href="{{ route('treasurer.meetings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.meetings') ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">event_available</span>Meetings
                </a>
                <a href="{{ route('treasurer.sms-parser') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.sms-parser') ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">sms</span>SMS Parser
                </a>
                <a href="{{ route('treasurer.penalties') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.penalties') ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">gavel</span>Penalties
                </a>
                <a href="{{ route('reports.treasurer') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'reports.treasurer') ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">assessment</span>Reports
                </a>
                <a href="{{ route('treasurer.chama.config') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 {{ str_starts_with($currentRoute, 'treasurer.chama.config') ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                    <span class="material-symbols-outlined">settings</span>Group Config
                </a>
            @endif
        </nav>
    </div>
</div>

@php
    // Resolve active loan for the payment-type selector in the SMS modal.
    // On the member dashboard $activeLoan is already computed by DashboardService,
    // so we reuse it. On other member pages we do one lightweight query.
    $smsModalActiveLoan = $activeLoan ?? null;
    if (is_null($smsModalActiveLoan) && auth()->check() && auth()->user()->role === 'member') {
        $smsModalActiveLoan = \App\Models\Loan::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();
    }
@endphp
@include('partials.sms-modal', ['activeLoan' => $smsModalActiveLoan])

@stack('scripts')
</body>
</html>