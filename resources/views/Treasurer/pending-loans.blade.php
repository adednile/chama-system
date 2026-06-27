@extends('layouts.app')
@section('title', 'Pending Loan Applications')

@section('content')
<div class="space-y-6" x-data="{ rejectLoanId: null }">

    {{-- Header Card --}}
    <div class="premium-card rounded-2xl p-6 flex items-center justify-between flex-wrap gap-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-500 to-gold-600"></div>
        <div>
            <h3 class="text-base font-bold font-title text-slate-800">Pending Loan Approvals</h3>
            <p class="text-xs text-slate-400 mt-1 font-medium">Review member loan requests, check credit scores, and manage disbursements.</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-md">
            <span class="material-symbols-outlined text-sm">pending_actions</span>
            {{ $pendingLoans->count() }} Applications Pending
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
                                                   ($score >= 5 ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-rose-50 border-rose-200 text-rose-700');
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
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-xs font-bold">check</span> Approve
                                    </button>
                                </form>

                                {{-- Rejection Trigger --}}
                                <button @click="rejectLoanId === {{ $loan->id }} ? rejectLoanId = null : rejectLoanId = {{ $loan->id }}" class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                    <span class="material-symbols-outlined text-xs font-bold">close</span> Reject
                                </button>
                            </div>

                            {{-- Inline Rejection Form --}}
                            <div x-show="rejectLoanId === {{ $loan->id }}" x-cloak class="mt-3 bg-slate-50 border border-slate-200 p-4 rounded-xl text-left max-w-xs ml-auto shadow-lg">
                                <form method="POST" action="{{ route('treasurer.loans.reject', $loan) }}">
                                    @csrf
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2">Rejection Reason:</label>
                                    <textarea name="reason" rows="2" required class="w-full text-xs p-2 rounded-lg bg-white border border-slate-200 focus:ring-1 focus:ring-gold-500 focus:border-gold-500 outline-none text-slate-800 mb-3" placeholder="State reason for rejecting..."></textarea>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" @click="rejectLoanId = null" class="bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 px-2.5 py-1 rounded-lg text-[10px] font-bold transition">Cancel</button>
                                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-2.5 py-1 rounded-lg text-[10px] font-bold transition">Confirm</button>
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
