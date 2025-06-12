@php
    $logoPath = public_path('images/logo.png');
    $base64logo = '';
    if (file_exists($logoPath)) {
        $base64logo = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Maintenance Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            /* increased */
            color: #1a202c;
            margin: 20px;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-xl {
            font-size: 24px;
            /* increased */
        }

        .text-lg {
            font-size: 18px;
            /* increased */
        }

        .mx-auto {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .my-4 {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        hr {
            border: none;
            border-top: 1px solid #cbd5e0;
            /* Tailwind gray-300 */
            margin: 1rem 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            border: 1px solid #cbd5e0;
            /* Tailwind gray-300 */
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        thead {
            background-color: #e2e8f0;
            /* Tailwind gray-200 */
        }

        a {
            color: #2b6cb0;
            /* Tailwind blue-600 */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="text-center">
        @if($base64logo)
            <img src="{{ $base64logo }}" alt="Logo" class="mx-auto" style="height: 80px;">
        @endif
        <h1 class="text-xl font-bold">Food Safety & Halal Food Authority</h1>
        <hr class="my-4">
    </div>

    <div class="mb-4">
        <h2 class="text-lg font-semibold">Maintenance Report - {{ $vehicle->RegID }} ({{ $vehicle->Model }})</h2>
        <p>Branch: {{ $vehicle->branch->name ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Cost (KES)</th>
                <th>Supervisor Notes</th>
                <th>Report</th>
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
                            <a href="{{ asset('storage/' . $report->report_file_path) }}" target="_blank"
                                rel="noopener noreferrer">
                                View Report
                            </a>
                        @else
                            <span>No Report</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="mt-6"><strong>Total Cost:</strong> KES {{ number_format($totalCost, 2) }}</p>
</body>

</html>