@extends('layouts.app')
@section('title', 'Pending Loan Applications')

@section('content')
<div class="space-y-6" x-data="{ rejectLoanId: null }">

    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
        <a href="{{ route('dashboard') }}" class="hover:text-digital-blue-600 transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="text-slate-800 font-medium">Pending Loan Applications</span>
    </nav>

    {{-- Header Card --}}
    <div class="premium-card rounded-2xl p-6 flex items-center justify-between flex-wrap gap-4 relative overflow-hidden mb-6">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-digital-blue-500 to-digital-blue-600"></div>
        <div>
            <h3 class="text-base font-bold font-title text-slate-800">Pending Loan Approvals</h3>
            <p class="text-xs text-slate-400 mt-1 font-medium">Review member loan requests, check credit scores, and manage disbursements.</p>
        </div>
        <div class="bg-digital-blue-50 border border-digital-blue-200 text-digital-blue-700 text-xs font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-md">
            <span class="material-symbols-outlined text-sm">pending_actions</span>
            {{ $pendingLoans->count() }} Applications Pending
        </div>
    </div>

    {{-- Liquidity vs Disbursed Card --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Liquidity Card -->
        <div class="premium-card rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Chama Liquidity</span>
                    <h3 class="text-2xl font-extrabold font-title text-slate-800 mt-1">Ksh {{ number_format($availableCashPool, 2) }}</h3>
                    <p class="text-[10px] text-slate-500 mt-1 font-medium">Available cash pool for new loans</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-2xl font-bold">account_balance_wallet</span>
                </div>
            </div>
        </div>

        <!-- Disbursed Loans Card -->
        <div class="premium-card rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-digital-blue-500 to-digital-blue-600"></div>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Disbursed Loans</span>
                    <h3 class="text-2xl font-extrabold font-title text-slate-800 mt-1">Ksh {{ number_format($loansDisbursed, 2) }}</h3>
                    <p class="text-[10px] text-slate-500 mt-1 font-medium">Total active and completed loans</p>
                </div>
                <div class="w-12 h-12 bg-digital-blue-50 text-digital-blue-600 rounded-2xl flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-2xl font-bold">handshake</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="premium-card rounded-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-sm font-bold font-title text-slate-800">Active Application Queue</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Date Applied</th>
                        <th class="px-6 py-4">Member Info</th>
                        <th class="px-6 py-4">Amount Requested</th>
                        <th class="px-6 py-4">Term</th>
                        <th class="px-6 py-4">Credit Score</th>
                        <th class="px-6 py-4">Purpose</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($pendingLoans ?? [] as $loan)
                    <tr class="hover:bg-slate-50/50 transition align-top font-medium">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $loan->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gold-50 border border-gold-200 text-gold-600 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($loan->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $loan->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold">{{ $loan->user->phone ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                            Ksh {{ number_format($loan->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $loan->term_months }} months</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $score = $loan->credit_score ?? 0;
                                 $scoreColorClass = $score >= 7 ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 
                                                    ($score >= 5 ? 'bg-digital-blue-50 border-digital-blue-200 text-digital-blue-700' : 'bg-rose-50 border-rose-200 text-rose-700');
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $scoreColorClass }}">
                                {{ number_format($score, 1) }} / 10
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500 max-w-xs break-words">
                            {{ $loan->reason ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-right space-y-2 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Approval Form --}}
                                <form method="POST" action="{{ route('treasurer.loans.approve', $loan) }}" onsubmit="return confirm('Approve this loan of Ksh {{ number_format($loan->amount, 2) }}?');">
                                    @csrf
                                    <button type="submit" class="bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-xs font-bold">check</span> Approve
                                    </button>
                                </form>

                                {{-- Rejection Trigger --}}
                                <button @click="rejectLoanId === {{ $loan->id }} ? rejectLoanId = null : rejectLoanId = {{ $loan->id }}" class="bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                    <span class="material-symbols-outlined text-xs font-bold">close</span> Reject
                                </button>
                            </div>

                            {{-- Inline Rejection Form --}}
                            <div x-show="rejectLoanId === {{ $loan->id }}" x-cloak class="mt-3 bg-slate-50 border border-slate-200 p-4 rounded-xl text-left max-w-xs ml-auto shadow-lg">
                                <form method="POST" action="{{ route('treasurer.loans.reject', $loan) }}">
                                    @csrf
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2">Rejection Reason:</label>
                                    <textarea name="reason" rows="2" required class="w-full text-xs p-2 rounded-lg bg-white border border-slate-200 focus:ring-1 focus:ring-digital-blue-500 focus:border-digital-blue-500 outline-none text-slate-800 mb-3" placeholder="State reason for rejecting..."></textarea>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" @click="rejectLoanId = null" class="bg-digital-blue-50 hover:bg-digital-blue-100 border border-digital-blue-200 text-digital-blue-700 px-2.5 py-1 rounded-lg text-[10px] font-bold transition">Cancel</button>
                                        <button type="submit" class="bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-lg text-[10px] font-bold transition">Confirm</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl block mb-2 opacity-50">verified</span>
                            No pending loan applications.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
