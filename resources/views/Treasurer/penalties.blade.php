@extends('layouts.app')
@section('title', 'Penalty Management')

@section('content')
<div class="space-y-6">

    {{-- Header Card --}}
    <div class="premium-card rounded-2xl p-6 flex items-center justify-between flex-wrap gap-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-500 to-gold-600"></div>
        <div>
            <h3 class="text-base font-bold font-title text-slate-800">Chama Penalties &amp; Fines</h3>
            <p class="text-xs text-slate-400 mt-1 font-medium">Manage fines assessed for late contributions or debt repayments.</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-sm">
            <span class="material-symbols-outlined text-sm font-bold">gavel</span>
            Total Accumulated Fines: Ksh {{ number_format($fines->sum('amount'), 2) }}
        </div>
    </div>

    {{-- Table Card --}}
    <div class="premium-card rounded-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-sm font-bold font-title text-slate-800">Chama Master Penalty List</h3>
            <a href="{{ route('treasurer.chama.config') }}" class="text-xs font-bold text-gold-600 hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-xs font-bold">settings</span> Configure Penalty Rules
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Member</th>
                        <th class="px-6 py-4">Penalty Type</th>
                        <th class="px-6 py-4">Assessed Amount</th>
                        <th class="px-6 py-4">Due Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($fines ?? [] as $fine)
                    <tr class="hover:bg-slate-50/50 transition font-medium">
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $fine->user->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">
                            {{ ucfirst(str_replace('_', ' ', $fine->type)) }}
                        </td>
                        <td class="px-6 py-4 font-bold text-rose-700">Ksh {{ number_format($fine->amount, 2) }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $fine->due_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = $fine->status === 'paid' ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-rose-700 bg-rose-50 border border-rose-200';
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusColor }}">
                                {{ ucfirst($fine->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($fine->status !== 'paid')
                                <form action="{{ route('treasurer.penalties.markPaid', $fine) }}" method="POST" class="inline" onsubmit="return confirm('Confirm payment of this penalty?');">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-sm">
                                        Mark Paid
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl block mb-2 opacity-50">gavel</span>
                            No penalty records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection