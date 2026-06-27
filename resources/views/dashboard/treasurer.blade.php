@extends('layouts.app')
@section('title', 'Treasurer Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Summary Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 font-medium">
        <div class="premium-card rounded-2xl p-5 border-l-4 border-blue-500 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total Savings Pool</span>
                <span class="material-symbols-outlined text-blue-500 text-lg">payments</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-title font-black text-slate-800">Ksh {{ number_format($totalSavings ?? 0, 2) }}</span>
            </div>
        </div>
        <div class="premium-card rounded-2xl p-5 border-l-4 border-emerald-500 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Active Disbursed Loans</span>
                <span class="material-symbols-outlined text-emerald-600 text-lg">account_balance</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-title font-black text-emerald-700">Ksh {{ number_format($activeLoans ?? 0, 2) }}</span>
                <span class="text-xs text-slate-400 block mt-1 font-medium">{{ $activeLoansCount ?? 0 }} active obligations</span>
            </div>
        </div>
        <div class="premium-card rounded-2xl p-5 border-l-4 border-amber-500 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Pending App Approvals</span>
                <span class="material-symbols-outlined text-amber-600 text-lg">pending_actions</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-title font-black text-amber-700">{{ $pendingApplications ?? 0 }}</span>
                <span class="text-xs text-slate-400 block mt-1 font-medium">Require administrative review</span>
            </div>
        </div>
        <div class="premium-card rounded-2xl p-5 border-l-4 border-rose-500 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Outstanding Fines</span>
                <span class="material-symbols-outlined text-rose-600 text-lg">gavel</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-title font-black text-rose-700">Ksh {{ number_format($totalFines ?? 0, 2) }}</span>
                <span class="text-xs text-slate-400 block mt-1 font-medium">{{ $unpaidFinesCount ?? 0 }} overdue unpaid records</span>
            </div>
        </div>
    </div>

    {{-- Pending Loan Approvals & Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Pending Approvals List --}}
        <div class="lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-sm font-bold font-title text-slate-800 tracking-wide">Pending Loan Queue</h4>
                <a href="{{ route('treasurer.loans.pending') }}" class="text-xs font-bold text-gold-600 hover:underline">View Queue Details</a>
            </div>
            <div class="premium-card rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Applicant</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Score</th>
                            <th class="px-4 py-3">Term</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @forelse(($pendingLoanList ?? []) as $loan)
                        <tr class="hover:bg-slate-50/50 transition font-medium">
                            <td class="px-4 py-3 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gold-50 border border-gold-200 text-gold-600 flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($loan->user->name, 0, 2)) }}
                                </div>
                                <span class="font-bold text-slate-700">{{ $loan->user->name }}</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800">Ksh {{ number_format($loan->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $score = $loan->credit_score ?? 0;
                                    $scoreColor = $score >= 7 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 
                                                 ($score >= 5 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-rose-700 bg-rose-50 border-rose-200');
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold border {{ $scoreColor }}">
                                    {{ $score }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $loan->term_months }} mo</td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <form method="POST" action="{{ route('treasurer.loans.approve', $loan) }}" class="inline" onsubmit="return confirm('Approve this loan?');">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 hover:bg-emerald-50 p-1.5 rounded-lg border border-slate-200 bg-slate-50 transition inline-flex items-center shadow-sm">
                                        <span class="material-symbols-outlined text-sm font-bold">check</span>
                                    </button>
                                </form>
                                <a href="{{ route('treasurer.loans.pending') }}" class="text-rose-600 hover:bg-rose-50 p-1.5 rounded-lg border border-slate-200 bg-slate-50 transition inline-flex items-center shadow-sm">
                                    <span class="material-symbols-outlined text-sm font-bold">close</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                <span class="material-symbols-outlined text-2xl block mb-2 opacity-50">verified_user</span>
                                No loan applications pending review.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div>
            <h4 class="text-sm font-bold font-title text-slate-800 mb-4">Quick Admin Actions</h4>
            <div class="premium-card p-5 rounded-2xl space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('treasurer.meetings') }}" class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100 transition group text-center shadow-sm">
                        <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-gold-600 mb-1">event_available</span>
                        <span class="text-[10px] font-bold text-slate-600">Meetings</span>
                    </a>
                    <button onclick="openSmsModal()" class="open-sms-modal-trigger flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100 transition group text-center w-full shadow-sm">
                        <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-gold-600 mb-1">sms</span>
                        <span class="text-[10px] font-bold text-slate-600">Parse SMS</span>
                    </button>
                    <a href="{{ route('treasurer.loans.pending') }}" class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100 transition group text-center shadow-sm">
                        <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-gold-600 mb-1">request_quote</span>
                        <span class="text-[10px] font-bold text-slate-600">Loan Queue</span>
                    </a>
                    <a href="{{ route('reports.treasurer') }}" class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100 transition group text-center shadow-sm">
                        <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-gold-600 mb-1">picture_as_pdf</span>
                        <span class="text-[10px] font-bold text-slate-600">Run Reports</span>
                    </a>
                </div>
                
                <button onclick="openSmsModal()" class="open-sms-modal-trigger w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-sm font-bold">account_balance_wallet</span> Map M-Pesa SMS
                </button>
            </div>
        </div>
    </div>

    {{-- Recent Transactions Logs --}}
    <div class="premium-card rounded-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-sm font-bold font-title text-slate-800">Chama Master Transaction Log</h3>
            <div class="flex gap-2">
                <input type="text" id="logSearch" placeholder="Search logs..." class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-gold-500 focus:border-gold-500 outline-none text-slate-800 w-48 shadow-inner">
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="logTable">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
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
                                $typeColor = $transaction->type === 'contribution' || $transaction->type === 'repayment' ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-gold-600 bg-amber-50 border border-amber-200';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full border {{ $typeColor }}">
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
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl block mb-2 opacity-50">account_balance_wallet</span>
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