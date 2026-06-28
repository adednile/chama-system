@extends('layouts.app')
@section('title', 'Financial Portal')

@section('content')
<div class="space-y-stack-lg max-w-container-max mx-auto">

    {{-- Header --}}
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Financial Portal</h1>
            <p class="font-body-md text-body-md text-secondary">View your contribution history and submit M-Pesa payments for verification.</p>
        </div>
        {{-- Primary action: open the SMS parser modal --}}
        <button onclick="openSmsModal()"
                class="open-sms-modal-trigger flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl shadow-sm transition-all">
            <span class="material-symbols-outlined">sms</span>
            Submit M-Pesa Payment
        </button>
    </header>

    {{-- How it works info banner --}}
    <div class="flex items-start gap-4 p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-800">
        <span class="material-symbols-outlined text-blue-500 mt-0.5 flex-shrink-0">info</span>
        <p>
            To record a contribution or loan repayment, click <strong>Submit M-Pesa Payment</strong> above and paste your Safaricom SMS notification.
            The Treasurer will verify and post it to your ledger within 24 hours.
        </p>
    </div>

    {{-- Contribution Ledger History Table --}}
    <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h2 class="font-headline-md text-headline-md text-on-background">Contribution Ledger History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-100 text-on-surface-variant font-label-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Posting Date</th>
                        <th class="px-6 py-4">Transaction Code</th>
                        <th class="px-6 py-4">Source</th>
                        <th class="px-6 py-4">Notes</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($contributions ?? [] as $contribution)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-on-surface whitespace-nowrap">
                            {{ $contribution->contribution_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-primary font-bold">
                            {{ $contribution->reference ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $sourceColors = [
                                    'mpesa'  => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'manual' => 'bg-slate-100 text-slate-700 border-slate-200',
                                ];
                                $colorClass = $sourceColors[$contribution->source] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $colorClass }}">
                                {{ ucfirst($contribution->source) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-on-surface-variant text-sm font-medium">
                            {{ $contribution->notes ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-on-surface whitespace-nowrap">
                            KES {{ number_format($contribution->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl block mb-2 opacity-50 font-light">receipt</span>
                            No contributions posted yet. Submit your M-Pesa SMS using the button above.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection