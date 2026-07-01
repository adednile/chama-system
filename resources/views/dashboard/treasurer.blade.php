@extends('layouts.app')
@section('title', 'Treasurer Dashboard')

@section('content')
@php
    $scoreVal = $averageCreditScore ?? 0;
    $scorePct = min(max(round($scoreVal * 10), 0), 100);
    
    if ($scoreVal == 5) {
        $statusText = 'OK';
        $colorClass = 'text-orange-500';
        $strokeColor = '#f97316'; // orange-500
        $bgBadgeClass = 'bg-orange-50 text-orange-700 border-orange-200';
    } elseif ($scoreVal > 5) {
        $statusText = 'Good';
        $colorClass = 'text-digital-blue-600';
        $strokeColor = '#0066ff'; // digital-blue-500
        $bgBadgeClass = 'bg-digital-blue-50 text-digital-blue-700 border-digital-blue-200';
    } else {
        $statusText = 'Bad';
        $colorClass = 'text-rose-600';
        $strokeColor = '#ef4444'; // rose-500
        $bgBadgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
    }
@endphp

<div class="space-y-stack-lg max-w-container-max mx-auto">

    <!-- Welcome Header & Average Credit Score Card at the top-most section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow">
        <div class="flex-1">
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="text-body-md text-secondary mt-1">Here is your Chama's financial summary for {{ now()->format('F Y') }}.</p>
        </div>
        
        <!-- Group Health Score Section inside the header -->
        <div class="flex flex-col sm:flex-row items-center gap-6 bg-digital-blue-50/50 p-4 rounded-xl border border-digital-blue-100/50 w-full lg:w-auto">
            <!-- Circular Infographic -->
            <div class="relative w-24 h-24 flex items-center justify-center flex-shrink-0">
                <svg class="w-full h-full transform -rotate-90">
                    <!-- Background circle -->
                    <circle cx="48" cy="48" r="38" stroke="#f1f5f9" stroke-width="8" fill="transparent" />
                    <!-- Foreground circle -->
                    <circle cx="48" cy="48" r="38" stroke="{{ $strokeColor }}" stroke-width="8" fill="transparent"
                            stroke-dasharray="238.76" stroke-dashoffset="{{ 238.76 * (1 - ($scoreVal / 10)) }}"
                            stroke-linecap="round" class="transition-all duration-700 ease-out" />
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="text-xl font-bold font-title {{ $colorClass }}">{{ $scorePct }}%</span>
                </div>
            </div>
            
            <!-- Rating Comment -->
            <div class="text-center sm:text-left">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $bgBadgeClass }}">
                    Group Average Score: {{ number_format($scoreVal, 1) }} / 10 ({{ $statusText }})
                </span>
                <p class="text-xs text-secondary mt-2 max-w-xs leading-relaxed">
                    @if($statusText === 'Good')
                        Your members' average score is strong! This indicates low default risk.
                    @elseif($statusText === 'OK')
                        Your members' average score is moderate. Encourage members to improve consistency.
                    @else
                        Your members' average score requires attention. Outstanding balances are affecting health scores.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Bento Financial Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter font-medium">
        
        <!-- Total Savings Pool (Click to view reports) -->
        <div onclick="window.location='{{ route('reports.treasurer') }}'" class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow group hover:border-digital-blue-500 transition-all duration-300 flex flex-col justify-between cursor-pointer relative overflow-hidden">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 rounded-lg bg-digital-blue-50 text-digital-blue-600">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <p class="text-label-md text-secondary font-medium">Total Savings Pool</p>
                <p class="font-headline-lg text-headline-lg text-on-surface mt-1">Ksh {{ number_format($totalSavings ?? 0, 2) }}</p>
            </div>
            
            <!-- Collapsed 'Run Reports' button that appears on hover -->
            <div class="mt-0 max-h-0 opacity-0 group-hover:mt-4 group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out">
                <span class="w-full gold-gradient-btn text-white py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-sm">assessment</span> Run Reports
                </span>
            </div>
        </div>

        <!-- Active Disbursed Loans (Click to view pending loans list) -->
        <div onclick="window.location='{{ route('treasurer.loans.pending') }}'" class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow group hover:border-digital-blue-500 transition-all duration-300 flex flex-col justify-between cursor-pointer relative overflow-hidden">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 rounded-lg bg-digital-blue-50 text-digital-blue-600">
                        <span class="material-symbols-outlined">account_balance</span>
                    </div>
                </div>
                <p class="text-label-md text-secondary font-medium">Active Disbursed Loans</p>
                <p class="font-headline-lg text-headline-lg text-on-surface mt-1">Ksh {{ number_format($activeLoans ?? 0, 2) }}</p>
            </div>
            
            <!-- Collapsed 'View Loan Queue' button that appears on hover -->
            <div class="mt-0 max-h-0 opacity-0 group-hover:mt-4 group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out">
                <span class="w-full gold-gradient-btn text-white py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-sm">request_quote</span> View Loan Queue
                </span>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-secondary font-medium">{{ $activeLoansCount ?? 0 }} active obligations</p>
            </div>
        </div>

        <!-- Pending App Approvals (Click to review approvals) -->
        <div onclick="window.location='{{ route('treasurer.loans.pending') }}'" class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow group hover:border-digital-blue-500 transition-all duration-300 flex flex-col justify-between cursor-pointer relative overflow-hidden">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 rounded-lg bg-digital-blue-50 text-digital-blue-600">
                        <span class="material-symbols-outlined">pending_actions</span>
                    </div>
                </div>
                <p class="text-label-md text-secondary font-medium">Pending App Approvals</p>
                <p class="font-headline-lg text-headline-lg text-on-surface mt-1">{{ $pendingApplications ?? 0 }}</p>
            </div>
            
            <!-- Collapsed 'Review Approvals' button that appears on hover -->
            <div class="mt-0 max-h-0 opacity-0 group-hover:mt-4 group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out">
                <span class="w-full gold-gradient-btn text-white py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-sm">check</span> Review Approvals
                </span>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-secondary font-medium">Require administrative review</p>
            </div>
        </div>

        <!-- Outstanding Fines (Click to manage fines) -->
        <div onclick="window.location='{{ route('treasurer.penalties') }}'" class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow group hover:border-digital-blue-500 transition-all duration-300 flex flex-col justify-between cursor-pointer relative overflow-hidden">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 rounded-lg bg-digital-blue-50 text-digital-blue-600">
                        <span class="material-symbols-outlined">gavel</span>
                    </div>
                </div>
                <p class="text-label-md text-secondary font-medium">Outstanding Fines</p>
                <p class="font-headline-lg text-headline-lg text-on-surface mt-1">Ksh {{ number_format($totalFines ?? 0, 2) }}</p>
            </div>
            
            <!-- Collapsed 'Manage Fines' button that appears on hover -->
            <div class="mt-0 max-h-0 opacity-0 group-hover:mt-4 group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out">
                <span class="w-full gold-gradient-btn text-white py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-sm">edit</span> Manage Fines
                </span>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-secondary font-medium">{{ $unpaidFinesCount ?? 0 }} overdue unpaid records</p>
            </div>
        </div>
    </div>

    <!-- Main Content Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
        
        <!-- Pending Approvals List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-digital-blue-100 card-shadow overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="px-6 py-5 border-b border-digital-blue-100 flex justify-between items-center">
                        <h4 class="font-headline-md text-headline-md text-on-surface">Pending Loan Queue</h4>
                        <a href="{{ route('treasurer.loans.pending') }}" class="text-digital-blue-600 font-bold text-label-md hover:underline flex items-center gap-1">
                            View Queue Details <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-digital-blue-50 text-secondary font-label-sm uppercase tracking-wider border-b border-digital-blue-100">
                                <tr>
                                    <th class="px-6 py-4">Applicant</th>
                                    <th class="px-6 py-4">Amount</th>
                                    <th class="px-6 py-4">Score</th>
                                    <th class="px-6 py-4">Term</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse(($pendingLoanList ?? []) as $loan)
                                <tr class="hover:bg-slate-50/50 transition font-medium">
                                    <td class="px-6 py-4 flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-digital-blue-50 border border-digital-blue-200 text-digital-blue-600 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($loan->user->name, 0, 2)) }}
                                        </div>
                                        <span class="font-bold text-slate-700">{{ $loan->user->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800">Ksh {{ number_format($loan->amount, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $score = $loan->credit_score ?? 0;
                                            $scoreColor = $score >= 7 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 
                                                         ($score >= 5 ? 'text-digital-blue-700 bg-digital-blue-50 border-digital-blue-200' : 'text-rose-700 bg-rose-50 border-rose-200');
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold border {{ $scoreColor }}">
                                            {{ $score }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $loan->term_months }} mo</td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <form method="POST" action="{{ route('treasurer.loans.approve', $loan) }}" class="inline" onsubmit="return confirm('Approve this loan?');">
                                            @csrf
                                            <button type="submit" class="bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 p-1.5 rounded-lg transition inline-flex items-center shadow-sm">
                                                <span class="material-symbols-outlined text-sm font-bold">check</span>
                                            </button>
                                        </form>
                                        <a href="{{ route('treasurer.loans.pending') }}" class="bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 p-1.5 rounded-lg transition inline-flex items-center shadow-sm">
                                            <span class="material-symbols-outlined text-sm font-bold">close</span>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <span class="material-symbols-outlined text-3xl block mb-2 opacity-30">verified_user</span>
                                        No loan applications pending review.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Quick Admin Actions -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow">
                <h4 class="font-headline-md text-headline-md mb-4 text-on-surface">Quick Admin Actions</h4>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('treasurer.meetings') }}" class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100 transition group text-center shadow-sm">
                            <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-digital-blue-600 mb-1">event_available</span>
                            <span class="text-[10px] font-bold text-slate-600">Meetings</span>
                        </a>
                        <button onclick="openSmsModal()" class="open-sms-modal-trigger flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100 transition group text-center w-full shadow-sm">
                            <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-digital-blue-600 mb-1">sms</span>
                            <span class="text-[10px] font-bold text-slate-600">Parse SMS</span>
                        </button>
                        <a href="{{ route('treasurer.loans.pending') }}" class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100 transition group text-center shadow-sm">
                            <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-digital-blue-600 mb-1">request_quote</span>
                            <span class="text-[10px] font-bold text-slate-600">Loan Queue</span>
                        </a>
                        <a href="{{ route('reports.treasurer') }}" class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100 transition group text-center shadow-sm">
                            <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-digital-blue-600 mb-1">picture_as_pdf</span>
                            <span class="text-[10px] font-bold text-slate-600">Run Reports</span>
                        </a>
                    </div>
                    
                    <button onclick="openSmsModal()" class="open-sms-modal-trigger w-full py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-sm font-bold">account_balance_wallet</span> Map M-Pesa SMS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Logs -->
    <div class="bg-white rounded-xl border border-digital-blue-100 card-shadow overflow-hidden">
        <div class="px-6 py-5 border-b border-digital-blue-100 flex flex-wrap items-center justify-between gap-4">
            <h3 class="font-headline-md text-headline-md text-on-surface">Chama Master Transaction Log</h3>
            <div class="flex gap-2">
                <input type="text" id="logSearch" placeholder="Search logs..." class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-digital-blue-500 focus:border-digital-blue-500 outline-none text-slate-800 w-48 shadow-inner">
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="logTable">
                <thead class="bg-digital-blue-50 text-secondary font-label-sm uppercase tracking-wider border-b border-digital-blue-100">
                    <tr>
                        <th class="px-6 py-4">Post Date</th>
                        <th class="px-6 py-4">Chama Member</th>
                        <th class="px-6 py-4">Transaction Type</th>
                        <th class="px-6 py-4">Reference</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($recentTransactions ?? [] as $transaction)
                    <tr class="hover:bg-slate-50/50 transition search-row font-medium">
                        <td class="px-6 py-4">{{ $transaction->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $transaction->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-xs font-bold uppercase">
                            @php
                                $typeColor = $transaction->type === 'contribution' || $transaction->type === 'repayment' ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-digital-blue-600 bg-digital-blue-50 border border-digital-blue-200';
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full border {{ $typeColor }}">
                                {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $transaction->reference ?? '—' }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">
                            Ksh {{ number_format($transaction->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl block mb-2 opacity-30">account_balance_wallet</span>
                            No transactions posted yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Micro-interactions for cards
    document.querySelectorAll('.card-shadow').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-2px)';
            card.style.transition = 'transform 0.2s ease-out';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });

    document.getElementById('logSearch').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('#logTable .search-row');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection