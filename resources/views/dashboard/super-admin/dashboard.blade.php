@extends('layouts.app')

@section('content')
<div class="p-6">
  <!-- Header -->
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold">Dashboard</h1>
    <div class="rounded-full p-2">
      <img src="{{ asset('images/icons/account_circle.png') }}" alt="User" class="w-6 h-6">
    </div>
  </div>
  <!-- Stat Cards -->
  <div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white shadow rounded-lg p-4 flex items-center space-x-4">
      <div class="bg-green-800 p-2 rounded-full">
        <img src="{{ asset('images/icons/application.png') }}" alt="File" class="w-4 h-4">
      </div>
      <div>
        <p class="text-sm font-semibold">Total Applications</p>
        <p class="text-lg font-bold">100</p>
      </div>
    </div>

    <div class="bg-white shadow rounded-lg p-4 flex items-center space-x-4">
      <div class="bg-green-800 p-2 rounded-full">
        <img src="{{ asset('images/icons/vehicles.png') }}" alt="File" class="w-4 h-4">
      </div>
      <div>
        <p class="text-sm font-semibold">No of Vehicles</p>
        <p class="text-lg font-bold">150</p>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Doughnut Chart -->
    <div class="bg-white rounded shadow p-4 flex justify-center">
      {{-- <canvas id="doughnutChart"></canvas> --}}
      <img src="{{ asset('images/piechart.png') }}" alt="File" class="w-20">
    </div>

    <!-- Line Chart -->
    <div class="bg-white rounded shadow p-4">
      <h2 class="text-center font-bold mb-4">Total Cost</h2>
      <canvas id="lineChart"></canvas>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Line Chart
  const lineCtx = document.getElementById('lineChart').getContext('2d');
  new Chart(lineCtx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Cost',
        data: [10000, 8000, 6000, 6000, 9000, 11000, 10000, 8500, 7000, 0, 7500, 12000],
        backgroundColor: 'rgba(16, 185, 129, 0.2)',
        borderColor: '#10b981',
        pointBackgroundColor: '#10b981',
        tension: 0.3,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Doughnut Chart
  const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
  new Chart(doughnutCtx, {
    type: 'doughnut',
    data: {
      labels: ['Approved', 'Pending', 'Rejected'],
      datasets: [{
        data: [60, 30, 10],
        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
        borderWidth: 1
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
</script>
@endpush
