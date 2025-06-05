<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Maintenance Report - {{ $vehicle->RegID }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .text-center {
            text-align: center;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .text-lg {
            font-size: 1.125rem;
            font-weight: bold;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .border {
            border: 1px solid black;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .w-full {
            width: 100%;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .bg-gray-100 {
            background-color: #f3f4f6;
        }

        hr {
            margin: 20px 0;
        }

        a {
            color: #2563eb;
            text-decoration: underline;
        }

        .font-bold {
            font-weight: bold;
        }

    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="text-center mb-4">
        
        <img src="{{ asset('images/logo.png') }}" style="height:80px; width: auto;" class="mb-2">
        <div class="text-lg">Food Safety & Halal Food Authority</div>
    </div>

    <hr>

    <!-- Vehicle Info -->
    <h2 class="mb-2">Maintenance Report - {{ $vehicle->RegID }} ({{ $vehicle->Model }})</h2>
    <p class="mb-4"><strong>Branch:</strong> {{ $vehicle->branch->name ?? 'N/A' }}</p>

    <!-- Table -->
    <table class="w-full border border-collapse mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2 text-sm">Date</th>
                <th class="border p-2 text-sm">Status</th>
                <th class="border p-2 text-sm">Cost (KES)</th>
                <th class="border p-2 text-sm">Supervisor Notes</th>
                <th class="border p-2 text-sm">Supervisor Report</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td class="border p-2 text-sm">{{ \Carbon\Carbon::parse($record->completed_at)->format('d M Y') }}</td>
                    <td class="border p-2 text-sm">{{ ucfirst($record->status) }}</td>
                    <td class="border p-2 text-sm">{{ number_format($record->actual_cost, 2) }}</td>
                    <td class="border p-2 text-sm">
                        {{ optional($record->supervisorReports->first())->maintenance_notes ?? 'N/A' }}
                    </td>
                    <td class="border p-2 text-sm">
                        @php
                            $report = optional($record->supervisorReports->first());
                        @endphp
                        @if($report && $report->report_file_path)
                            <a href="{{ asset('storage/' . $report->report_file_path) }}">View</a>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <p class="font-bold">Total Maintenance Cost: KES {{ number_format($records->sum('actual_cost'), 2) }}</p>

</body>
</html>
