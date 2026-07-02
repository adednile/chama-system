@extends('layouts.app')
@section('title', 'Member Dashboard')

@section('content')
@php
    $scoreVal = $creditScore ?? 0;
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
<!-- Header & Stats Grid -->
<div class="space-y-stack-lg max-w-container-max mx-auto">
        <!-- Welcome Header & Credit Score Rating Card at the top-most section -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow">
            <div class="flex-1">
                <h1 class="font-headline-lg text-headline-lg text-on-surface">Welcome back, {{ auth()->user()->name }}</h1>
                <p class="text-body-md text-secondary mt-1">Here is your financial summary for {{ now()->format('F Y') }}.</p>
            </div>
            
            <!-- Credit Score Rating Section inside the header -->
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
                        Credit Score: {{ number_format($scoreVal, 1) }} / 10 ({{ $statusText }})
                    </span>
                    <p class="text-xs text-secondary mt-2 max-w-xs leading-relaxed">
                        @if($statusText === 'Good')
                            Your score is strong! You qualify for higher loan limits.
                        @elseif($statusText === 'OK')
                            Your score is moderate. Maintain consistent savings to improve it.
                        @else
                            Your rating requires attention. Clear any outstanding balances to rebuild your score.
                        @endif
                    </p>
                </div>
            </div>
        </div>

    <!-- Bento Financial Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
        <!-- Current Savings Balance (Interactive button card, collapsed Make Contribution button) -->
        <div onclick="window.location='{{ route('member.contributions') }}'" class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow group hover:border-digital-blue-500 transition-all duration-300 flex flex-col justify-between cursor-pointer relative overflow-hidden">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 rounded-lg bg-digital-blue-50 text-digital-blue-600">
                        <span class="material-symbols-outlined">savings</span>
                    </div>
                    <!-- Mock trend indicator -->
                    <span class="text-emerald-600 text-label-sm font-bold bg-emerald-50 px-2 py-1 rounded-full flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">trending_up</span> +2.4%
                    </span>
                </div>
                <p class="text-label-md text-secondary font-medium">Current Savings Balance</p>
                <p class="font-headline-lg text-headline-lg text-on-surface mt-1">KES {{ number_format($savingsBalance ?? 0, 2) }}</p>
            </div>
            
            <!-- Collapsed 'Make Contribution' button that appears on hover -->
            <div class="mt-0 max-h-0 opacity-0 group-hover:mt-4 group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out">
                <span class="w-full gold-gradient-btn text-white py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-sm">add_circle</span> Make Contribution
                </span>
            </div>
        </div>

        <!-- Loan Eligibility Limit Card -->
        <div @if($outstandingLoan <= 0) onclick="window.location='{{ route('member.loans') }}'" @endif class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow group hover:border-digital-blue-500 transition-all duration-300 flex flex-col justify-between @if($outstandingLoan <= 0) cursor-pointer @endif relative overflow-hidden">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 rounded-lg bg-digital-blue-50 text-digital-blue-600">
                        <span class="material-symbols-outlined">trending_up</span>
                    </div>
                </div>
                <p class="text-label-md text-secondary font-medium">Loan Eligibility Limit</p>
                <p class="font-headline-lg text-headline-lg text-on-surface mt-1">KES {{ number_format($loanLimit ?? 0, 2) }}</p>
            </div>
            
            <!-- Collapsed 'Apply for Loan' button that appears on hover -->
            <div class="mt-0 max-h-0 opacity-0 group-hover:mt-4 group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out">
                @if($outstandingLoan > 0)
                    <button class="w-full bg-slate-200 text-slate-500 py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 cursor-not-allowed" disabled>
                        <span class="material-symbols-outlined text-sm">lock</span> Apply for Loan (Locked)
                    </button>
                @else
                    <span class="w-full gold-gradient-btn text-white py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-sm">trending_flat</span> Apply for Loan
                    </span>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-secondary font-medium">Based on 3x savings multiplier</p>
            </div>
        </div>

        @php
            $nextInstallment = $activeLoan ? $activeLoan->amortizationSchedule()->where('payment_status', 'pending')->orderBy('due_date')->first() : null;
        @endphp
        <!-- Outstanding Loan Balance Card (White, interactive if loan outstanding) -->
        <div @if($outstandingLoan > 0) onclick="openSmsModal(); event.stopPropagation();" @endif class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow group hover:border-digital-blue-500 transition-all duration-300 flex flex-col justify-between @if($outstandingLoan > 0) cursor-pointer @endif relative overflow-hidden">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 rounded-lg bg-digital-blue-50 text-digital-blue-600">
                        <span class="material-symbols-outlined">pending_actions</span>
                    </div>
                </div>
                <p class="text-label-md text-secondary font-medium">Outstanding Loan Balance</p>
                <p class="font-headline-lg text-headline-lg text-on-surface mt-1">KES {{ number_format($outstandingLoan ?? 0, 2) }}</p>
            </div>
            
            @if($outstandingLoan > 0)
                <!-- Collapsed 'Make Loan Payment' button that appears on hover -->
                <div class="mt-0 max-h-0 opacity-0 group-hover:mt-4 group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out">
                    <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-sm">payment</span> Make Loan Payment
                    </button>
                </div>
            @endif

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                @if($outstandingLoan > 0 && $activeLoan && $nextInstallment)
                    <div class="text-xs text-secondary">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Next due:</span>
                        <span class="font-bold text-slate-700">{{ $nextInstallment->due_date->format('d M Y') }}</span>
                    </div>
                @else
                    <div class="text-xs text-emerald-600 font-semibold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                        No active loan
                    </div>
                @endif
            </div>
        </div>

        <!-- Unpaid Fines Card (White, interactive if fines exist) -->
        <div @if(($unpaidFines ?? 0) > 0) onclick="openSmsModal(); event.stopPropagation();" @endif class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow group hover:border-digital-blue-500 transition-all duration-300 flex flex-col justify-between @if(($unpaidFines ?? 0) > 0) cursor-pointer @endif relative overflow-hidden">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 rounded-lg bg-digital-blue-50 text-digital-blue-600">
                        <span class="material-symbols-outlined">gavel</span>
                    </div>
                </div>
                <p class="text-label-md text-secondary font-medium">Unpaid Fines</p>
                <p class="font-headline-lg text-headline-lg text-on-surface mt-1">KES {{ number_format($unpaidFines ?? 0, 2) }}</p>
            </div>
            
            @if(($unpaidFines ?? 0) > 0)
                <!-- Collapsed 'Pay Fine' button that appears on hover -->
                <div class="mt-0 max-h-0 opacity-0 group-hover:mt-4 group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out">
                    <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-sm">payment</span> Pay Fine
                    </button>
                </div>
            @endif

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center">
                @if(($unpaidFines ?? 0) > 0)
                    <span class="text-error text-xs font-bold bg-error-container/20 px-2 py-1 rounded-full flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">warning</span> Overdue penalties
                    </span>
                @else
                    <span class="text-emerald-600 text-xs font-semibold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span> All clear
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
        <!-- Left Column: Transactions -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-digital-blue-100 card-shadow overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-6 py-5 border-b border-digital-blue-100 flex justify-between items-center">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Recent Transactions</h3>
                    <a href="{{ route('reports.member', auth()->user()) }}" class="text-digital-blue-600 font-bold text-label-md hover:underline flex items-center gap-1">
                        View All <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-digital-blue-50">
                            <tr>
                                <th class="px-6 py-4 text-label-sm text-secondary font-semibold uppercase">Date</th>
                                <th class="px-6 py-4 text-label-sm text-secondary font-semibold uppercase">Description</th>
                                <th class="px-6 py-4 text-label-sm text-secondary font-semibold uppercase">Type</th>
                                <th class="px-6 py-4 text-label-sm text-secondary font-semibold uppercase text-right">Amount (Ksh)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentTransactions ?? [] as $transaction)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-label-md whitespace-nowrap text-secondary">{{ $transaction->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-label-md font-medium text-on-surface">
                                    <div class="font-semibold text-slate-800">{{ $transaction->description }}</div>
                                    @if($transaction->reference)
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">M-Pesa Ref: {{ $transaction->reference }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $typeColors = [
                                            'contribution' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'repayment' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'fine_paid' => 'bg-digital-blue-50 text-digital-blue-700 border-digital-blue-200',
                                            'loan_disbursement' => 'bg-rose-50 text-rose-800 border-rose-100',
                                        ];
                                        $typeLabels = [
                                            'contribution' => 'Savings',
                                            'repayment' => 'Loan Payment',
                                            'fine_paid' => 'Penalty Paid',
                                            'loan_disbursement' => 'Disbursement',
                                        ];
                                        $colorClass = $typeColors[$transaction->type] ?? 'bg-slate-50 text-slate-700 border-slate-100';
                                        $label = $typeLabels[$transaction->type] ?? ucfirst(str_replace('_', ' ', $transaction->type));
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $colorClass }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                @php
                                    $isNegative = in_array($transaction->type, ['loan_disbursement', 'fine']);
                                    $amountPref = $isNegative ? '-' : '+';
                                    $amountColor = $isNegative ? 'text-rose-600' : 'text-emerald-600';
                                @endphp
                                <td class="px-6 py-4 text-label-md font-bold text-right {{ $amountColor }} whitespace-nowrap">
                                    {{ $amountPref }}{{ number_format($transaction->amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl block mb-2 opacity-30">receipt_long</span>
                                    No transactions recorded yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Active Loan Tracker & SMS parser -->
        <div class="lg:col-span-1 space-y-6">


            <!-- Active Loan Status Card -->
            <div class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow">
                <h3 class="font-headline-md text-headline-md mb-4 text-on-surface">Active Loan Status</h3>
                
                @if($activeLoan)
                    @php
                        $totalDues = $activeLoan->amount * (1 + ($activeLoan->interest_rate / 100));
                        $repaidDues = min($activeLoan->repayments()->sum('repayment_amount'), $totalDues);
                        $progress = $totalDues > 0 ? min(round(($repaidDues / $totalDues) * 100), 100) : 0;
                    @endphp
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-label-sm mb-1.5">
                                <span class="text-secondary font-medium">Development Loan</span>
                                <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-md text-[11px]">{{ $progress }}% Paid</span>
                            </div>
                            <p class="text-xs text-slate-400 mb-2 font-medium">KES {{ number_format($repaidDues, 2) }} / KES {{ number_format($totalDues, 2) }}</p>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="h-full bg-digital-blue-600" style="width: {{ $progress }}%;"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Monthly Installment</span>
                                <span class="text-sm font-bold text-slate-800">KES {{ number_format($totalDues / ($activeLoan->term_months ?: 12), 2) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Interest Rate</span>
                                <span class="text-sm font-bold text-slate-800">{{ number_format($activeLoan->interest_rate, 1) }}% fixed</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Start Date</span>
                                <span class="text-sm font-bold text-slate-800">{{ $activeLoan->approved_at ? $activeLoan->approved_at->format('d M Y') : $activeLoan->created_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Expected Clearance</span>
                                <span class="text-sm font-bold text-slate-800">{{ $activeLoan->maturity_date ? \Carbon\Carbon::parse($activeLoan->maturity_date)->format('d M Y') : 'N/A' }}</span>
                            </div>
                        </div>

                        <a href="{{ route('member.loans') }}" class="mt-4 w-full bg-white border border-slate-200 text-slate-700 py-2.5 rounded-lg font-bold text-xs hover:bg-slate-50 transition-colors flex items-center justify-center gap-1.5 shadow-sm text-center">
                            View Amortization Schedule
                        </a>
                    </div>
                @else
                    <div class="text-center py-6 text-slate-400">
                        <span class="material-symbols-outlined text-4xl block mb-2 opacity-30">account_balance</span>
                        <p class="text-sm font-medium text-slate-700">No active loan facility</p>
                        <p class="text-xs text-slate-400 mt-1">Need cash? You can request a loan if your eligibility is active.</p>
                        <a href="{{ route('member.loans') }}" class="mt-4 inline-block text-digital-blue-600 font-bold text-xs hover:underline">
                            Request Loan facility
                        </a>
                    </div>
                @endif
            </div>

            <!-- Lipa na M-Pesa Trigger -->
            <div class="bg-white p-6 rounded-xl border border-digital-blue-100 card-shadow">
                <h3 class="font-headline-md text-headline-md mb-2 text-on-surface">Lipa na M-Pesa</h3>
                <p class="text-xs text-secondary mb-4 leading-relaxed">Quickly log and verify your contributions by copying and parsing your M-Pesa receipt SMS.</p>
                <button onclick="openSmsModal()" class="open-sms-modal-trigger w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-lg font-bold shadow-sm transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">send</span> Parse SMS Receipt
                </button>

                @if(isset($pendingMpesa) && count($pendingMpesa) > 0)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pending Verification ({{ count($pendingMpesa) }})</h4>
                    <div class="space-y-2">
                        @foreach($pendingMpesa as $pm)
                        <div class="flex justify-between items-center bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-xs">
                            <div>
                                <span class="font-mono font-bold text-slate-700 block">{{ $pm->transaction_code }}</span>
                                <span class="text-slate-400 text-[10px]">{{ $pm->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-slate-800 block">KES {{ number_format($pm->amount, 2) }}</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-digital-blue-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-digital-blue-500 animate-pulse"></span> Pending Review
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
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