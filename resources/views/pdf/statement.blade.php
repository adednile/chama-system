<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member Statement</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #1e293b; font-size: 12px; line-height: 1.5; margin: 0; padding: 0; }
        .header { border-bottom: 3px solid #0052cc; padding-bottom: 12px; margin-bottom: 20px; }
        .header h2 { color: #0052cc; margin: 0 0 6px 0; font-size: 22px; font-weight: bold; }
        .header p { margin: 2px 0; color: #475569; font-size: 12px; }
        .info-card { background: #e5f0ff; border: 1px solid #cce0ff; padding: 12px; border-radius: 6px; text-align: center; }
        .info-card h4 { margin: 0 0 6px 0; color: #003d99; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-card p { margin: 0; font-size: 15px; font-weight: bold; color: #0052cc; }
        .table { width: 100%; border-collapse: collapse; margin: 25px 0; }
        .table th, .table td { padding: 9px 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .table th { background: #0052cc; color: #ffffff; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        .table td { color: #334155; }
        .table tr:nth-child(even) { background: #f8fafc; }
        .chart-container { text-align: center; margin: 25px 0; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; }
        .footer { text-align: center; margin-top: 40px; font-size: 10px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Member Financial Statement</h2>
        <p><strong>Member Name:</strong> {{ $user->name }} ({{ $user->email }})</p>
        <p><strong>Chama Group:</strong> {{ $user->chama->name ?? 'N/A' }}</p>
        <p><strong>Statement Run Date:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Summary Metrics (Dompdf compatible columns) -->
    <table style="width: 100%; border-spacing: 12px; margin: 20px 0; border-collapse: separate;">
        <tr>
            <td style="width: 33.33%; padding: 0;">
                <div class="info-card">
                    <h4>Savings Balance</h4>
                    <p>Ksh {{ number_format($contributions->sum('amount'), 2) }}</p>
                </div>
            </td>
            <td style="width: 33.33%; padding: 0;">
                <div class="info-card">
                    <h4>Outstanding Loans</h4>
                    <p>Ksh {{ number_format($loans->sum('amount'), 2) }}</p>
                </div>
            </td>
            <td style="width: 33.33%; padding: 0;">
                <div class="info-card">
                    <h4>Unpaid Fines</h4>
                    <p>Ksh {{ number_format($fines->where('status', 'unpaid')->sum('amount'), 2) }}</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Cumulative Savings Velocity Chart -->
    <div class="chart-container">
        <img src="{{ $savingsChartUrl }}" style="width: 100%; max-width: 580px; height: auto;" alt="Historical Growth and Performance Tracking" />
    </div>

    <h3>Transaction Ledger</h3>
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
                <tr><td colspan="4" style="text-align: center; color: #64748b;">No transactions recorded.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Designed by Chama Gold &bull; This is an official computer-generated statement.
    </div>
</body>
</html>