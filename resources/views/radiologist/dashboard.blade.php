@extends('layouts.radiologist')

@section('title', 'Radiologist Dashboard')
@section('page-title', 'Radiologist Dashboard')
@section('page-description', 'X-Ray Review & Analysis Portal')

@section('content')
<div class="space-y-6">
    @php
        $totalXrays = $checklists->count();
        $pendingPreEmployment = $preEmployments->count();
        $pendingAnnual = $annuals->count();
        $totalPending = $pendingPreEmployment + $pendingAnnual;
    @endphp

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-slate-700 to-slate-800 rounded-xl shadow-lg overflow-hidden">
        <div class="px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-2">
                        <i class="fas fa-user-md mr-2"></i>Welcome, {{ Auth::user()->fname }}
                    </h1>
                    <p class="text-slate-300">{{ now()->format('l, F j, Y') }}</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm border-2 border-white/20">
                        <i class="fas fa-x-ray text-white text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total X-Rays -->
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-images text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Total</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalXrays }}</h3>
                <p class="text-sm text-gray-600">X-Ray Images</p>
            </div>
            <div class="bg-blue-50 px-6 py-2">
                <p class="text-xs text-blue-700">
                    <i class="fas fa-check-circle mr-1"></i>Available for review
                </p>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-amber-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Pending</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalPending }}</h3>
                <p class="text-sm text-gray-600">Awaiting Review</p>
            </div>
            <div class="bg-amber-50 px-6 py-2">
                <p class="text-xs text-amber-700">
                    <i class="fas fa-exclamation-circle mr-1"></i>Requires attention
                </p>
            </div>
        </div>

        <!-- Pre-Employment -->
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-briefcase text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Pre-Emp</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $pendingPreEmployment }}</h3>
                <p class="text-sm text-gray-600">Pre-Employment</p>
            </div>
            <div class="bg-purple-50 px-6 py-2">
                <p class="text-xs text-purple-700">
                    <i class="fas fa-user-tie mr-1"></i>New applicants
                </p>
            </div>
        </div>

        <!-- Annual Physical -->
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-emerald-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Annual</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $pendingAnnual }}</h3>
                <p class="text-sm text-gray-600">Annual Physical</p>
            </div>
            <div class="bg-emerald-50 px-6 py-2">
                <p class="text-xs text-emerald-700">
                    <i class="fas fa-heartbeat mr-1"></i>Routine checkups
                </p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Examination Distribution Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-pie text-slate-600 mr-2"></i>Examination Distribution
                </h3>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>

        <!-- Review Status Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-bar text-slate-600 mr-2"></i>Review Status
                </h3>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent X-Ray Images -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-images text-slate-600 mr-2"></i>Recent X-Ray Images
                </h3>
                <span class="text-sm text-gray-600">{{ $totalXrays }} total</span>
            </div>
        </div>
        <div class="p-6">
            @if($checklists->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($checklists as $c)
                        <div class="group border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-200 hover:border-slate-300">
                            @if($c->xray_image_path)
                                <div class="relative bg-slate-900 aspect-square">
                                    <img src="{{ asset('storage/' . $c->xray_image_path) }}" 
                                         alt="X-Ray" 
                                         class="w-full h-full object-contain">
                                    <div class="absolute top-2 right-2">
                                        <span class="bg-slate-800/80 text-white text-xs px-2 py-1 rounded-full backdrop-blur-sm">
                                            <i class="fas fa-x-ray mr-1"></i>X-Ray
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="aspect-square bg-slate-100 flex items-center justify-center">
                                    <div class="text-center text-gray-400">
                                        <i class="fas fa-image text-3xl mb-2"></i>
                                        <p class="text-xs">No image</p>
                                    </div>
                                </div>
                            @endif
                            <div class="p-3 bg-white">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $c->name ?? 'Unnamed Patient' }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="far fa-calendar mr-1"></i>{{ $c->date?->format('M d, Y') ?? 'No date' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-images text-slate-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium">No X-ray images found</p>
                    <p class="text-sm text-gray-500 mt-1">X-ray images will appear here once uploaded</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending Reviews Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pre-Employment Reviews -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-briefcase text-purple-600 mr-2"></i>Pre-Employment Reviews
                    </h3>
                    <span class="bg-purple-600 text-white text-xs font-medium px-2.5 py-1 rounded-full">
                        {{ $pendingPreEmployment }}
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                @if($preEmployments->count() > 0)
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($preEmployments as $row)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-purple-600 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $row['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">{{ $row['company'] ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('radiologist.pre-employment.show', $row['id']) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white text-xs font-medium rounded-lg transition-colors">
                                            <i class="fas fa-eye mr-1.5"></i>Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium">All caught up!</p>
                        <p class="text-sm text-gray-500 mt-1">No pending pre-employment reviews</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Annual Physical Reviews -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 px-6 py-4 border-b border-emerald-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-calendar-check text-emerald-600 mr-2"></i>Annual Physical Reviews
                    </h3>
                    <span class="bg-emerald-600 text-white text-xs font-medium px-2.5 py-1 rounded-full">
                        {{ $pendingAnnual }}
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                @if($annuals->count() > 0)
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($annuals as $row)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-emerald-600 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $row['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">{{ $row['company'] ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('radiologist.annual-physical.show', $row['id']) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white text-xs font-medium rounded-lg transition-colors">
                                            <i class="fas fa-eye mr-1.5"></i>Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium">All caught up!</p>
                        <p class="text-sm text-gray-500 mt-1">No pending annual physical reviews</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Distribution Chart (Pie)
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pre-Employment', 'Annual Physical'],
                datasets: [{
                    data: [{{ $pendingPreEmployment }}, {{ $pendingAnnual }}],
                    backgroundColor: ['#9333ea', '#059669'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Status Chart (Bar)
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['Total X-Rays', 'Pending Reviews', 'Pre-Employment', 'Annual Physical'],
                datasets: [{
                    label: 'Count',
                    data: [{{ $totalXrays }}, {{ $totalPending }}, {{ $pendingPreEmployment }}, {{ $pendingAnnual }}],
                    backgroundColor: ['#3b82f6', '#f59e0b', '#9333ea', '#059669'],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Count: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection


