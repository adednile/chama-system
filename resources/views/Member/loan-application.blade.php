@extends('layouts.app')
@section('title', 'Loan Facility')

@section('content')
<div class="space-y-stack-lg max-w-container-max mx-auto">
    <!-- Header -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-headline-xl text-headline-xl text-on-background">Loan Facility</h1>
            <p class="text-on-surface-variant">Apply for a low-interest credit facility based on your Chama contributions.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-primary-fixed text-on-primary-fixed px-4 py-2 rounded-full font-label-md flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">verified_user</span>
                Credit Limit: Ksh {{ number_format($loanLimit ?? 0, 2) }}
            </span>
        </div>
    </header>

    <!-- Two Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Loan Application Form (Left Column) -->
        <section class="lg:col-span-7 bg-white p-6 md:p-8 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 mb-6">
                <span class="w-2 h-8 gold-gradient rounded-full"></span>
                <h2 class="font-headline-md text-headline-md text-on-background">Apply for a Loan</h2>
            </div>
            
            <form action="{{ route('member.loans.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="font-label-md text-on-surface-variant" for="principal">Principal Amount (Ksh)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-on-surface-variant">Ksh</span>
                        <input class="w-full pl-14 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all font-headline-md outline-none" 
                               id="principal" name="amount" placeholder="0.00" step="1000" type="number" value="50000" required/>
                    </div>
                    <p class="text-[12px] text-on-surface-variant">Minimum: Ksh 5,000 | Maximum: Ksh {{ number_format($loanLimit ?? 0, 2) }}</p>
                    @error('amount')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="font-label-md text-on-surface-variant" for="period">Repayment Period</label>
                        <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none" 
                                id="period" name="term_months" required>
                            <option value="1">1 Month</option>
                            <option value="3">3 Months</option>
                            <option selected value="6">6 Months</option>
                            <option value="12">12 Months</option>
                            <option value="18">18 Months</option>
                            <option value="24">24 Months</option>
                            <option value="36">36 Months</option>
                        </select>
                        @error('term_months')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-on-surface-variant">Interest Rate (Fixed)</label>
                        <div class="px-4 py-3 bg-slate-100 border border-slate-200 rounded-lg text-on-surface font-semibold flex items-center justify-between">
                            <span>{{ number_format($interestRate ?? 5.00, 1) }}% Per Annum</span>
                            <span class="material-symbols-outlined text-primary">info</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="font-label-md text-on-surface-variant" for="notes">Purpose/Notes</label>
                    <textarea class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none resize-none" 
                              id="notes" name="reason" placeholder="Explain the purpose of the loan..." rows="3" required></textarea>
                    @error('reason')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-start gap-3 p-4 bg-surface-container-low rounded-lg border border-slate-100">
                    <input class="mt-1 w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary" id="terms" type="checkbox" required/>
                    <label class="text-label-md text-on-surface-variant leading-tight" for="terms">
                        I confirm that I have read and agree to the <a class="text-primary font-semibold underline decoration-primary/30 hover:decoration-primary" href="#">Chama Bylaws Agreement</a> regarding loan disbursements and interest defaults.
                    </label>
                </div>

                @if($canApplyForLoan ?? true)
                <button class="w-full gold-gradient text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-primary-fixed/20 transition-all flex items-center justify-center gap-3 text-lg" type="submit">
                    Apply for Disbursement
                    <span class="material-symbols-outlined">trending_flat</span>
                </button>
                @else
                <button class="w-full bg-slate-300 text-slate-500 font-bold py-4 rounded-xl shadow-none cursor-not-allowed flex items-center justify-center gap-3 text-lg" type="button" disabled>
                    <span class="material-symbols-outlined">lock</span>
                    Application Locked (Clear Penalties)
                </button>
                @endif
            </form>
        </section>

        <!-- Live EMI Preview Panel (Right Column) -->
        <aside class="lg:col-span-5 space-y-6">
            <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm sticky top-24">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="font-headline-md text-headline-md text-on-background">Repayment Preview</h2>
                    <span class="material-symbols-outlined text-primary-fixed-dim scale-150">calculate</span>
                </div>
                <div class="space-y-8">
                    <div class="text-center p-6 rounded-2xl bg-primary-fixed/10 border border-primary-fixed/20">
                        <p class="text-label-md text-on-surface-variant font-medium uppercase tracking-widest mb-1">Estimated Monthly Payment</p>
                        <h3 class="text-[40px] font-extrabold text-primary leading-none" id="preview-emi">Ksh 0.00</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-100">
                            <p class="text-label-sm text-rose-800 font-semibold mb-1">Total Interest</p>
                            <p class="text-headline-md font-bold text-rose-600" id="preview-interest">Ksh 0.00</p>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                            <p class="text-label-sm text-emerald-800 font-semibold mb-1">Total Repayment</p>
                            <p class="text-headline-md font-bold text-emerald-600" id="preview-total">Ksh 0.00</p>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 pt-6">
                        <h4 class="text-label-md font-bold text-on-background mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            Disbursement Timeline
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-on-surface-variant">Application Review</span>
                                <span class="text-on-surface font-medium">Within 24 Hours</span>
                            </li>
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-on-surface-variant">Member Consensus</span>
                                <span class="text-on-surface font-medium">Not Required (Automated)</span>
                            </li>
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-on-surface-variant">M-Pesa Payout</span>
                                <span class="text-on-surface font-medium">Instant after approval</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Subtle Help Card -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-sm text-primary">
                    <span class="material-symbols-outlined">support_agent</span>
                </div>
                <div>
                    <p class="font-label-md text-on-surface font-bold">Need a higher limit?</p>
                    <p class="text-sm text-on-surface-variant">Increase your monthly contributions to unlock larger loan facilities.</p>
                </div>
            </div>
        </aside>
    </div>

    <!-- Application History Table -->
    <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-headline-md text-headline-md text-on-background">Application History</h2>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-sm">search</span>
                    <input class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-1 focus:ring-primary outline-none" id="searchHistory" placeholder="Search loans..." type="text"/>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="loansHistoryTable">
                <thead class="bg-slate-50 text-on-surface-variant font-label-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Application Date</th>
                        <th class="px-6 py-4">Principal</th>
                        <th class="px-6 py-4">Term</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($loans ?? [] as $loan)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-on-surface">{{ $loan->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-on-surface-variant font-mono">REF: LOAN-{{ $loan->id }}</div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-on-surface">Ksh {{ number_format($loan->amount, 2) }}</td>
                        <td class="px-6 py-4 text-on-surface-variant">{{ $loan->term_months }} Months</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'active'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'pending'   => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'rejected'  => 'bg-rose-100 text-rose-800 border-rose-200',
                                ];
                                $colorClass = $statusColors[$loan->status] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-[12px] font-bold border {{ $colorClass }}">
                                    {{ ucfirst($loan->status) }}
                                </span>
                                @if($loan->status === 'rejected')
                                <span class="material-symbols-outlined text-rose-400 text-base cursor-help"
                                      title="{{ $loan->rejection_reason ?? 'Rejected by treasurer' }}">error_outline</span>
                                @endif
                                @if($loan->status === 'active')
                                <span class="text-[10px] text-slate-400 italic">Repay via SMS Parser</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl block mb-2 opacity-50 font-light">history_edu</span>
                            No loan history available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@push('scripts')
<script>
    // Reducing balance calculation formula matching the backend
    const principalInput = document.getElementById('principal');
    const periodSelect = document.getElementById('period');
    
    const emiDisplay = document.getElementById('preview-emi');
    const interestDisplay = document.getElementById('preview-interest');
    const totalDisplay = document.getElementById('preview-total');

    const ANNUAL_RATE = {{ ($interestRate ?? 5.00) / 100 }}; // chama configured interest rate

    function calculateLoan() {
        const P = parseFloat(principalInput.value) || 0;
        const N = parseInt(periodSelect.value) || 1;
        
        if (P <= 0) {
            emiDisplay.innerText = 'Ksh 0.00';
            interestDisplay.innerText = 'Ksh 0.00';
            totalDisplay.innerText = 'Ksh 0.00';
            return;
        }

        const monthlyRate = ANNUAL_RATE / 12;
        
        let emi = 0;
        let totalRepayment = 0;
        let totalInterest = 0;

        if (monthlyRate === 0) {
            emi = P / N;
            totalRepayment = P;
            totalInterest = 0;
        } else {
            // Reducing balance EMI formula: P * r * (1+r)^N / ((1+r)^N - 1)
            emi = P * monthlyRate * Math.pow(1 + monthlyRate, N) / (Math.pow(1 + monthlyRate, N) - 1);
            totalRepayment = emi * N;
            totalInterest = totalRepayment - P;
        }

        // Update UI with Kenyan formatting
        const formatter = new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES',
            minimumFractionDigits: 2
        });

        emiDisplay.innerText = formatter.format(emi).replace('KES', 'Ksh');
        interestDisplay.innerText = formatter.format(totalInterest).replace('KES', 'Ksh');
        totalDisplay.innerText = formatter.format(totalRepayment).replace('KES', 'Ksh');
    }

    principalInput.addEventListener('input', calculateLoan);
    periodSelect.addEventListener('change', calculateLoan);

    // Initial Calculation
    calculateLoan();

    // Table Search filter
    document.getElementById('searchHistory').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#loansHistoryTable tbody tr');
        rows.forEach(row => {
            if(row.innerText.toLowerCase().indexOf(value) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>
@endpush
@endsection