<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Group Financial Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .summary { display: flex; justify-content: space-around; margin: 20px 0; }
        .summary-card { text-align: center; border: 1px solid #ddd; padding: 10px; width: 30%; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background: #f2f2f2; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Group Financial Report</h2>
        <p><strong>Chama:</strong> {{ Auth::user()->chama->name ?? 'N/A' }}</p>
        <p>Generated: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-card">
            <h4>Total Contributions</h4>
            <p>Ksh {{ number_format($contributions->sum('amount'), 2) }}</p>
        </div>
        <div class="summary-card">
            <h4>Total Loans</h4>
            <p>Ksh {{ number_format($loans->sum('amount'), 2) }}</p>
        </div>
        <div class="summary-card">
            <h4>Total Fines</h4>
            <p>Ksh {{ number_format($fines->sum('amount'), 2) }}</p>
        </div>
    </div>

    <h3>Member Summary</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Contributions</th>
                <th>Loans</th>
                <th>Fines</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>Ksh {{ number_format($contributions->where('user_id', $user->id)->sum('amount'), 2) }}</td>
                    <td>Ksh {{ number_format($loans->where('user_id', $user->id)->sum('amount'), 2) }}</td>
                    <td>Ksh {{ number_format($fines->where('user_id', $user->id)->sum('amount'), 2) }}</td>
                    <td>Ksh {{ number_format($contributions->where('user_id', $user->id)->sum('amount') - $loans->where('user_id', $user->id)->sum('amount'), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        This is a computer‑generated report and does not require a signature.
    </div>
</body>
</html>