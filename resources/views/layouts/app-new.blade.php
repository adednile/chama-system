<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chama Gold & Trust')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- if using Vite -->
    <!-- Or include CDN links directly -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Open+Sans:wght@400;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        /* Your custom styles from the HTML */
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .card-shadow { box-shadow: 0 2px 4px rgba(26, 54, 93, 0.06); }
        /* ... copy all CSS from the design */
    </style>
    @stack('styles')
</head>
<body class="font-body-md text-on-background">
    <!-- Sidebar (fixed) -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low border-r border-outline-variant z-40 hidden md:flex flex-col py-md px-sm">
        <!-- logo, nav links -->
        <nav>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-md px-md py-sm {{ request()->routeIs('dashboard') ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} rounded-lg">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-label-md">Dashboard</span>
            </a>
            <!-- other nav items -->
        </nav>
        <!-- logout -->
    </aside>

    <!-- Main content wrapper -->
    <div class="md:ml-64 min-h-screen flex flex-col">
        <!-- Top App Bar -->
        <header class="bg-surface sticky top-0 z-50 border-b border-outline-variant shadow-sm w-full">
            <div class="flex justify-between items-center px-gutter h-16 max-w-7xl mx-auto">
                @yield('header')
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-gutter max-w-7xl mx-auto w-full flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-surface-container-highest mt-xl">
            @yield('footer')
        </footer>
    </div>

    @stack('scripts')
</body>
</html>