<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Maintenance Report</title>
    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }

        h1,
        h2 {
            text-align: center;
            font-weight: bold;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 5px;
        }

        .logo {
            height: 80px;
        }

        hr {
            border: none;
            border-top: 1px solid #666;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }

        thead {
            background-color: #e2e8f0;
            font-weight: bold;
        }

        .total {
            margin-top: 20px;
            font-weight: bold;
        }

        .bold {
            font-weight: bold;
        }

        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>

    @if($base64logo)
        <div class="logo-container">
            <img src="{{ $base64logo }}" class="logo" alt="Logo">
        </div>
    @endif

    <h1>Food Safety & Halal Food Authority</h1>
    <hr>

    <h2>Maintenance Report - {{ $vehicle->RegID }} ({{ $vehicle->Model }})</h2>
    <p><strong>Branch:</strong> {{ $vehicle->branch->name ?? 'N/A' }}</p>

    <table cellpadding="6">
        <thead bgcolor="gray">
            <tr>
                <th class="bold">Date</th>
                <th class="bold">Status</th>
                <th class="bold">Cost (KES)</th>
                <th class="bold">Supervisor Notes</th>
                <th class="bold">Report</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($record->completed_at)->format('d M Y') }}</td>
                    <td>{{ ucfirst($record->status) }}</td>
                    <td>{{ number_format($record->actual_cost, 2) }}</td>
                    <td>{{ optional($record->supervisorReports->first())->maintenance_notes ?? 'N/A' }}</td>
                    <td>
                        @php
                            $report = optional($record->supervisorReports->first());
                        @endphp
                        @if ($report && $report->report_file_path)
                            <a href="{{ asset('storage/'.$report->report_file_path) }}">View Report</a>
                        @else
                            No Report
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total Cost: KES {{ number_format($totalCost, 2) }}</p>

</body>

</html>