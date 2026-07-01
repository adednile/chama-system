@extends('layouts.app')
@section('title', 'Fines History')

@section('content')
<div class="space-y-stack-lg max-w-container-max mx-auto">

    {{-- Header --}}
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-digital-blue-600 transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-slate-800 font-medium">Fines History</span>
            </nav>
            <h1 class="font-headline-lg text-headline-lg text-digital-blue-900">Fines History</h1>
            <p class="font-body-md text-body-md text-secondary">View your penalties history and submit M-Pesa payments for verification.</p>
        </div>
        
        {{-- Primary action: open the SMS parser modal --}}
        <button onclick="openSmsModal()"
                class="open-sms-modal-trigger flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl shadow-sm transition-all">
            <span class="material-symbols-outlined">sms</span>
            Pay Fine via SMS
        </button>
    </header>

    @php
        $pendingFinesSum = $fines->where('status', 'pending')->sum('amount');
    @endphp

    {{-- Info banners with Red/Green accents --}}
    @if($pendingFinesSum > 0)
        <div class="flex items-start gap-4 p-4 bg-rose-50 border border-rose-100 rounded-xl text-sm text-rose-800">
            <span class="material-symbols-outlined text-rose-500 mt-0.5 flex-shrink-0">warning</span>
            <div class="flex-1">
                <p class="font-semibold text-rose-900">You have KES {{ number_format($pendingFinesSum, 2) }} in outstanding fines.</p>
                <p class="mt-1 text-rose-700">Please clear these balances to avoid account limitations. You can submit payments using the <strong>Pay Fine via SMS</strong> button above.</p>
            </div>
        </div>
    @else
        <div class="flex items-start gap-4 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-sm text-emerald-800">
            <span class="material-symbols-outlined text-emerald-500 mt-0.5 flex-shrink-0">check_circle</span>
            <p class="font-semibold text-emerald-900">All clear! You have no pending fines or penalties on your account.</p>
        </div>
    @endif

    {{-- Fines Table (Blue color palette only) --}}
    <section class="bg-white rounded-xl border border-digital-blue-100 card-shadow overflow-hidden">
        <div class="p-6 border-b border-digital-blue-50 flex justify-between items-center bg-digital-blue-50/20">
            <h2 class="font-headline-md text-headline-md text-digital-blue-900">Your Penalties</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-digital-blue-50/50 text-digital-blue-800 font-label-sm uppercase tracking-wider border-b border-digital-blue-100">
                    <tr>
                        <th class="px-6 py-4">Fined Date</th>
                        <th class="px-6 py-4">Reason / Description</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-digital-blue-50">
                    @forelse($fines ?? [] as $fine)
                    <tr class="hover:bg-digital-blue-50/20 transition-colors">
                        <td class="px-6 py-4 text-slate-800 whitespace-nowrap">
                            {{ $fine->due_date ? \Carbon\Carbon::parse($fine->due_date)->format('M d, Y') : $fine->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-sm font-medium">
                            <span class="block text-digital-blue-800 font-semibold text-xs uppercase tracking-wider mb-0.5">
                                {{ str_replace('_', ' ', $fine->type) }}
                            </span>
                            {{ $fine->description ?? 'No details provided' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'unpaid'  => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'paid'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                ];
                                $colorClass = $statusColors[$fine->status] ?? 'bg-slate-100 text-slate-700';
                                $labelText = $fine->status === 'pending' || $fine->status === 'unpaid' ? 'Pending' : 'Paid';
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $colorClass }}">
                                {{ $labelText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-digital-blue-900 whitespace-nowrap">
                            KES {{ number_format($fine->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2 opacity-50 font-light text-digital-blue-300">gavel</span>
                            <span class="text-slate-500 font-medium">No fines or penalties recorded on your account.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
