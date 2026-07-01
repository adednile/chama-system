<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chama Financial Report</title>
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
        <h2>Chama Financial Report</h2>
        <p><strong>Chama Group:</strong> {{ Auth::user()->chama->name ?? 'N/A' }}</p>
        <p><strong>Report Run Date:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Summary Metrics (Dompdf compatible columns) -->
    <table style="width: 100%; border-spacing: 12px; margin: 20px 0; border-collapse: separate;">
        <tr>
            <td style="width: 33.33%; padding: 0;">
                <div class="info-card">
                    <h4>Total Contributions</h4>
                    <p>Ksh {{ number_format($contributions->sum('amount'), 2) }}</p>
                </div>
            </td>
            <td style="width: 33.33%; padding: 0;">
                <div class="info-card">
                    <h4>Total Loans</h4>
                    <p>Ksh {{ number_format($loans->sum('amount'), 2) }}</p>
                </div>
            </td>
            <td style="width: 33.33%; padding: 0;">
                <div class="info-card">
                    <h4>Total Fines</h4>
                    <p>Ksh {{ number_format($fines->sum('amount'), 2) }}</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Month-on-Month Growth Trend Chart -->
    <div class="chart-container">
        <img src="{{ $groupChartUrl }}" style="width: 100%; max-width: 580px; height: auto;" alt="Chama Performance Growth Chart" />
    </div>

    <h3>Chama Member Summary</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Contributions</th>
                <th>Loans Issued</th>
                <th>Fines Logged</th>
                <th>Net Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>Ksh {{ number_format($contributions->where('user_id', $user->id)->sum('amount'), 2) }}</td>
                    <td>Ksh {{ number_format($loans->where('user_id', $user->id)->sum('amount'), 2) }}</td>
                    <td>Ksh {{ number_format($fines->where('user_id', $user->id)->sum('amount'), 2) }}</td>
                    <td><strong>Ksh {{ number_format($contributions->where('user_id', $user->id)->sum('amount') - $loans->where('user_id', $user->id)->sum('amount'), 2) }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center; color: #64748b;">No members registered in this Chama.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Designed by Chama Gold &bull; This is an official computer-generated financial summary.
    </div>
</body>
</html>