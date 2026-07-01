<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chama Attendance History Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #1e293b; font-size: 11px; line-height: 1.4; margin: 0; padding: 0; }
        .header { border-bottom: 3px solid #0052cc; padding-bottom: 12px; margin-bottom: 20px; }
        .header h2 { color: #0052cc; margin: 0 0 6px 0; font-size: 20px; font-weight: bold; }
        .header p { margin: 2px 0; color: #475569; font-size: 11px; }
        .info-card { background: #e5f0ff; border: 1px solid #cce0ff; padding: 12px; border-radius: 6px; text-align: center; }
        .info-card h4 { margin: 0 0 6px 0; color: #003d99; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-card p { margin: 0; font-size: 14px; font-weight: bold; color: #0052cc; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        .table th { background: #0052cc; color: #ffffff; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .table td { color: #334155; }
        .table tr:nth-child(even) { background: #f8fafc; }
        .checkbox-present {
            background-color: #d1fae5;
            border: 1px solid #10b981;
            color: #059669;
            padding: 2px 5px;
            font-weight: bold;
            font-size: 10px;
            border-radius: 3px;
        }
        .checkbox-absent {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            color: #dc2626;
            padding: 2px 5px;
            font-weight: bold;
            font-size: 10px;
            border-radius: 3px;
        }
        .footer { text-align: center; margin-top: 40px; font-size: 9px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Chama Attendance History Report</h2>
        <p><strong>Chama Group:</strong> {{ $chama->name }}</p>
        <p><strong>Financial Year:</strong> {{ $year }}</p>
        <p><strong>Report Run Date:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Summary Metrics -->
    <table style="width: 100%; border-spacing: 12px; margin: 20px 0; border-collapse: separate;">
        <tr>
            <td style="width: 50%; padding: 0;">
                <div class="info-card">
                    <h4>Total Meetings Tracked</h4>
                    <p>{{ $meetings->count() }} Meetings</p>
                </div>
            </td>
            <td style="width: 50%; padding: 0;">
                <div class="info-card">
                    <h4>Group Average Attendance</h4>
                    <p>{{ $averageAttendance }}%</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 25%;">Member Name</th>
                @foreach($meetings as $meeting)
                    <th style="text-align: center; font-size: 8px;">
                        {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d/m') }}
                        <span style="display: block; font-size: 7px; opacity: 0.8; font-weight: normal;">
                            {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('D') }}
                        </span>
                    </th>
                @endforeach
                <th style="text-align: right; width: 15%;">Reliability</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
                <tr>
                    <td style="font-weight: bold; color: #1e293b;">{{ $member->name }}</td>
                    @php
                        $attended = 0;
                    @endphp
                    @foreach($meetings as $meeting)
                        @php
                            $attRecord = $attendances->where('user_id', $member->id)->where('meeting_id', $meeting->id)->first();
                            $isPresent = $attRecord ? (bool)$attRecord->present : false;
                            if ($isPresent) {
                                $attended++;
                            }
                        @endphp
                        <td style="text-align: center;">
                            @if($isPresent)
                                <span class="checkbox-present">&#10003;</span>
                            @else
                                <span class="checkbox-absent">&#10007;</span>
                            @endif
                        </td>
                    @endforeach
                    @php
                        $reliability = $meetings->count() > 0 ? round(($attended / $meetings->count()) * 100, 1) : 100;
                    @endphp
                    <td style="text-align: right; font-weight: bold; color: #0052cc;">
                        {{ $reliability }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by Chama Gold System &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
