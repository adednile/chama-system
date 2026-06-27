@extends('layouts.app')
@section('title', 'Member Dashboard')

@section('content')
<!-- Header & Stats Grid -->
<div class="space-y-stack-lg max-w-container-max mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="text-body-md text-secondary">Here's your circle performance for this month.</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-outline-variant card-shadow">
            <div class="w-12 h-12 rounded-full bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed-variant">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">stars</span>
            </div>
            <div>
                <p class="text-label-sm text-secondary uppercase tracking-wider">Credit Score</p>
                <p class="font-headline-md text-headline-md text-on-surface">{{ number_format($creditScore ?? 0, 1) }} <span class="text-xs text-secondary font-normal">/ 10</span></p>
            </div>
        </div>
    </div>

    <!-- Bento Financial Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
        <!-- Savings Balance -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant card-shadow group hover:border-primary transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 rounded-lg bg-surface-container-low text-primary">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                </div>
            </div>
            <p class="text-label-md text-secondary">Savings Balance</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">KES {{ number_format($savingsBalance ?? 0, 2) }}</p>
        </div>
        <!-- Loan Limit -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant card-shadow group hover:border-primary transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 rounded-lg bg-surface-container-low text-primary">
                    <span class="material-symbols-outlined">trending_up</span>
                </div>
                <span class="text-secondary text-label-sm font-bold bg-secondary-container px-2 py-1 rounded-full">3x Savings</span>
            </div>
            <p class="text-label-md text-secondary">Available Loan Limit</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">KES {{ number_format($loanLimit ?? 0, 2) }}</p>
        </div>
        <!-- Outstanding Loan -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant card-shadow group hover:border-primary transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 rounded-lg bg-surface-container-low text-error">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
            </div>
            <p class="text-label-md text-secondary">Outstanding Loan</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">KES {{ number_format($outstandingLoan ?? 0, 2) }}</p>
        </div>
        <!-- Unpaid Fines -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant card-shadow group hover:border-primary transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 rounded-lg bg-error-container text-error">
                    <span class="material-symbols-outlined">gavel</span>
                </div>
                @if(($unpaidFines ?? 0) > 0)
                <span class="text-error text-label-sm font-bold bg-error-container/20 px-2 py-1 rounded-full">Overdue</span>
                @endif
            </div>
            <p class="text-label-md text-secondary">Unpaid Fines</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">KES {{ number_format($unpaidFines ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Main Actions & Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
        <!-- Action Column -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Loan Status Alert -->
            @if(!$canApplyForLoan)
            <div class="bg-error-container/30 border border-error/20 p-6 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-error">warning</span>
                    <div>
                        <h3 class="font-label-md text-error font-bold mb-1">Loan Application Restricted</h3>
                        <p class="text-label-md text-on-error-container opacity-80">{{ $loanIneligibilityReason }} Please clear all pending penalties or outstanding balances to qualify.</p>
                    </div>
                </div>
                <button class="mt-4 w-full bg-slate-300 text-slate-500 py-3 rounded-lg font-bold cursor-not-allowed transition-all flex items-center justify-center gap-2" disabled>
                    <span class="material-symbols-outlined text-sm">lock</span>
                    Apply for Loan (Locked)
                </button>
            </div>
            @else
            <div class="bg-emerald-50 border border-emerald-200 p-6 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <div>
                        <h3 class="font-label-md text-emerald-800 font-bold mb-1">Loan Facility Available</h3>
                        <p class="text-label-md text-emerald-800/80">Your credit profile is active and clean. You are fully eligible to apply for loans up to your current limit.</p>
                    </div>
                </div>
                <a href="{{ route('member.loans') }}" class="mt-4 w-full gold-gradient text-white py-3 rounded-lg font-bold hover:opacity-90 transition-all flex items-center justify-center gap-2 text-center">
                    <span class="material-symbols-outlined text-sm">trending_flat</span>
                    Apply for Loan
                </a>
            </div>
            @endif

            <!-- Lipa na M-Pesa Trigger -->
            <div class="bg-white p-6 rounded-xl border border-outline-variant card-shadow">
                <h3 class="font-headline-md text-headline-md mb-2">Lipa na M-Pesa</h3>
                <p class="text-body-md text-secondary mb-4">Quickly log and verify your contributions by copying and parsing your M-Pesa receipt SMS.</p>
                <button onclick="openSmsModal()" class="open-sms-modal-trigger w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-lg font-bold shadow-sm transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">send</span> Parse Payment SMS
                </button>

                @if(isset($pendingMpesa) && count($pendingMpesa) > 0)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3">Pending Verification ({{ count($pendingMpesa) }})</h4>
                    <div class="space-y-3">
                        @foreach($pendingMpesa as $pm)
                        <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                            <div>
                                <span class="font-mono font-bold text-slate-700 block">{{ $pm->transaction_code }}</span>
                                <span class="text-slate-400 text-[10px]">{{ $pm->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-slate-800 block">KES {{ number_format($pm->amount, 2) }}</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending Review
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Growth Card -->
            <div class="relative overflow-hidden bg-on-background rounded-xl p-6 text-white card-shadow">
                <div class="relative z-10">
                    @php
                        $scorePercent = min(round((($creditScore ?? 0) / 10) * 100), 100);
                    @endphp
                    <h3 class="font-headline-md text-headline-md mb-2">Member Growth</h3>
                    <p class="text-label-md mb-6 opacity-80">Your credit rating is currently at {{ $scorePercent }}% efficiency relative to your borrowing capacity.</p>
                    <!-- Custom Progress Bar Component -->
                    <div class="w-full bg-white/20 h-4 rounded-full overflow-hidden mb-2">
                        <div class="gold-gradient h-full rounded-full" style="width: {{ $scorePercent }}%"></div>
                    </div>
                    <div class="flex justify-between text-label-sm">
                        <span>Consistency Level</span>
                        <span>{{ number_format($creditScore ?? 0, 1) }} / 10</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-outline-variant card-shadow overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-outline-variant flex justify-between items-center">
                <h3 class="font-headline-md text-headline-md">Recent Transactions</h3>
                <a href="{{ route('reports.member', auth()->user()) }}" class="text-primary font-bold text-label-md hover:underline">View Statement</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-6 py-4 text-label-sm text-secondary font-semibold uppercase">Date</th>
                            <th class="px-6 py-4 text-label-sm text-secondary font-semibold uppercase">Type</th>
                            <th class="px-6 py-4 text-label-sm text-secondary font-semibold uppercase">Reference</th>
                            <th class="px-6 py-4 text-label-sm text-secondary font-semibold uppercase">Description</th>
                            <th class="px-6 py-4 text-label-sm text-secondary font-semibold uppercase text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions ?? [] as $transaction)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-label-md whitespace-nowrap">{{ $transaction->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $typeColors = [
                                        'contribution' => 'bg-tertiary-container/10 text-tertiary border-tertiary-container/20',
                                        'repayment' => 'bg-secondary-container text-on-secondary-fixed border-secondary-container',
                                        'fine_paid' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'loan_disbursement' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    ];
                                    $colorClass = $typeColors[$transaction->type] ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase border {{ $colorClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-label-md font-mono">{{ $transaction->reference ?? '—' }}</td>
                            <td class="px-6 py-4 text-label-md text-secondary">{{ $transaction->description }}</td>
                            <td class="px-6 py-4 text-label-md font-bold text-right @if($transaction->type === 'loan_disbursement' || $transaction->type === 'fine') text-rose-700 @endif">
                                KES {{ number_format($transaction->amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                <span class="material-symbols-outlined text-3xl block mb-2 opacity-50">receipt</span>
                                No transactions recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active Loan Progress Tracker -->
    @if($activeLoan ?? false)
    @php
        $totalDues = $activeLoan->amount * (1 + ($activeLoan->interest_rate / 100));
        $repaidDues = max($totalDues - $activeLoan->outstanding_balance, 0);
        $progress = $totalDues > 0 ? min(round(($repaidDues / $totalDues) * 100), 100) : 0;
    @endphp
    <div class="bg-white p-6 rounded-xl border border-outline-variant card-shadow">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="font-headline-md text-headline-md">Active Loan Tracker</h3>
                <p class="text-label-sm text-secondary">Loan Reference: #LOAN-{{ $activeLoan->id }}</p>
            </div>
            <div class="text-right">
                <span class="text-label-sm text-secondary block">Remaining Balance</span>
                <span class="text-headline-md text-headline-md text-primary">KES {{ number_format($activeLoan->outstanding_balance, 2) }}</span>
            </div>
        </div>
        <div class="mb-4">
            <div class="flex justify-between text-label-sm mb-1">
                <span class="text-secondary font-medium">Repayment Progress ({{ $progress }}%)</span>
                <span class="text-on-surface font-bold">Total Payable: KES {{ number_format($totalDues, 2) }}</span>
            </div>
            <div class="w-full bg-slate-200 h-4 rounded-full overflow-hidden">
                <div class="h-full gold-gradient" style="width: {{ $progress }}%;"></div>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-stack-md pt-4 border-t border-slate-100">
            <div class="p-stack-md bg-surface-container-low rounded-lg">
                <p class="text-[11px] uppercase font-bold text-on-surface-variant mb-1">Interest Rate</p>
                <p class="font-label-md text-label-md font-bold">{{ number_format($activeLoan->interest_rate, 2) }}% Fixed</p>
            </div>
            <div class="p-stack-md bg-surface-container-low rounded-lg">
                <p class="text-[11px] uppercase font-bold text-on-surface-variant mb-1">Maturity Date</p>
                <p class="font-label-md text-label-md font-bold">{{ $activeLoan->maturity_date ? \Carbon\Carbon::parse($activeLoan->maturity_date)->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div class="p-stack-md bg-surface-container-low rounded-lg md:col-span-1 col-span-2">
                <p class="text-[11px] uppercase font-bold text-on-surface-variant mb-1">Status</p>
                <p class="font-label-md text-label-md font-bold text-emerald-700 capitalize">{{ $activeLoan->status }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Visual Decorative Area -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
        <div class="bg-white p-6 rounded-xl border border-outline-variant card-shadow flex gap-6 items-center">
            <div class="relative w-24 h-24 flex-shrink-0">
                <div class="absolute inset-0 bg-primary/10 rounded-full animate-pulse"></div>
                <div class="absolute inset-2 bg-primary/20 rounded-full"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-4xl">volunteer_activism</span>
                </div>
            </div>
            <div>
                <h4 class="font-headline-md text-headline-md mb-1">Community Spirit</h4>
                <p class="text-body-md text-secondary">You are an active participant of the {{ auth()->user()->chama->name ?? 'savings circle' }}. Keep up the excellent work!</p>
            </div>
        </div>
        <div class="bg-white p-0 rounded-xl border border-outline-variant card-shadow overflow-hidden relative min-h-[120px] flex items-center">
            <div class="p-6 relative z-10 w-2/3">
                <h4 class="font-headline-md text-headline-md mb-1">Active Chama</h4>
                <div class="flex items-center gap-2 text-secondary">
                    <span class="material-symbols-outlined text-sm">group</span>
                    <p class="text-label-md">{{ auth()->user()->chama->name ?? 'Default Group' }}</p>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-1/3 h-full opacity-30" style="background: linear-gradient(135deg, #d97706 0%, #b45309 50%, #78350f 100%);"></div>
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
</script>
@endpush
@endsection