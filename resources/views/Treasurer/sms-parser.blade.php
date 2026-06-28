@extends('layouts.app')
@section('title', 'SMS Parser & Unmapped Queue')

@section('content')
<div class="space-y-stack-lg max-w-container-max mx-auto">

    {{-- Header Card --}}
    <div class="premium-card rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 gold-gradient"></div>
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface">SMS Contribution Parser</h3>
            <p class="text-body-md text-secondary">Paste Safaricom M-Pesa SMS statements to automatically parse and resolve transaction records.</p>
        </div>
        <button onclick="openSmsModal()" class="open-sms-modal-trigger gold-gradient text-white px-6 py-2.5 rounded-lg font-bold flex items-center justify-center gap-1.5 shadow-md hover:opacity-90 active:scale-95 transition-all whitespace-nowrap">
            <span class="material-symbols-outlined">sms</span> Parse M-Pesa SMS
        </button>
    </div>

    {{-- Unmapped Queue Table --}}
    <div class="bg-white rounded-xl border border-secondary-container card-shadow overflow-hidden">
        <div class="p-gutter border-b border-secondary-container flex justify-between items-center bg-surface-container-lowest">
            <h3 class="font-headline-md text-headline-md text-on-surface">Incoming Unmapped Queue</h3>
            <span class="bg-rose-50 border border-rose-200 text-rose-700 px-3 py-1 rounded-full text-[12px] font-bold">
                {{ $transactions->where('status', 'unmapped')->count() }} Action Pending
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low border-b border-secondary-container">
                    <tr>
                        <th class="px-gutter py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Received</th>
                        <th class="px-gutter py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Sender</th>
                        <th class="px-gutter py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Ref Code</th>
                        <th class="px-gutter py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Amount</th>
                        <th class="px-gutter py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Purpose</th>
                        <th class="px-gutter py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Status</th>
                        <th class="px-gutter py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @forelse($transactions ?? [] as $tx)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="px-gutter py-4 font-label-md text-label-md whitespace-nowrap text-secondary">
                            {{ $tx->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-gutter py-4 font-label-md text-label-md font-bold text-on-surface">
                            {{ $tx->sender ?? '—' }}
                        </td>
                        <td class="px-gutter py-4 font-label-md text-label-md font-mono text-primary font-semibold">
                            {{ $tx->transaction_code ?? '—' }}
                        </td>
                        <td class="px-gutter py-4 font-label-md text-label-md font-bold text-on-surface">
                            KES {{ number_format($tx->amount, 2) }}
                        </td>
                        <td class="px-gutter py-4">
                            @if($tx->payment_type === 'loan_repayment')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border bg-emerald-50 border-emerald-200 text-emerald-700">
                                <span class="material-symbols-outlined text-xs" style="font-size:14px;">account_balance</span>
                                Loan Repayment
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border bg-amber-50 border-amber-200 text-amber-700">
                                <span class="material-symbols-outlined text-xs" style="font-size:14px;">savings</span>
                                Contribution
                            </span>
                            @endif
                        </td>
                        <td class="px-gutter py-4">
                            @php
                                $statusColors = [
                                    'mapped'   => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'unmapped' => 'bg-amber-100 text-amber-800 border-amber-200',
                                ];
                                $colorClass = $statusColors[$tx->status] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $colorClass }}">
                                {{ ucfirst($tx->status) }}
                            </span>
                        </td>
                        <td class="px-gutter py-4 text-right whitespace-nowrap space-x-2">
                            @if($tx->status === 'unmapped')
                            <button onclick="matchTransaction(
                                        {{ $tx->id }},
                                        '{{ $tx->transaction_code }}',
                                        {{ $tx->amount }},
                                        '{{ addslashes($tx->sender) }}',
                                        {{ $tx->user && $tx->user->role === 'member' ? $tx->user_id : 'null' }},
                                        '{{ $tx->payment_type }}',
                                        {{ $tx->loan_id ?? 'null' }})"
                                    class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-label-sm transition shadow-sm hover:shadow-md">
                                Match
                            </button>
                            <button onclick="rejectTransaction({{ $tx->id }})"
                                    class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded font-label-sm transition shadow-sm hover:shadow-md">
                                Reject
                            </button>
                            @else
                            <span class="text-slate-400 text-xs italic">
                                @if($tx->payment_type === 'loan_repayment')
                                    Repayment → {{ $tx->user->name ?? 'User' }}
                                @else
                                    Contribution → {{ $tx->user->name ?? 'User' }}
                                @endif
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl block mb-2 opacity-50 font-light">sms</span>
                            No incoming unmapped transactions in the queue.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Match Modal ─────────────────────────────────────────────────────────── --}}
<div id="matchModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-xl max-w-md w-full mx-4 border border-secondary-container shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 gold-gradient"></div>

        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold font-title text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person_add</span>
                Map M-Pesa Transaction
            </h3>
            <button onclick="closeMatchModal()" class="text-slate-500 hover:text-slate-800 text-2xl font-bold transition">&times;</button>
        </div>

        <div class="p-6 space-y-4">
            {{-- Transaction summary --}}
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-2 text-xs text-secondary">
                <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                    <span class="font-medium">Transaction Code</span>
                    <span class="font-mono text-primary font-bold" id="matchTxCode"></span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                    <span class="font-medium">Amount</span>
                    <span class="font-bold text-on-surface text-sm" id="matchTxAmount"></span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                    <span class="font-medium">M-Pesa Sender</span>
                    <span class="text-on-surface font-bold" id="matchTxSender"></span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="font-medium">Member's Stated Intent</span>
                    <span id="matchTxIntent" class="font-bold text-xs px-2 py-0.5 rounded-full border"></span>
                </div>
            </div>

            {{-- Member selector --}}
            <div>
                <label for="matchMemberId" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Assign to Member</label>
                <select id="matchMemberId"
                        onchange="onMemberChange(this.value)"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none text-on-surface text-sm transition-all">
                    <option value="">— Select Member —</option>
                    @foreach($members ?? [] as $member)
                        <option value="{{ $member->id }}">
                            {{ $member->name }} ({{ $member->email }})
                            {{ $member->active_loan ? ' · Has active loan' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Payment Type override --}}
            <div>
                <label for="matchPaymentType" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Apply Payment As</label>
                <select id="matchPaymentType"
                        onchange="onPaymentTypeChange()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none text-on-surface text-sm transition-all">
                    <option value="contribution">💰 Savings Contribution</option>
                    <option value="loan_repayment">🏦 Loan Repayment</option>
                </select>
            </div>

            {{-- Active loan info (shown when loan_repayment is selected and member has a loan) --}}
            <div id="activeLoanInfo" class="hidden items-center gap-2 p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-xs text-emerald-800 font-medium">
                <span class="material-symbols-outlined text-sm text-emerald-600">verified</span>
                <span id="activeLoanDetails"></span>
            </div>

            {{-- Warning when loan_repayment selected but member has no active loan --}}
            <div id="noLoanWarning" class="hidden p-3 bg-rose-50 border border-rose-100 rounded-xl text-xs text-rose-800 font-medium">
                ⚠️ This member has no active loan. Please select <strong>Savings Contribution</strong>.
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button onclick="submitMatch()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl transition flex-1 text-sm shadow-md flex items-center justify-center gap-1">
                    Confirm Mapping
                </button>
                <button onclick="closeMatchModal()"
                        class="bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-semibold py-3 px-6 rounded-xl transition text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@php
    $membersJson = $members->map(fn($m) => [
        'id'          => $m->id,
        'name'        => $m->name,
        'active_loan' => $m->active_loan ? [
            'id'                  => $m->active_loan->id,
            'outstanding_balance' => number_format($m->active_loan->outstanding_balance, 2),
        ] : null,
    ])->values()->toJson();
@endphp

@push('scripts')
<script>
    // Members data with active loan info — built server-side, zero extra AJAX calls
    const membersData = {!! $membersJson !!};

    let currentMatchTxId   = null;
    let currentLoanId      = null;

    // ── Open match modal ──────────────────────────────────────────────────────
    function matchTransaction(id, code, amount, sender, suggestedMemberId, paymentType, loanId) {
        currentMatchTxId = id;
        currentLoanId    = loanId || null;

        document.getElementById('matchTxCode').innerText   = code;
        document.getElementById('matchTxAmount').innerText = 'KES ' + Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
        document.getElementById('matchTxSender').innerText = sender;

        // Show stated intent badge
        const intentEl = document.getElementById('matchTxIntent');
        if (paymentType === 'loan_repayment') {
            intentEl.innerText   = '🏦 Loan Repayment';
            intentEl.className   = 'font-bold text-xs px-2 py-0.5 rounded-full border bg-emerald-50 border-emerald-200 text-emerald-700';
        } else {
            intentEl.innerText   = '💰 Contribution';
            intentEl.className   = 'font-bold text-xs px-2 py-0.5 rounded-full border bg-amber-50 border-amber-200 text-amber-700';
        }

        // Pre-fill payment type (from stored intent)
        document.getElementById('matchPaymentType').value = paymentType || 'contribution';

        // Pre-fill member if auto-detected
        document.getElementById('matchMemberId').value = suggestedMemberId || '';
        if (suggestedMemberId) {
            updateActiveLoanDisplay(suggestedMemberId);
        } else {
            hideActiveLoanInfo();
        }

        document.getElementById('matchModal').classList.remove('hidden');
        document.getElementById('matchModal').classList.add('flex');
    }

    function closeMatchModal() {
        document.getElementById('matchModal').classList.add('hidden');
        document.getElementById('matchModal').classList.remove('flex');
        currentMatchTxId = null;
        currentLoanId    = null;
    }

    // ── React to member selection change ─────────────────────────────────────
    function onMemberChange(memberId) {
        updateActiveLoanDisplay(memberId);
    }

    // ── React to payment type selection change ───────────────────────────────
    function onPaymentTypeChange() {
        const memberId = document.getElementById('matchMemberId').value;
        updateActiveLoanDisplay(memberId);
    }

    // ── Show/hide active loan info based on current selections ───────────────
    function updateActiveLoanDisplay(memberId) {
        const paymentType = document.getElementById('matchPaymentType').value;
        const member = membersData.find(m => m.id == memberId);

        hideActiveLoanInfo();

        if (paymentType !== 'loan_repayment') return;

        if (member && member.active_loan) {
            currentLoanId = member.active_loan.id;
            document.getElementById('activeLoanDetails').innerText =
                `Loan #${member.active_loan.id} — KES ${member.active_loan.outstanding_balance} remaining`;
            document.getElementById('activeLoanInfo').classList.remove('hidden');
            document.getElementById('activeLoanInfo').classList.add('flex');
        } else if (member && !member.active_loan) {
            currentLoanId = null;
            document.getElementById('noLoanWarning').classList.remove('hidden');
        }
    }

    function hideActiveLoanInfo() {
        currentLoanId = null;
        document.getElementById('activeLoanInfo').classList.add('hidden');
        document.getElementById('activeLoanInfo').classList.remove('flex');
        document.getElementById('noLoanWarning').classList.add('hidden');
    }

    // ── Submit the match ──────────────────────────────────────────────────────
    function submitMatch() {
        const memberId    = document.getElementById('matchMemberId').value;
        const paymentType = document.getElementById('matchPaymentType').value;

        if (!memberId) {
            alert('Please select a member first.');
            return;
        }

        if (paymentType === 'loan_repayment' && !currentLoanId) {
            alert('The selected member has no active loan. Please apply this as a Savings Contribution instead.');
            return;
        }

        fetch(`/treasurer/sms-parser/${currentMatchTxId}/match`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                user_id:      memberId,
                payment_type: paymentType,
                loan_id:      currentLoanId,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Transaction mapped successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(() => alert('Network error. Please try again.'));
    }

    // ── Reject ────────────────────────────────────────────────────────────────
    function rejectTransaction(id) {
        if (confirm('Are you sure you want to reject this transaction? This cannot be undone.')) {
            fetch(`/treasurer/sms-parser/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Transaction rejected.');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(() => alert('Network error.'));
        }
    }
</script>
@endpush
@endsection