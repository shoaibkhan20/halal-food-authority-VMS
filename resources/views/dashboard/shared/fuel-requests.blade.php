@extends('layouts.app')
@section('content')

<div class="w-full min-h-screen grid place-items-center">
    <div class="w-full h-full rounded-lg bg-white">
        <div class="flex flex-col w-full p-8">
            <h1 class="text-3xl font-bold mb-6">Fuel Requests</h1>
            <div class="tabs tabs-lift">
                {{-- Pending Tab --}}
                <input type="radio" name="fuel_tabs" class="tab" aria-label="Pending" checked />
                <div class="tab-content p-6 bg-base-100 border-base-300">
                    @php
                        $headers = ['Reg ID', 'User', 'Liters', 'Total Amount','Invoice', 'Payment Method', 'Fuel Date' , 'Action'];
                        $rows = $pending->map(function($item) {
                                $userRole = Auth::user()?->role?->role_name;
                                $approveRoute = route('fuel-requests.approve', ['id' => $item->id]);
                                $rejectRoute = route('fuel-requests.reject', ['id' => $item->id]);
                                $actions = '';
                                $actions .= '
                                            <div class="flex gap-2">
                                            <form method="POST" action="' . $approveRoute . '">
                                                ' . csrf_field() . '
                                                <button type="submit" class="btn btn-sm bg-green-800 text-white">Approve</button>
                                            </form>
                                            <form method="POST" action="' . $rejectRoute . '">
                                                ' . csrf_field() . '
                                                <button type="submit" class="btn btn-sm bg-red-900 text-white">Reject</button>
                                            </form>
                                            </div>
                                        ';             
                                $invoice = '';
                                $invoice .= '<a href="' . $item->invoice . '" target="_blank" class="text-blue-400">View</a>';
                            return [
                                $item->vehicle_id,
                                $item->user?->name ?? '-',
                                $item->liter,
                                $item->fuel_amount,
                                $invoice ?? '-',  
                                $item->payment_method ?? '-',             
                                $item->fuel_date ?? '-',
                                $actions
                            ];
                        })->toArray();
                    @endphp
                    <x-table :headers="$headers" :rows="$rows" />
                </div>

                {{-- Approved Tab --}}
                <input type="radio" name="fuel_tabs" class="tab" aria-label="Approved" />
                <div class="tab-content p-6 bg-base-100 border-base-300">
                    @php
                        $headers = ['Reg ID', 'User', 'Liters', 'Total Amount','Invoice', 'Payment Method', 'Fuel Date'];

                        $rows = $approved->map(function($item) {
                            $invoice = '';
                            $invoice .= '<a href="' . $item->invoice . '" target="_blank" class="text-blue-400">View</a>';
                            return [
                                $item->vehicle_id,
                                $item->user?->name ?? '-',
                                $item->liter,
                                $item->fuel_amount,
                                $invoice,
                                $item->payment_method ?? '-',
                                $item->fuel_date ?? '-',
                            ];
                        })->toArray();
                    @endphp
                    <x-table :headers="$headers" :rows="$rows" />
                </div>


                {{-- Rejected Tab --}}
                <input type="radio" name="fuel_tabs" class="tab" aria-label="Rejected" />
                <div class="tab-content p-6 bg-base-100 border-base-300">
                    @php
                        $headers = ['Reg ID', 'User', 'Liters', 'Total Amount','Invoice', 'Payment Method', 'Fuel Date'];

                        $rows = $rejected->map(function($item) {
                            $invoice = '';
                            $invoice .= '<a href="' . $item->invoice . '" target="_blank" class="text-blue-400">View</a>';
                            return [
                                $item->vehicle_id,
                                $item->user?->name ?? '-',
                                $item->liter,
                                $item->fuel_amount,
                                $invoice,
                                $item->payment_method ?? '-',
                                $item->fuel_date ?? '-',
                            ];
                        })->toArray();
                    @endphp
                    <x-table :headers="$headers" :rows="$rows" />
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
