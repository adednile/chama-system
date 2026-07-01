@extends('layouts.app')
@section('title', 'Trust, Transparency, & Support')

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="{ 
    activeTab: '{{ $tab ?? 'privacy' }}',
    searchQuery: '',
    faqOpen: {
        faq1: false,
        faq2: false,
        faq3: false,
        faq4: false
    }
}">

    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
        <a href="{{ route('dashboard') }}" class="hover:text-digital-blue-600 transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="text-slate-800 font-medium">Trust Portal</span>
    </nav>

    <!-- 1. Hero Section (Header) -->
    <header class="bg-gradient-to-br from-digital-blue-900 via-digital-blue-950 to-slate-900 rounded-2xl p-8 md:p-12 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row items-center gap-8 border border-digital-blue-800">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-digital-blue-600 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-emerald-600 rounded-full opacity-10 blur-3xl"></div>
        
        <!-- Shield Icon -->
        <div class="p-6 bg-white/10 rounded-2xl border border-white/10 backdrop-blur-md text-digital-blue-400 flex items-center justify-center shadow-inner">
            <span class="material-symbols-outlined text-6xl">verified_user</span>
        </div>
        
        <div class="space-y-3 text-center md:text-left flex-1">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-digital-blue-500/20 text-digital-blue-300 border border-digital-blue-500/30 uppercase tracking-wider">
                Chama Security Portal
            </span>
            <h1 class="font-headline-xl text-headline-xl tracking-tight leading-tight">Trust, Transparency, and Support</h1>
            <p class="text-sm md:text-base text-slate-300 leading-relaxed max-w-2xl">
                Everything you need to know about how Chama Gold protects your group’s data, the rules of our platform, and how to get immediate help when you need it.
            </p>
        </div>
    </header>

    <!-- Navigation Style: Sticky Sub-navigation tabs (scrollable on mobile) -->
    <nav class="sticky top-16 bg-slate-100/80 backdrop-blur-md p-1.5 rounded-xl border border-slate-200 shadow-sm z-30 flex gap-1 items-center overflow-x-auto whitespace-nowrap scrollbar-none">
        <button @click="activeTab = 'privacy'" 
                :class="activeTab === 'privacy' ? 'bg-white text-digital-blue-600 font-bold shadow-sm border border-digital-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">shield</span> Privacy Policy
        </button>
        <button @click="activeTab = 'terms'" 
                :class="activeTab === 'terms' ? 'bg-white text-digital-blue-600 font-bold shadow-sm border border-digital-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">gavel</span> Terms of Service
        </button>
        <button @click="activeTab = 'support'" 
                :class="activeTab === 'support' ? 'bg-white text-digital-blue-600 font-bold shadow-sm border border-digital-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">support_agent</span> Support & Help
        </button>
    </nav>

    <!-- Content Sections -->
    <div class="space-y-6">

        <!-- 2. Section One: Privacy Policy -->
        <section x-show="activeTab === 'privacy'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-6">
            <div class="bg-white rounded-xl border border-digital-blue-100 card-shadow p-6 md:p-8 space-y-6">
                <div>
                    <h2 class="text-headline-md font-headline-md text-digital-blue-900">How We Protect Your Data</h2>
                    <p class="text-sm text-secondary mt-1">Our privacy policies clarify data custody boundaries and M-Pesa parsing guidelines.</p>
                </div>

                <!-- Ledger-Only Summary Box (Red/Rose accent for warning status) -->
                <div class="flex items-start gap-4 p-5 bg-rose-50 border border-rose-100 rounded-xl text-sm text-rose-800 shadow-sm">
                    <span class="material-symbols-outlined text-rose-500 text-2xl flex-shrink-0">warning</span>
                    <div class="space-y-1">
                        <h4 class="font-bold text-rose-900">Ledger-Only Platform Notice</h4>
                        <p class="text-rose-700 leading-relaxed">
                            Chama Gold is a bookkeeping and ledger-only application. <strong>We never hold, touch, or transfer your group's actual money.</strong> Your savings and cash pool remain securely deposited within your chosen bank accounts or mobile money channels.
                        </p>
                    </div>
                </div>

                <!-- Data Collection & Usage Table -->
                <div class="space-y-3">
                    <h3 class="text-base font-bold text-slate-800">Data Collection &amp; Usage</h3>
                    <div class="overflow-x-auto rounded-xl border border-digital-blue-100">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-digital-blue-50/50 text-digital-blue-900 font-bold border-b border-digital-blue-100">
                                    <th class="px-6 py-4">Data Type</th>
                                    <th class="px-6 py-4">How It Is Collected</th>
                                    <th class="px-6 py-4">Why We Need It</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-digital-blue-50">
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-800">Personal Details</td>
                                    <td class="px-6 py-4 text-slate-600">Entered by the Treasurer (Name, Phone, ID)</td>
                                    <td class="px-6 py-4 text-slate-600">To verify identity and create secure member profiles.</td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-800">Financial Records</td>
                                    <td class="px-6 py-4 text-slate-600">Manual entry or automated M-Pesa SMS parsing</td>
                                    <td class="px-6 py-4 text-slate-600">To update your group's centralized digital ledger and generate statements.</td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-800">System Activity</td>
                                    <td class="px-6 py-4 text-slate-600">Meeting attendance and loan repayment history</td>
                                    <td class="px-6 py-4 text-slate-600">To accurately calculate your 1–10 point loan eligibility score.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Security Assurances -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <h3 class="text-base font-bold text-slate-800">Our Security Commitments</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-digital-blue-50/20 border border-digital-blue-100/50 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-digital-blue-50 text-digital-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-base">lock</span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-800">End-to-End Encryption</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                All data transmitted between your device and our servers is fully encrypted using SSL protocols.
                            </p>
                        </div>
                        
                        <div class="p-4 rounded-xl bg-digital-blue-50/20 border border-digital-blue-100/50 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-digital-blue-50 text-digital-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-base">domain_disabled</span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-800">No Third-Party Selling</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Your financial histories, attendance, and details are strictly confidential. We never sell your data to marketing agencies.
                            </p>
                        </div>
                        
                        <div class="p-4 rounded-xl bg-digital-blue-50/20 border border-digital-blue-100/50 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-digital-blue-50 text-digital-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-base">sms</span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-800">Secure SMS Parsing</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Our M-Pesa parser only extracts code, amount, and sender details to map to the ledger. We do not read personal messages.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Section Two: Terms of Service -->
        <section x-show="activeTab === 'terms'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-6" x-cloak>
            <div class="bg-white rounded-xl border border-digital-blue-100 card-shadow p-6 md:p-8 space-y-8">
                <div>
                    <h2 class="text-headline-md font-headline-md text-digital-blue-900">Platform Rules and Responsibilities</h2>
                    <p class="text-sm text-secondary mt-1">Clear operational guidelines for Treasurers, Members, and automated processes.</p>
                </div>

                <!-- User Roles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 rounded-xl border border-digital-blue-100/60 bg-digital-blue-50/10 space-y-4">
                        <div class="flex items-center gap-2 text-digital-blue-800">
                            <span class="material-symbols-outlined">manage_accounts</span>
                            <h3 class="font-bold">Treasurer Responsibilities</h3>
                        </div>
                        <ul class="text-sm text-slate-600 space-y-2.5">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-emerald-500 text-sm mt-0.5">check_circle</span>
                                Setup and configure group bylaws (penalty rules, interest rates, weights) accurately.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-emerald-500 text-sm mt-0.5">check_circle</span>
                                Inspect and reconcile unmapped M-Pesa transactions regularly.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-emerald-500 text-sm mt-0.5">check_circle</span>
                                Review and perform administrative approvals for member loan requests.
                            </li>
                        </ul>
                    </div>

                    <div class="p-6 rounded-xl border border-digital-blue-100/60 bg-digital-blue-50/10 space-y-4">
                        <div class="flex items-center gap-2 text-digital-blue-800">
                            <span class="material-symbols-outlined">group</span>
                            <h3 class="font-bold">Member Accountability</h3>
                        </div>
                        <ul class="text-sm text-slate-600 space-y-2.5">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-emerald-500 text-sm mt-0.5">check_circle</span>
                                Keep your login credentials, passwords, and accounts secure.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-emerald-500 text-sm mt-0.5">check_circle</span>
                                Ensure M-Pesa transaction texts pasted are complete, unaltered, and match registered profiles.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-emerald-500 text-sm mt-0.5">check_circle</span>
                                Comply with group repayment terms to protect your credit score.
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- System Limitations -->
                <div class="space-y-4 pt-6 border-t border-slate-100">
                    <h3 class="text-base font-bold text-slate-800">System Limitations &amp; Liability</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        <div class="space-y-1.5">
                            <h4 class="font-bold text-slate-700">1. Automated Scoring</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                The 1–10 eligibility score is a mathematical suggestion based on historical ledger parameters. Final loan disbursement remains the sole administrative decision of the Chama Treasurer.
                            </p>
                        </div>
                        <div class="space-y-1.5">
                            <h4 class="font-bold text-slate-700">2. Service Availability</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                While we target a 99.5% uptime on servers, Chama Gold is not responsible or liable for missed group deadlines caused by external mobile network outages, M-Pesa downtime, or cellular provider delay.
                            </p>
                        </div>
                        <div class="space-y-1.5">
                            <h4 class="font-bold text-slate-700">3. Account Termination</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Group administrators (Treasurers) can initiate a request to permanently delete their Chama's database footprint at any time, which completely erases all logs, transactions, and ledger entries.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Section Three: Support & Help Center -->
        <section x-show="activeTab === 'support'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-6" x-cloak>
            <div class="bg-white rounded-xl border border-digital-blue-100 card-shadow p-6 md:p-8 space-y-6">
                <div>
                    <h2 class="text-headline-md font-headline-md text-digital-blue-900">Chama Gold Help Center</h2>
                    <p class="text-sm text-secondary mt-1">Immediate answers to troubleshooting, guides, and contact lines.</p>
                </div>

                <!-- Search Bar -->
                <div class="relative max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
                        <span class="material-symbols-outlined text-xl">search</span>
                    </span>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Search for troubleshooting, guides, or FAQs..." 
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-digital-blue-500 focus:border-digital-blue-500 text-sm shadow-sm transition-all" />
                </div>

                <!-- FAQ Dropdowns (Filtered by search query) -->
                <div class="space-y-3">
                    <h3 class="text-base font-bold text-slate-800">Frequently Asked Questions</h3>
                    
                    <!-- FAQ 1 -->
                    <div x-show="searchQuery === '' || 'unmapped'.includes(searchQuery.toLowerCase()) || 'mpesa'.includes(searchQuery.toLowerCase())" 
                         class="border border-slate-200 rounded-xl overflow-hidden shadow-sm transition">
                        <button @click="faqOpen.faq1 = !faqOpen.faq1" 
                                class="w-full p-4 text-left font-bold text-sm text-slate-700 hover:bg-slate-50 flex justify-between items-center">
                            <span>Why was my M-Pesa SMS flagged as "Unmapped"?</span>
                            <span class="material-symbols-outlined transition-transform duration-300" :class="faqOpen.faq1 ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="faqOpen.faq1" x-collapse class="p-4 bg-slate-50 border-t border-slate-100 text-sm text-slate-600 leading-relaxed">
                            A transaction is marked "Unmapped" if the system cannot automatically link the sender details (e.g., telephone number or name) to a registered member. The group Treasurer can manually review the SMS content and map it to your ledger profile.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div x-show="searchQuery === '' || 'credit'.includes(searchQuery.toLowerCase()) || 'score'.includes(searchQuery.toLowerCase()) || 'limit'.includes(searchQuery.toLowerCase())" 
                         class="border border-slate-200 rounded-xl overflow-hidden shadow-sm transition">
                        <button @click="faqOpen.faq2 = !faqOpen.faq2" 
                                class="w-full p-4 text-left font-bold text-sm text-slate-700 hover:bg-slate-50 flex justify-between items-center">
                            <span>How does the 1–10 Credit Scoring Engine work?</span>
                            <span class="material-symbols-outlined transition-transform duration-300" :class="faqOpen.faq2 ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="faqOpen.faq2" x-collapse class="p-4 bg-slate-50 border-t border-slate-100 text-sm text-slate-600 leading-relaxed">
                            Our scoring engine analyzes your savings frequency, meeting attendance rates, and promptness of loan repayments. It calculates a weighted rating between 1 and 10 to help Chama administrators decide on loan thresholds.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div x-show="searchQuery === '' || 'penalty'.includes(searchQuery.toLowerCase()) || 'rate'.includes(searchQuery.toLowerCase()) || 'bylaws'.includes(searchQuery.toLowerCase())" 
                         class="border border-slate-200 rounded-xl overflow-hidden shadow-sm transition">
                        <button @click="faqOpen.faq3 = !faqOpen.faq3" 
                                class="w-full p-4 text-left font-bold text-sm text-slate-700 hover:bg-slate-50 flex justify-between items-center">
                            <span>Can I change my group's late penalty percentage mid-month?</span>
                            <span class="material-symbols-outlined transition-transform duration-300" :class="faqOpen.faq3 ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="faqOpen.faq3" x-collapse class="p-4 bg-slate-50 border-t border-slate-100 text-sm text-slate-600 leading-relaxed">
                            Yes, group Treasurers can update configuration parameters under "Group Config" in the sidebar. Note that changes apply immediately to newly generated penalties but do not alter already issued fines.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div x-show="searchQuery === '' || 'password'.includes(searchQuery.toLowerCase()) || 'reset'.includes(searchQuery.toLowerCase())" 
                         class="border border-slate-200 rounded-xl overflow-hidden shadow-sm transition">
                        <button @click="faqOpen.faq4 = !faqOpen.faq4" 
                                class="w-full p-4 text-left font-bold text-sm text-slate-700 hover:bg-slate-50 flex justify-between items-center">
                            <span>I forgot my password, how do I reset it?</span>
                            <span class="material-symbols-outlined transition-transform duration-300" :class="faqOpen.faq4 ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="faqOpen.faq4" x-collapse class="p-4 bg-slate-50 border-t border-slate-100 text-sm text-slate-600 leading-relaxed">
                            On the login portal page, click the "Forgot Password?" link. Enter your registered email address, and a secure password reset link will be sent to your inbox.
                        </div>
                    </div>
                </div>

                <!-- Support Contact Grid (Green accents for the Parse / Support features) -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <h3 class="text-base font-bold text-slate-800">Support Channels</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        <!-- Chat Support -->
                        <div class="p-5 rounded-xl border border-emerald-100 bg-emerald-50/20 flex flex-col justify-between shadow-sm relative overflow-hidden">
                            <div class="space-y-2">
                                <span class="material-symbols-outlined text-emerald-600 text-3xl">chat</span>
                                <h4 class="font-bold text-slate-800">Live Chat</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Best for quick login diagnostics and password resets.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-emerald-100/50 flex justify-between items-center">
                                <span class="text-[10px] uppercase font-bold text-emerald-700">Response: < 5 mins</span>
                                <button class="px-3 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] shadow transition">Start Chat</button>
                            </div>
                        </div>

                        <!-- Email Support -->
                        <div class="p-5 rounded-xl border border-digital-blue-100 bg-digital-blue-50/10 flex flex-col justify-between shadow-sm">
                            <div class="space-y-2">
                                <span class="material-symbols-outlined text-digital-blue-600 text-3xl">mail</span>
                                <h4 class="font-bold text-slate-800">Email Support</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Best for detailed ledger discrepancies or database deletion requests.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-digital-blue-100/30 flex justify-between items-center">
                                <span class="text-[10px] uppercase font-bold text-digital-blue-700">Response: < 24 hrs</span>
                                <a href="mailto:support@chamagold.com" class="px-3 py-1 rounded bg-digital-blue-600 hover:bg-digital-blue-700 text-white font-bold text-[10px] text-center shadow transition">Email Us</a>
                            </div>
                        </div>

                        <!-- User Guide -->
                        <div class="p-5 rounded-xl border border-digital-blue-100 bg-digital-blue-50/10 flex flex-col justify-between shadow-sm">
                            <div class="space-y-2">
                                <span class="material-symbols-outlined text-digital-blue-600 text-3xl">menu_book</span>
                                <h4 class="font-bold text-slate-800">User Guide</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Best for learning how to configure rules, penalty rates, and mapping parameters.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-digital-blue-100/30 flex justify-between items-center">
                                <span class="text-[10px] uppercase font-bold text-digital-blue-700">Status: Online</span>
                                <button class="px-3 py-1 rounded bg-digital-blue-600 hover:bg-digital-blue-700 text-white font-bold text-[10px] shadow transition">Read Guide</button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

    </div>

</div>
@endsection
