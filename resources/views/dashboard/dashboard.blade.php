@extends('layouts.app')

@section('content')
<div class="p-6">
  <!-- Header -->
  <div class=" mb-6">
    <h1 class="text-xl font-bold">Dashboard</h1>
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
<script>
  const doughnutData = @json(array_values($applicationStatusData));
  const lineLabels = @json($labels);
  const lineData = @json($costData);

  window.addEventListener('DOMContentLoaded', () => {
    // Line Chart
    const lineCtx = document.getElementById('lineChart');
    if (lineCtx) {
      new Chart(lineCtx.getContext('2d'), {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
          datasets: [{
            label: 'Cost',
            data: [5000, 8000, 7500, 6000, 9500],
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

    // Doughnut Chart
    const doughnutCtx = document.getElementById('doughnutChart');
    if (doughnutCtx) {
      new Chart(doughnutCtx.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: ['Approved', 'Pending', 'Rejected'],
          datasets: [{
            data: [60, 30, 10],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
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
