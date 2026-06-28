<div id="smsModal"
     class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden z-50"
     style="align-items:center; justify-content:center;"
     x-data="smsParserModal()"
     @open-sms-modal.window="openModal()"
     x-cloak>

    {{-- Backdrop: only closes when modal is confirmed open, preventing the opening click from closing it --}}
    <div class="absolute inset-0" @click="if(isOpen) closeModal()"></div>

    <div class="premium-card rounded-2xl max-w-xl w-full mx-4 border border-slate-200 shadow-2xl relative overflow-hidden z-10 max-h-[90vh] overflow-y-auto"
         @click.stop>
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-500 to-gold-600"></div>

        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold font-title text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-gold-500">sms</span> Parse M-Pesa SMS
            </h3>
            <button @click="closeModal()" class="text-slate-500 hover:text-slate-800 text-2xl font-bold transition">&times;</button>
        </div>

        <div class="p-6 space-y-4">

            {{-- ── STEP 1: Input + payment type ─────────────────────────────── --}}
            <div x-show="!parsed">

                {{-- Payment Type Selector (members only — treasurers set type at match step) --}}
                @if(auth()->check() && auth()->user()->role === 'member')
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">What is this payment for?</p>
                    <div class="grid grid-cols-2 gap-2">
                        {{-- Contribution button --}}
                        <button type="button"
                                @click="paymentType = 'contribution'; loanId = null"
                                :class="paymentType === 'contribution'
                                    ? 'border-amber-400 bg-amber-50 text-amber-800 ring-1 ring-amber-300'
                                    : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50'"
                                class="py-3 px-3 rounded-xl border text-xs font-bold transition-all flex flex-col items-center gap-1">
                            <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">savings</span>
                            Savings Contribution
                        </button>

                        {{-- Loan Repayment button --}}
                        <button type="button"
                                @click="paymentType = 'loan_repayment'; loanId = {{ isset($activeLoan) && $activeLoan ? $activeLoan->id : 'null' }}"
                                :class="paymentType === 'loan_repayment'
                                    ? 'border-emerald-400 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300'
                                    : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50'"
                                class="py-3 px-3 rounded-xl border text-xs font-bold transition-all flex flex-col items-center gap-1
                                       {{ !(isset($activeLoan) && $activeLoan) ? 'opacity-40 cursor-not-allowed' : '' }}"
                                {{ !(isset($activeLoan) && $activeLoan) ? 'disabled title="No active loan to repay"' : '' }}>
                            <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">account_balance</span>
                            Loan Repayment
                        </button>
                    </div>

                    {{-- Active loan info (shown when loan_repayment is selected and loan exists) --}}
                    @if(isset($activeLoan) && $activeLoan)
                    <div x-show="paymentType === 'loan_repayment'"
                         class="mt-2 flex items-center gap-2 p-2.5 bg-emerald-50 border border-emerald-100 rounded-lg text-xs text-emerald-800 font-medium">
                        <span class="material-symbols-outlined text-sm text-emerald-600">verified</span>
                        Loan #{{ $activeLoan->id }} &mdash; <span class="font-bold">KES {{ number_format($activeLoan->outstanding_balance, 2) }}</span>&nbsp;outstanding
                    </div>
                    @else
                    <div x-show="paymentType === 'loan_repayment'"
                         class="mt-2 p-2.5 bg-amber-50 border border-amber-100 rounded-lg text-xs text-amber-800">
                        You have no active loan. Please select <strong>Savings Contribution</strong>.
                    </div>
                    @endif
                </div>
                @endif

                {{-- SMS Textarea --}}
                <div>
                    <p class="text-xs text-slate-500 mb-2 font-medium">Paste the M-Pesa SMS notification below:</p>
                    <textarea x-model="smsText"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 h-32 focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 outline-none text-slate-800 text-sm transition-all resize-none"
                        placeholder="e.g. QKX3B5T2R1 Confirmed. Ksh 5,000.00 received from ALICE WANJIKU 0712345678 on 2026-06-21..."></textarea>
                </div>

                <button @click="parseSms()"
                        class="w-full py-3 bg-gradient-to-r from-gold-500 to-gold-600 hover:opacity-95 text-white font-bold rounded-xl text-sm transition shadow-md flex items-center justify-center gap-2"
                        :disabled="loading">
                    <span x-show="!loading" class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm font-bold">search</span>
                        Extract Transaction Details
                    </span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Parsing...
                    </span>
                </button>
            </div>

            {{-- ── STEP 2: Confirm parsed data ───────────────────────────────── --}}
            <div x-show="parsed" x-cloak class="space-y-4">
                <h4 class="font-bold text-sm text-slate-800 font-title flex items-center gap-1">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    Transaction Extracted
                </h4>

                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-2.5 text-xs text-slate-600">
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Amount</span>
                        <span class="font-bold text-slate-800 text-sm" x-text="'KES ' + parsedData.amount"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Sender</span>
                        <span class="text-slate-700 font-medium" x-text="parsedData.sender || '—'"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Reference Code</span>
                        <span class="font-mono text-gold-600 font-bold" x-text="parsedData.transaction_code || '—'"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Date</span>
                        <span class="text-slate-600" x-text="parsedData.date || '—'"></span>
                    </div>
                    {{-- Payment Type (members only) --}}
                    @if(auth()->check() && auth()->user()->role === 'member')
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-500 font-medium">Recorded As</span>
                        <span class="font-bold text-xs px-2 py-0.5 rounded-full border"
                              :class="paymentType === 'loan_repayment'
                                  ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                                  : 'bg-amber-50 border-amber-200 text-amber-700'"
                              x-text="paymentType === 'loan_repayment' ? 'Loan Repayment' : 'Savings Contribution'">
                        </span>
                    </div>
                    @endif
                </div>

                <div class="flex gap-3">
                    <button @click="confirmRecord()"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl transition flex-1 text-sm shadow-md flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm font-bold">check</span>
                        Confirm &amp; Submit
                    </button>
                    <button @click="parsed = false; parsedData = {}"
                            class="bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-semibold py-3 px-6 rounded-xl transition text-sm">
                        Back
                    </button>
                </div>
                <p class="text-[10px] text-slate-500 text-center font-medium">
                    @if(auth()->check() && auth()->user()->role === 'treasurer')
                        This will be queued for member mapping.
                    @else
                        Pending Treasurer review. Will appear in your pending list.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function smsParserModal() {
        // Active loan context set by server; used to smart-default the payment type.
        const hasActiveLoan = {{ (isset($activeLoan) && $activeLoan) ? 'true' : 'false' }};
        const activeLoanId  = {{ (isset($activeLoan) && $activeLoan) ? $activeLoan->id : 'null' }};

        return {
            isOpen:      false,
            smsText:     '',
            loading:     false,
            parsed:      false,
            parsedData:  {},
            paymentType: hasActiveLoan ? 'loan_repayment' : 'contribution',
            loanId:      hasActiveLoan ? activeLoanId : null,

            openModal() {
                // Reset to smart default each time the modal opens
                this.paymentType = hasActiveLoan ? 'loan_repayment' : 'contribution';
                this.loanId      = hasActiveLoan ? activeLoanId : null;
                this.smsText     = '';
                this.loading     = false;
                this.parsed      = false;
                this.parsedData  = {};
                this.isOpen      = true;

                const modal = document.getElementById('smsModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.style.display = 'flex';
                }
            },

            closeModal() {
                this.isOpen     = false;
                this.smsText    = '';
                this.parsed     = false;
                this.parsedData = {};

                const modal = document.getElementById('smsModal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }
            },

            parseSms() {
                if (!this.smsText.trim()) {
                    alert('Please paste an M-Pesa SMS message first.');
                    return;
                }
                this.loading = true;

                const parseRoute = '{{ auth()->user()->role === "treasurer" ? route("treasurer.sms-parser.store") : route("member.contributions.parseSms") }}';

                fetch(parseRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        message:      this.smsText,
                        payment_type: this.paymentType,
                        loan_id:      this.loanId,
                    }),
                })
                .then(r => r.json().then(data => ({ ok: r.ok, data })))
                .then(({ ok, data }) => {
                    this.loading = false;
                    if (ok && data.success) {
                        this.parsedData = data.data;
                        this.parsed     = true;
                    } else {
                        alert(data.message || 'Could not parse SMS. Please check the message format.');
                    }
                })
                .catch(() => {
                    this.loading = false;
                    alert('Network error. Please try again.');
                });
            },

            confirmRecord() {
                const typeLabel = this.paymentType === 'loan_repayment' ? 'loan repayment' : 'contribution';
                @if(auth()->check() && auth()->user()->role === 'treasurer')
                    alert('M-Pesa SMS queued for member matching in the SMS Parser.');
                @else
                    alert(`Your M-Pesa ${typeLabel} has been submitted for Treasurer review.`);
                @endif
                this.closeModal();
                window.location.reload();
            },
        };
    }
</script>
@endpush