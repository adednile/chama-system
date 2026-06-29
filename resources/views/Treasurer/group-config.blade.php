@extends('layouts.app')
@section('title', 'Group Configuration')

@section('content')
<div class="space-y-6">

    {{-- Header Card --}}
    <div class="premium-card rounded-2xl p-6 flex items-center justify-between flex-wrap gap-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-500 to-gold-600"></div>
        <div>
            <h3 class="text-base font-bold font-title text-slate-800">Chama Group Settings</h3>
            <p class="text-xs text-slate-400 mt-1 font-medium">Configure financial milestones, late fees, loan parameters, and credit engine weights.</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-sm">
            <span class="material-symbols-outlined text-sm font-bold">settings</span>
            Group: {{ $chama->name }}
        </div>
    </div>

    {{-- Settings Form --}}
    <form action="{{ route('treasurer.chama.config.update') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @csrf

        {{-- Left Card: Financial Rules --}}
        <div class="premium-card rounded-2xl p-6 space-y-6">
            <h4 class="text-sm font-bold font-title text-gold-600 border-b border-slate-100 pb-2 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm font-bold">payments</span> Financial Milestones & Rules
            </h4>
            
            <div class="space-y-4">
                <div>
                    <label for="contribution_target" class="block text-xs font-bold text-slate-500 mb-1.5">Monthly Contribution Target (KES)</label>
                    <input type="number" step="0.01" name="contribution_target" id="contribution_target" value="{{ old('contribution_target', $chama->contribution_target) }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-gold-500 transition">
                </div>

                <div>
                    <label for="collection_cutoff" class="block text-xs font-bold text-slate-500 mb-1.5">Collection Cutoff Date</label>
                    <input type="date" name="collection_cutoff" id="collection_cutoff" value="{{ old('collection_cutoff', $chama->collection_cutoff ? \Carbon\Carbon::parse($chama->collection_cutoff)->format('Y-m-d') : '') }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:border-gold-500 transition">
                </div>

                <div>
                    <label for="late_penalty_flat" class="block text-xs font-bold text-slate-500 mb-1.5">Late Penalty Flat Fee per Day (KES)</label>
                    <input type="number" step="0.01" name="late_penalty_flat" id="late_penalty_flat" value="{{ old('late_penalty_flat', $chama->late_penalty_flat) }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-gold-500 transition">
                </div>

                <div>
                    <label for="interest_rate_pct" class="block text-xs font-bold text-slate-500 mb-1.5">Default Annual Loan Interest Rate (%)</label>
                    <input type="number" step="0.01" name="interest_rate_pct" id="interest_rate_pct" value="{{ old('interest_rate_pct', $chama->interest_rate_pct) }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-gold-500 transition">
                </div>
            </div>
        </div>

        {{-- Right Card: Credit scoring & Loan thresholds --}}
        <div class="premium-card rounded-2xl p-6 space-y-6">
            <h4 class="text-sm font-bold font-title text-gold-600 border-b border-slate-100 pb-2 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm font-bold">credit_score</span> Credit Engine & Scoring Weights
            </h4>

            <div class="space-y-4">
                <div>
                    <label for="min_credit_score" class="block text-xs font-bold text-slate-500 mb-1.5">Minimum Credit Score Threshold (1.0 to 10.0)</label>
                    <input type="number" step="0.1" name="min_credit_score" id="min_credit_score" value="{{ old('min_credit_score', $chama->min_credit_score) }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-gold-500 transition">
                </div>

                <div class="pt-2">
                    <p class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wider">Scoring Model Weights (Must sum to 1.0)</p>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="savings_weight" class="block text-[10px] font-bold text-slate-400 mb-1 leading-tight">Savings Consistency</label>
                            <input type="number" step="0.01" min="0" max="1" name="savings_weight" id="savings_weight" value="{{ old('savings_weight', $chama->savings_weight) }}" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-gold-500 transition">
                        </div>

                        <div>
                            <label for="attendance_weight" class="block text-[10px] font-bold text-slate-400 mb-1 leading-tight">Attendance</label>
                            <input type="number" step="0.01" min="0" max="1" name="attendance_weight" id="attendance_weight" value="{{ old('attendance_weight', $chama->attendance_weight) }}" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-gold-500 transition">
                        </div>

                        <div>
                            <label for="repayment_weight" class="block text-[10px] font-bold text-slate-400 mb-1 leading-tight">Repayments</label>
                            <input type="number" step="0.01" min="0" max="1" name="repayment_weight" id="repayment_weight" value="{{ old('repayment_weight', $chama->repayment_weight) }}" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-gold-500 transition">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="gold-gradient-btn px-6 py-2.5 rounded-xl text-xs font-bold shadow-md hover:opacity-95 transition">
                    Save Group Configuration
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
