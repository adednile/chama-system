<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ChamaWealth | Secure Access</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "surface-dim": "#cbdbf5",
                      "primary-fixed-dim": "#ffb77d",
                      "outline": "#887364",
                      "on-tertiary": "#ffffff",
                      "surface-bright": "#f8f9ff",
                      "on-secondary-fixed": "#131b2e",
                      "surface-container-lowest": "#ffffff",
                      "on-error-container": "#93000a",
                      "error": "#ba1a1a",
                      "secondary-container": "#dae2fd",
                      "on-tertiary-container": "#f5fff7",
                      "on-primary-container": "#fffbff",
                      "secondary-fixed": "#dae2fd",
                      "on-secondary-container": "#5c647a",
                      "surface-container": "#e5eeff",
                      "on-primary-fixed-variant": "#6e3900",
                      "surface-variant": "#d3e4fe",
                      "tertiary-fixed-dim": "#68dba9",
                      "tertiary-container": "#00855d",
                      "on-background": "#0b1c30",
                      "inverse-surface": "#213145",
                      "on-primary-fixed": "#2f1500",
                      "surface-tint": "#904d00",
                      "secondary": "#565e74",
                      "on-primary": "#ffffff",
                      "inverse-primary": "#ffb77d",
                      "error-container": "#ffdad6",
                      "surface-container-highest": "#d3e4fe",
                      "on-secondary-fixed-variant": "#3f465c",
                      "tertiary": "#006948",
                      "outline-variant": "#dbc2b0",
                      "primary": "#8d4b00",
                      "on-tertiary-fixed-variant": "#005137",
                      "surface-container-low": "#eff4ff",
                      "primary-fixed": "#ffdcc3",
                      "inverse-on-surface": "#eaf1ff",
                      "surface": "#f8f9ff",
                      "on-error": "#ffffff",
                      "on-tertiary-fixed": "#002114",
                      "primary-container": "#b15f00",
                      "tertiary-fixed": "#85f8c4",
                      "on-secondary": "#ffffff",
                      "on-surface-variant": "#554336",
                      "secondary-fixed-dim": "#bec6e0",
                      "background": "#f8f9ff",
                      "surface-container-high": "#dce9ff",
                      "on-surface": "#0b1c30"
              },
              "borderRadius": {
                      "DEFAULT": "0.25rem",
                      "lg": "0.5rem",
                      "xl": "0.75rem",
                      "full": "9999px"
              },
              "spacing": {
                      "stack-md": "1rem",
                      "margin-desktop": "2.5rem",
                      "gutter": "1.5rem",
                      "stack-sm": "0.5rem",
                      "stack-xs": "0.25rem",
                      "stack-lg": "2rem",
                      "margin-mobile": "1rem",
                      "container-max": "1280px"
              },
              "fontFamily": {
                      "headline-xl": ["Outfit"],
                      "headline-lg": ["Outfit"],
                      "body-md": ["Inter"],
                      "headline-md": ["Outfit"],
                      "label-sm": ["Inter"],
                      "body-lg": ["Inter"],
                      "headline-lg-mobile": ["Outfit"],
                      "label-md": ["Inter"]
              },
              "fontSize": {
                      "headline-xl": ["40px", {"lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                      "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                      "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                      "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                      "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "600"}],
                      "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                      "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                      "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500"}]
              }
            },
          },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .gold-gradient {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
        .auth-card-transition {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-input-focus:focus {
            border-color: #d97706;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
            outline: none;
        }
    </style>
</head>
<body class="bg-[#f1f5f9] font-body-md text-on-background min-h-screen flex items-center justify-center p-4 sm:p-gutter relative overflow-hidden">

<div class="w-full max-w-container-max mx-auto grid lg:grid-cols-12 gap-stack-lg items-center relative z-10">
    <!-- Brand Narrative Section (Desktop Only) -->
    <div class="hidden lg:flex lg:col-span-6 flex-col gap-stack-md pr-stack-lg">
        <div class="flex items-center gap-stack-sm mb-stack-md">
            <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
            <h1 class="font-headline-xl text-headline-xl text-primary tracking-tight">ChamaWealth</h1>
        </div>
        <h2 class="font-headline-lg text-headline-lg text-on-background max-w-md">The communal engine for your group's financial future.</h2>
        <p class="font-body-lg text-body-lg text-secondary max-w-lg">Experience the next generation of social banking in Kenya. Secure, transparent, and built for communal prosperity.</p>
        <div class="mt-stack-lg grid grid-cols-2 gap-stack-md">
            <div class="bg-surface-container-low p-stack-md border border-outline-variant rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">security</span>
                <p class="font-label-md text-label-md text-on-surface">Secure Ledger</p>
            </div>
            <div class="bg-surface-container-low p-stack-md border border-outline-variant rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">trending_up</span>
                <p class="font-label-md text-label-md text-on-surface">Growth Tracking</p>
            </div>
        </div>
    </div>

    <!-- Auth Canvas -->
    <div class="lg:col-span-6 w-full flex justify-center">
        <div class="w-full max-w-[480px] bg-white rounded-xl shadow-sm border border-secondary-container p-8 sm:p-10 auth-card-transition overflow-hidden">
            
            <!-- Session Flash Messages -->
            @if(session('status'))
            <div class="mb-stack-md p-stack-sm bg-tertiary-fixed text-on-tertiary-fixed-variant rounded-lg font-label-md flex items-center gap-stack-sm">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-stack-md p-stack-sm bg-error-container text-on-error-container rounded-lg font-label-md flex items-center gap-stack-sm">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <!-- Form Tabs Toggle -->
            <div class="flex bg-surface-container-low p-1 rounded-lg mb-stack-lg">
                <button class="flex-1 py-2 font-label-md rounded-md transition-all text-secondary" id="toggleLogin" onclick="showAuth('login')">Login</button>
                <button class="flex-1 py-2 font-label-md rounded-md transition-all bg-white text-primary shadow-sm" id="toggleRegister" onclick="showAuth('register')">Register</button>
            </div>

            <!-- Login Form -->
            <div class="auth-view hidden" id="loginSection">
                <h3 class="font-headline-md text-headline-md text-on-background mb-stack-sm">Welcome back</h3>
                <p class="font-body-md text-body-md text-secondary mb-stack-lg">Log in to manage your group contributions.</p>
                <form action="{{ route('login') }}" class="space-y-stack-md" method="POST">
                    @csrf
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface-variant mb-stack-xs" for="email">Email Address</label>
                        <input class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-body-md form-input-focus transition-all" id="email" name="email" placeholder="name@example.com" required="" type="email" value="{{ old('email') }}"/>
                    </div>
                    <div>
                        <div class="flex justify-between mb-stack-xs">
                            <label class="block font-label-md text-label-md text-on-surface-variant" for="password">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-label-md font-label-md text-primary hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
                            @endif
                        </div>
                        <input class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-body-md form-input-focus transition-all" id="password" name="password" placeholder="••••••••" required="" type="password"/>
                    </div>
                    <div class="flex items-center gap-stack-sm py-stack-xs">
                        <input class="w-4 h-4 rounded text-primary border-outline-variant focus:ring-primary" id="remember" name="remember" type="checkbox"/>
                        <label class="font-label-md text-label-md text-secondary select-none" for="remember">Remember me for 30 days</label>
                    </div>
                    <button class="w-full h-12 gold-gradient text-white font-label-md rounded-lg shadow-sm hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-stack-sm" type="submit">
                        Sign In
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </button>
                </form>
            </div>

            <!-- Register Form -->
            <div class="auth-view" id="registerSection">
                <h3 class="font-headline-md text-headline-md text-on-background mb-stack-sm">Start a Journey</h3>
                <p class="font-body-md text-body-md text-secondary mb-stack-lg">Join a Chama or create a new savings group today.</p>
                <form action="{{ route('register') }}" class="space-y-stack-md" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-stack-md">
                        <div>
                            <label class="block font-label-md text-label-md text-on-surface-variant mb-stack-xs" for="reg_name">Full Name</label>
                            <input class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-body-md form-input-focus transition-all" id="reg_name" name="name" placeholder="John Doe" required="" type="text" value="{{ old('name') }}"/>
                        </div>
                        <div>
                            <label class="block font-label-md text-label-md text-on-surface-variant mb-stack-xs" for="reg_email">Email</label>
                            <input class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-body-md form-input-focus transition-all" id="reg_email" name="email" placeholder="john@example.com" required="" type="email" value="{{ old('email') }}"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-stack-md">
                        <div>
                            <label class="block font-label-md text-label-md text-on-surface-variant mb-stack-xs" for="reg_pass">Password</label>
                            <input class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-body-md form-input-focus transition-all" id="reg_pass" name="password" placeholder="••••••••" required="" type="password"/>
                        </div>
                        <div>
                            <label class="block font-label-md text-label-md text-on-surface-variant mb-stack-xs" for="password_confirmation">Confirm</label>
                            <input class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-body-md form-input-focus transition-all" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required="" type="password"/>
                        </div>
                    </div>
                    <div class="pt-stack-xs border-t border-secondary-container mt-stack-md">
                        <label class="block font-label-md text-label-md text-on-surface-variant mb-stack-sm">Membership Type</label>
                        <select class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-body-md form-input-focus transition-all" id="registration_type" name="registration_type" onchange="toggleChamaLogic()">
                            <option value="join" {{ old('registration_type') === 'join' ? 'selected' : '' }}>Join an Existing Chama</option>
                            <option value="create" {{ old('registration_type') === 'create' ? 'selected' : '' }}>Create a New Chama</option>
                        </select>
                    </div>

                    <!-- Conditional: Join Chama -->
                    <div class="space-y-stack-xs" id="join_logic">
                        <label class="block font-label-md text-label-md text-on-surface-variant mb-stack-xs" for="chama_id">Select Your Group</label>
                        <select class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-body-md form-input-focus transition-all" id="chama_id" name="chama_id">
                            <option value="">-- Choose Chama --</option>
                            @foreach($chamas ?? [] as $c)
                                <option value="{{ $c->id }}" {{ old('chama_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->location }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Conditional: Create Chama -->
                    <div class="hidden animate-in fade-in slide-in-from-top-2 duration-300" id="create_logic">
                        <label class="block font-label-md text-label-md text-on-surface-variant mb-stack-xs" for="new_chama_name">Chama Name</label>
                        <input class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-body-md form-input-focus transition-all" id="new_chama_name" name="new_chama_name" placeholder="e.g. Prosper Wealth Circle" type="text" value="{{ old('new_chama_name') }}"/>
                    </div>

                    <button class="w-full h-12 gold-gradient text-white font-label-md rounded-lg shadow-sm hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-stack-sm mt-stack-md" type="submit">
                        Create Account
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                    </button>
                </form>
            </div>

            <!-- Social Proof Footer -->
            <div class="mt-stack-lg pt-stack-md border-t border-outline-variant flex flex-col items-center gap-stack-sm">
                <p class="font-label-sm text-label-sm text-secondary uppercase tracking-widest">Trusted by groups across Kenya</p>
                <div class="flex gap-stack-md opacity-40 grayscale filter">
                    <img class="h-6" alt="Secure Ledger" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBd16bLf7v9JUJWLO4fAtedVlvG8YcOA4Pemv9sDBRw-2aaKk0uD6OEbkcv16vk7VjnGrV6mkIJk7AEVx8TPK6K_zWDCUEbASY8xscS7rN7SLLg1xngzdecomYhrV8bCnuWwg9vrugXG8FWHtbS48xe62YTI9p5NZW9HySpcwuFY8mIXU2G7meGvq9qQT5qVeGdxSGvGpuPiRlnT5dphQgIZ054mXrlV0XAxIAczhlPNWv9AYR84wCvVayFpXcOQPMHU1jPfyU92Zk"/>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showAuth(type) {
        const loginSection = document.getElementById('loginSection');
        const registerSection = document.getElementById('registerSection');
        const toggleLogin = document.getElementById('toggleLogin');
        const toggleRegister = document.getElementById('toggleRegister');

        if (type === 'login') {
            loginSection.classList.remove('hidden');
            registerSection.classList.add('hidden');
            toggleLogin.classList.add('bg-white', 'text-primary', 'shadow-sm');
            toggleLogin.classList.remove('text-secondary');
            toggleRegister.classList.remove('bg-white', 'text-primary', 'shadow-sm');
            toggleRegister.classList.add('text-secondary');
        } else {
            loginSection.classList.add('hidden');
            registerSection.classList.remove('hidden');
            toggleRegister.classList.add('bg-white', 'text-primary', 'shadow-sm');
            toggleRegister.classList.remove('text-secondary');
            toggleLogin.classList.remove('bg-white', 'text-primary', 'shadow-sm');
            toggleLogin.classList.add('text-secondary');
        }
    }

    function toggleChamaLogic() {
        const type = document.getElementById('registration_type').value;
        const joinLogic = document.getElementById('join_logic');
        const createLogic = document.getElementById('create_logic');

        if (type === 'create') {
            joinLogic.classList.add('hidden');
            createLogic.classList.remove('hidden');
        } else {
            joinLogic.classList.remove('hidden');
            createLogic.classList.add('hidden');
        }
    }

    // Set initial register type state
    document.addEventListener('DOMContentLoaded', () => {
        toggleChamaLogic();
        showAuth('register');
    });
</script>
</body>
</html>