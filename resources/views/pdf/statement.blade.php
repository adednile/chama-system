<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member Statement</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .info { margin: 20px 0; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background: #f2f2f2; }
        .total { font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Member Financial Statement</h2>
        <p><strong>{{ $user->name }}</strong> – {{ $user->email }}</p>
        <p>Chama: {{ $user->chama->name ?? 'N/A' }}</p>
        <p>Generated: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info">
        <p><strong>Savings Balance:</strong> Ksh {{ number_format($contributions->sum('amount'), 2) }}</p>
        <p><strong>Outstanding Loans:</strong> Ksh {{ number_format($loans->sum('amount'), 2) }}</p>
        <p><strong>Unpaid Fines:</strong> Ksh {{ number_format($fines->where('status', 'unpaid')->sum('amount'), 2) }}</p>
    </div>

    <h3>Transaction History</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->created_at->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($tx->type) }}</td>
                    <td>{{ $tx->description ?? '—' }}</td>
                    <td>{{ $tx->type === 'credit' ? '+' : '-' }} Ksh {{ number_format($tx->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No transactions found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        This is a computer‑generated statement and does not require a signature.
    </div>
</body>
</html>