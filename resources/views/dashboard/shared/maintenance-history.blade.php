@extends('layouts.app')
@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="w-[80%] h-full sm:h-[85vh] rounded-lg bg-white backdrop:bg-gray/50">
            <div class="flex flex-col w-full p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Maintenance History</h1>
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
                {{-- Maintenance History Table --}}
                @php
                $headers = ['Reg ID', 'Date', 'Cost', 'Items', 'Location','bill'];
                $rows = [
                    ['12323', '32oct', 'saleem', 'engine', 'islamabad','<button>bill</button>'],
                    ['12323', '32oct', 'saleem', 'engine', 'islamabad','<button>bill</button>'],
                    ['12323', '32oct', 'saleem', 'engine', 'islamabad','<button>bill</button>'],
                    ['12323', '32oct', 'saleem', 'engine', 'islamabad','<button>bill</button>'],
                    ['12323', '32oct', 'saleem', 'engine', 'islamabad','<button>bill</button>'],
                ];
            @endphp

            <x-table :headers="$headers" :rows="$rows" />
            </div>
        </div>
    </div>

@endsection