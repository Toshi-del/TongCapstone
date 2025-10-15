@extends('layouts.admin')

@section('title', 'Analytics & Reports')
@section('page-title', 'Analytics & Reports')

@section('content')
@php
    // Ensure test data is available for the table rendered below
    $since = \Carbon\Carbon::now()->subDays(90);
    $testsEarly = \App\Models\MedicalTest::select('id','name','medical_test_category_id','price')->with('category')->get();
    $testData = $testsEarly->map(function($t) use ($since){
        // Pre-employment data
        $perCount = \App\Models\PreEmploymentRecord::where('medical_test_id', $t->id)
            ->where('created_at','>=',$since)
            ->count();
        $perRevenue = (float) (\App\Models\PreEmploymentRecord::where('medical_test_id', $t->id)
            ->where('created_at','>=',$since)
            ->sum('total_price') ?? 0);
        
        // Appointment data with patient counts
        $appointments = \App\Models\Appointment::where('medical_test_id', $t->id)
            ->where('created_at','>=',$since)
            ->with('patients')
            ->get();
        $apptCount = $appointments->count();
        $totalPatients = $appointments->sum(function($appointment) {
            return $appointment->patients->count();
        });
        
        // Calculate appointment revenue using dynamic pricing
        $apptRevenue = 0;
        foreach($appointments as $appointment) {
            $patientCount = $appointment->patients->count();
            $testPrice = $t->price ?? 0;
            $apptRevenue += ($testPrice * $patientCount);
        }
        
        return [
            'test' => $t->name,
            'category' => optional($t->category)->name,
            'pre_employment_count' => $perCount,
            'appointment_count' => $apptCount,
            'total_patients' => $totalPatients,
            'count' => $perCount + $apptCount,
            'per_revenue' => round($perRevenue, 2),
            'appt_revenue' => round($apptRevenue, 2),
            'revenue' => round($perRevenue + $apptRevenue, 2),
            'test_price' => $t->price ?? 0,
        ];
    })->filter(fn($row) => $row['count'] > 0)
      ->sortByDesc('count')
      ->take(10)
      ->values();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-indigo-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Enhanced Header Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Analytics & Reports</h1>
                        <p class="text-sm text-gray-600 mt-1">Comprehensive insights into medical test performance and revenue trends</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $testData->sum('pre_employment_count') }}</div>
                        <div class="text-xs text-gray-500">Pre-Employment (90d)</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $testData->sum('total_patients') }}</div>
                        <div class="text-xs text-gray-500">Total Patients (90d)</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $testData->sum('appointment_count') }}</div>
                        <div class="text-xs text-gray-500">Appointments (90d)</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">₱{{ number_format($testData->sum('revenue'), 0) }}</div>
                        <div class="text-xs text-gray-500">Total Revenue (90d)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export to Excel Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-excel text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Export Services Data</h2>
                        <p class="text-sm text-gray-500">Download detailed records with custom filters</p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('admin.report.export') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Date From -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-gray-400 mr-1"></i> Date From
                        </label>
                        <input type="date" 
                               id="date_from" 
                               name="date_from" 
                               value="{{ request('date_from', now()->subDays(90)->format('Y-m-d')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                    </div>
                    
                    <!-- Date To -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-gray-400 mr-1"></i> Date To
                        </label>
                        <input type="date" 
                               id="date_to" 
                               name="date_to" 
                               value="{{ request('date_to', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                    </div>
                    
                    <!-- Service Type -->
                    <div>
                        <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-filter text-gray-400 mr-1"></i> Service Type
                        </label>
                        <select id="service_type" 
                                name="service_type" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            <option value="all" {{ request('service_type') == 'all' ? 'selected' : '' }}>All Services</option>
                            <option value="pre_employment" {{ request('service_type') == 'pre_employment' ? 'selected' : '' }}>Pre-Employment</option>
                            <option value="annual_physical" {{ request('service_type') == 'annual_physical' ? 'selected' : '' }}>Annual Physical</option>
                            <option value="opd" {{ request('service_type') == 'opd' ? 'selected' : '' }}>OPD</option>
                        </select>
                    </div>
                    
                    <!-- Export Button -->
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg shadow-md hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-download"></i>
                            <span>Export to Excel</span>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2 text-sm text-gray-600 bg-blue-50 p-3 rounded-lg">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>Export includes all service records with patient details, test information, and pricing data.</span>
                </div>
            </form>
        </div>
        <!-- Charts Section -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Medical Test Volume Chart -->
            <div class="xl:col-span-2">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 h-full">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-area text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Medical Test Volume</h3>
                                    <p class="text-sm text-gray-500">Last 12 months trend</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">All sources</span>
                                <a href="{{ route('admin.report.export.test-volume') }}" 
                                   class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-full transition-colors duration-200 flex items-center space-x-1">
                                    <i class="fas fa-download text-xs"></i>
                                    <span>Export</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="medicalTestsTrend" height="80" tabindex="-1"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Categories Chart -->
            <div class="xl:col-span-1">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 h-full">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-pie text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Top Categories</h3>
                                    <p class="text-sm text-gray-500">Last 90 days</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.report.export.top-categories') }}" 
                               class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-full transition-colors duration-200 flex items-center space-x-1">
                                <i class="fas fa-download text-xs"></i>
                                <span>Export</span>
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="topCategoriesChart" height="80" tabindex="-1"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Tables and Revenue Chart Section -->
        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
            <!-- Top Medical Tests Table -->
            <div class="xl:col-span-2">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 h-full">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-trophy text-orange-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Top Medical Tests</h3>
                                    <p class="text-sm text-gray-500">Performance over last 90 days</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.report.export.top-tests') }}" 
                               class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-full transition-colors duration-200 flex items-center space-x-1">
                                <i class="fas fa-download text-xs"></i>
                                <span>Export</span>
                            </a>
                        </div>
                    </div>
                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pre-Emp</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Patients</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($testData as $index => $row)
                                        <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-6 h-6 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $row['test'] }}</div>
                                                        <div class="text-xs text-gray-500">₱{{ number_format($row['test_price'], 2) }} per test</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $row['category'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="text-sm font-semibold text-purple-600">{{ $row['pre_employment_count'] }}</div>
                                                <div class="text-xs text-gray-500">₱{{ number_format($row['per_revenue'], 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="text-sm font-semibold text-blue-600">{{ $row['total_patients'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $row['appointment_count'] }} appointments</div>
                                                <div class="text-xs text-gray-500">₱{{ number_format($row['appt_revenue'], 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="text-sm font-semibold text-green-600">₱{{ number_format($row['revenue'], 2) }}</div>
                                                <div class="text-xs text-gray-500">{{ $row['count'] }} total tests</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                                                        <i class="fas fa-chart-bar text-gray-400 text-lg"></i>
                                                    </div>
                                                    <p class="text-gray-500 text-sm">No test data available</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Trend Chart -->
            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 h-full">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-line text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Financial Trend</h3>
                                    <p class="text-sm text-gray-500">Revenue by month</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full">Pre-employment + Appointments</span>
                                <a href="{{ route('admin.report.export.financial-trend') }}" 
                                   class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-full transition-colors duration-200 flex items-center space-x-1">
                                    <i class="fas fa-download text-xs"></i>
                                    <span>Export</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="revenueTrend" height="90" tabindex="-1"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php

    // Build last 12 months labels
    $months = collect(range(0, 11))->map(fn($i) => \Carbon\Carbon::now()->subMonths(11 - $i));
    $monthLabels = $months->map(fn($m) => $m->format('M Y'));

    // Medical test volume per month (from pre-employment records and appointments with patient counts)
    $monthlyTestCounts = $months->map(function ($m) {
        $start = $m->copy()->startOfMonth();
        $end = $m->copy()->endOfMonth();
        $per = \App\Models\PreEmploymentRecord::whereBetween('created_at', [$start, $end])
            ->whereNotNull('medical_test_id')
            ->count();
        
        // Get appointments with patient counts for more accurate test volume
        $appointments = \App\Models\Appointment::whereBetween('created_at', [$start, $end])
            ->whereNotNull('medical_test_id')
            ->with('patients')
            ->get();
        $apptPatients = $appointments->sum(function($appointment) {
            return $appointment->patients->count();
        });
        
        return $per + $apptPatients;
    });

    // Top categories last 90 days by count (including patient counts)
    $since = \Carbon\Carbon::now()->subDays(90);
    $categoryData = \App\Models\MedicalTestCategory::select('id','name')
        ->get()
        ->map(function ($cat) use ($since) {
            $per = \App\Models\PreEmploymentRecord::where('medical_test_categories_id', $cat->id)
                ->where('created_at', '>=', $since)
                ->count();
            
            // Get appointments with patient counts for this category
            $appointments = \App\Models\Appointment::where('medical_test_categories_id', $cat->id)
                ->where('created_at', '>=', $since)
                ->with('patients')
                ->get();
            $apptPatients = $appointments->sum(function($appointment) {
                return $appointment->patients->count();
            });
            
            return [
                'name' => $cat->name,
                'count' => $per + $apptPatients,
            ];
        })
        ->sortByDesc('count')
        ->take(6)
        ->values();

    // Revenue trend per month (calculated with patient counts for appointments)
    $monthlyRevenue = $months->map(function ($m) {
        $start = $m->copy()->startOfMonth();
        $end = $m->copy()->endOfMonth();
        $perTotal = (float) (\App\Models\PreEmploymentRecord::whereBetween('created_at', [$start, $end])->sum('total_price') ?? 0);
        
        // Calculate appointment revenue with patient counts
        $appointments = \App\Models\Appointment::whereBetween('created_at', [$start, $end])
            ->with(['patients', 'medicalTest'])
            ->get();
        $apptTotal = 0;
        foreach($appointments as $appointment) {
            $patientCount = $appointment->patients->count();
            $testPrice = $appointment->medicalTest ? $appointment->medicalTest->price : 0;
            $apptTotal += ($testPrice * $patientCount);
        }
        
        return round($perTotal + $apptTotal, 2);
    });

    // This testData is already calculated above with enhanced patient count logic
    // No need to recalculate - the $testData variable is already available
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function(){
        const monthLabels = @json($monthLabels);
        const testCounts = @json($monthlyTestCounts);
        const revenue = @json($monthlyRevenue);
        const topCatLabels = @json($categoryData->pluck('name'));
        const topCatCounts = @json($categoryData->pluck('count'));

        const primary = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary') || '#0d6efd';
        const success = getComputedStyle(document.documentElement).getPropertyValue('--bs-success') || '#198754';
        const warning = getComputedStyle(document.documentElement).getPropertyValue('--bs-warning') || '#ffc107';

        // Medical tests trend
        new Chart(document.getElementById('medicalTestsTrend'), {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Tests',
                    data: testCounts,
                    tension: 0.35,
                    borderColor: primary.trim(),
                    backgroundColor: primary.trim() + '22',
                    borderWidth: 2,
                    fill: true,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        // Top categories bar
        new Chart(document.getElementById('topCategoriesChart'), {
            type: 'bar',
            data: {
                labels: topCatLabels,
                datasets: [{
                    label: 'Count',
                    data: topCatCounts,
                    backgroundColor: [primary.trim(), success.trim(), warning.trim(), '#6f42c1', '#20c997', '#fd7e14']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        // Revenue trend
        new Chart(document.getElementById('revenueTrend'), {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Revenue (PHP)',
                    data: revenue,
                    tension: 0.35,
                    borderColor: success.trim(),
                    backgroundColor: success.trim() + '22',
                    borderWidth: 2,
                    fill: true,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: (ctx) => '₱' + Number(ctx.parsed.y).toLocaleString(undefined, {minimumFractionDigits:2}) }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => '₱' + Number(v).toLocaleString()
                        }
                    }
                }
            }
        });
    })();
</script>
@endsection


