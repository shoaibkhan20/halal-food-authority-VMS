@extends('layouts.app')

@section('content')
  <div class="">
    <!-- Header -->
    <div class="mb-6">
    <p class="text-[10px] capitalize">{{Auth::User()->role->role_name }}</p>
    <h1 class="text-xl font-bold"> Dashboard</h1>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 bg-white shadow rounded-lg">
    <div class=" p-4 flex items-center space-x-4">
      <div class="bg-green-800 p-2 rounded-full">
      <img src="{{ asset('images/icons/application.png') }}" alt="Applications" class="w-4 h-4">
      </div>
      <div>
      <p class="text-sm font-semibold">Total Applications</p>
      <p class="text-lg font-bold">{{ $totalApplications }}</p>
      </div>
    </div>

    <div class=" p-4 flex items-center space-x-4">
      <div class="bg-green-800 p-2 rounded-full">
      <img src="{{ asset('images/icons/vehicles.png') }}" alt="Vehicles" class="w-4 h-4">
      </div>
      <div>
      <p class="text-sm font-semibold">No of Vehicles</p>
      <p class="text-lg font-bold">{{ $totalVehicles }}</p>
      </div>
    </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Doughnut Chart -->
    <div class="bg-white rounded shadow p-4">
      <h2 class="text-center font-bold mb-4">Application Status</h2>
      <div class="relative w-full h-64">
      <canvas id="doughnutChart" class="w-full h-full"></canvas>
      </div>
    </div>

    <!-- Line Chart -->
    <div class="bg-white rounded shadow p-4">
      <h2 class="text-center font-bold mb-4">Total Cost</h2>
      <div class="relative w-full h-64">
      <canvas id="lineChart" class="w-full h-full"></canvas>
      </div>
    </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const doughnutLabels = @json(array_map('ucfirst', array_keys($applicationStatusData)));
    const doughnutData = @json(array_values($applicationStatusData));
    const lineLabels = @json(array_values($labels));
    const lineData = @json($costData);

    console.log('Doughnut Labels:', doughnutLabels);
    console.log('Doughnut Data:', doughnutData);
    console.log('Line Labels:', lineLabels);
    console.log('Line Data:', lineData);

    document.addEventListener('DOMContentLoaded', function () {
    const lineCtx = document.getElementById('lineChart');
    const doughnutCtx = document.getElementById('doughnutChart');

    if (lineCtx) {
      new Chart(lineCtx.getContext('2d'), {
      type: 'line',
      data: {
        labels: lineLabels,
        datasets: [{
        label: 'Cost',
        data: lineData,
        fill: true,
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.2)',
        tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
      });
    }

    if (doughnutCtx) {
      new Chart(doughnutCtx.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: doughnutLabels,
        datasets: [{
        data: doughnutData,
        backgroundColor: [
          
          '#4CAF50', // Green
          '#2196F3', // Blue
          '#FF9800', // Orange
          '#F44336', // Red
          '#9C27B0', // Purple
          '#FFEB3B'  // Yellow
          ],
      }]
      },
      options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
        position: 'bottom'
        }
      }
      }
      });
    }
    });
  </script>
@endpush