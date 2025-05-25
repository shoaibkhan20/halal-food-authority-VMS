@extends('layouts.app')
@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="w-full h-full overflow-y-auto rounded-lg bg-white backdrop:bg-gray/50">
            <div class="flex flex-col w-full p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Maintenance History Committe</h1>

                    {{-- <pre>{{$pendingRequests}}</pre> --}}
                </div>
                <div class="flex justify-between">
                    <div class="mb-6">
                        <input type="text" placeholder="Search Reg.id" class="border border-gray-300 rounded px-4 py-2">
                    </div>
                    <div class="mb-6">
                        <select class="border border-gray-300 rounded px-4 py-2">
                            <option>Last Month</option>
                            <option>Last 3 Months</option>
                            <option>All Time</option>
                        </select>
                    </div>
                </div>
                <div class="tabs tabs-lift">
                    <input type="radio" name="my_tabs_3" class="tab" aria-label="Pending Requests" checked="checked" />
                    <div class="tab-content bg-base-100 border-base-300 p-6">
                        @if (session('success'))
                            <div class="bg-green-200 text-green-800 p-4 mb-4 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="bg-red-200 text-red-800 p-4 mb-4 rounded-lg">
                                {{ session('error') }}
                            </div>
                        @endif
                        @php
                            $headers = ['Reg ID', 'Request Description', 'Estimated Cost', 'Applied By', 'Region', 'Rejection Reason' ,'Rejected By', 'Actions'];
                            $rows = $pendingRequests
                                ->filter(fn($r) => in_array($r->status, ['under_committee_review'])) // 🔥 Status filter here
                                ->map(function ($record) {
                                    $regId = $record->vehicle->RegID ?? 'N/A';
                                    $issue = $record->issue;
                                    $cost = $record->estimated_cost ? '$' . number_format($record->estimated_cost, 2) : 'N/A';
                                    $appliedBy = $record->appliedBy->name ?? 'N/A';
                                    $region = $record->vehicle->branch->location ?? 'N/A';
                                    $rejectionReason = $record->director_rejection_message;
                                    $rejectedby = $record->directorReviewer->name;

                                    $userRole = Auth::user()?->role?->role_name;
                                    $approveRoute = route('maintenance.approve', ['id' => $record->id]);
                                    $rejectRoute = route('maintenance.reject', ['id' => $record->id]);
                                    $actions = '';
                                    $actions .= '<div class="flex gap-2">';
                                    $actions .= '
                                            <form method="POST" action="' . $approveRoute . '">
                                                ' . csrf_field() . '
                                                <button type="submit" class="btn btn-sm bg-green-800 text-white">Approve</button>
                                            </form>
                                        ';
                                    $actions .= '
                                        <label for="reject-modal-' . $record->id . '" class="btn btn-sm bg-red-800 text-white hover:bg-red-700 transition">Reject</label>
                                    </div>';

                                    // Modal content
                                    $actions .= '
                                        <input type="checkbox" id="reject-modal-' . $record->id . '" class="modal-toggle" />
                                        <div class="modal">
                                            <div class="modal-box w-full max-w-lg">
                                                <h3 class="text-xl font-semibold text-gray-800 mb-4">Reject Maintenance Request</h3>
                                                <form method="POST" action="' . $rejectRoute . '" class="space-y-4">
                                                    ' . csrf_field() . '
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Message</label>
                                                        <textarea name="rejection_message" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" rows="4" placeholder="Reason for rejection..."></textarea>
                                                    </div>
                                                    <div class="flex justify-end gap-2 mt-6">
                                                        <button type="submit" class="btn bg-red-800 text-white hover:bg-red-700 transition">Submit</button>
                                                        <label for="reject-modal-' . $record->id . '" class="btn btn-outline">Cancel</label>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    ';
                                    return [$regId, $issue, $cost, $appliedBy, $region,$rejectionReason ,$rejectedby, $actions];
                                })->toArray();
                        @endphp

                        <x-table :headers="$headers" :rows="$rows" />
                    </div>

                

                    <input type="radio" name="my_tabs_3" class="tab" aria-label="History" />
                    <div class="tab-content bg-base-100 border-base-300 p-6">
                        @php
                            $headers = ['Reg ID', 'Request Description', 'Estimated Cost', 'Applied By', 'Region', 'Status'];
                            $rows = $pendingRequests
                                ->filter(fn($r) => in_array($r->status, ['committee_approved', 'committee_rejected', 'final_approved', 'final_rejected'])) // 🔥 Status filter here
                                ->map(function ($record) {
                                    $regId = $record->vehicle->RegID ?? 'N/A';
                                    $issue = $record->issue;
                                    $cost = $record->estimated_cost ? '$' . number_format($record->estimated_cost, 2) : 'N/A';
                                    $appliedBy = $record->appliedBy->name ?? 'N/A';
                                    $region = $record->vehicle->branch->location ?? 'N/A';
                                    $rejectionReason = $record->status;
                                    return [$regId, $issue, $cost, $appliedBy, $region, $rejectionReason];
                                })->toArray();
                        @endphp

                        <x-table :headers="$headers" :rows="$rows" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for each maintenance record --}}
    @foreach($maintenanceHistory as $record)
        <input type="checkbox" id="modal-{{ $record->id }}" class="modal-toggle" />
        <div class="modal" role="dialog">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-2">Maintenance Bill - {{ $record->vehicle_id }}</h3>
                <div class="space-y-1">
                    <p><strong>Date:</strong> {{ optional($record->started_at)->format('Y-m-d') ?? 'N/A' }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($record->status) }}</p>
                    <p><strong>Performed By:</strong> {{ $record->performed_by_user->name ?? 'N/A' }}</p>
                    <p><strong>Estimated Cost:</strong> ${{ number_format($record->estimated_cost, 2) }}</p>
                    <p><strong>Actual Cost:</strong> ${{ number_format($record->actual_cost, 2) }}</p>
                    <p><strong>Notes:</strong> {{ $record->maintenance_notes }}</p>
                </div>
                <div class="modal-action">
                    <label for="modal-{{ $record->id }}" class="btn">Close</label>
                </div>
            </div>
        </div>
    @endforeach

@endsection