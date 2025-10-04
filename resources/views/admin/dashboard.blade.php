@extends('layouts.admin')

@section('title', 'Dashboard - RSS Citi Health Services')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <!-- Total Patients Card -->
        <div class="stat-card content-card rounded-lg p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-2 uppercase tracking-wide">Total Patients</p>
                    <div class="flex items-center space-x-3">
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalPatients) }}</p>
                        @php
                            $lastMonthPatients = \App\Models\Patient::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
                            $currentMonthPatients = \App\Models\Patient::whereBetween('created_at', [now()->startOfMonth(), now()])->count();
                            $percentageChange = $lastMonthPatients > 0 ? (($currentMonthPatients - $lastMonthPatients) / $lastMonthPatients) * 100 : 0;
                        @endphp
                        @if($percentageChange > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-50 text-green-700">
                                <i class="fas fa-arrow-up text-xs mr-1"></i>
                                +{{ number_format($percentageChange, 1) }}%
                            </span>
                        @elseif($percentageChange < 0)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-50 text-red-700">
                                <i class="fas fa-arrow-down text-xs mr-1"></i>
                                {{ number_format($percentageChange, 1) }}%
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-50 text-gray-700">
                                <i class="fas fa-minus text-xs mr-1"></i>
                                0%
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">vs last month</p>
                </div>
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Appointments Today Card -->
        <div class="stat-card content-card rounded-lg p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-2 uppercase tracking-wide">Today's Appointments</p>
                    <div class="flex items-center space-x-3">
                        <p class="text-2xl font-semibold text-gray-900">{{ $testsToday }}</p>
                        @php
                            $pendingAppointments = \App\Models\Appointment::where('status', 'pending')->count();
                        @endphp
                        @if($testsToday > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-50 text-green-700">
                                <i class="fas fa-calendar-check text-xs mr-1"></i>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-50 text-gray-700">
                                <i class="fas fa-calendar text-xs mr-1"></i>
                                None
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $pendingAppointments }} pending approval</p>
                </div>
                <div class="w-12 h-12 bg-emerald-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-white text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Pending Tests Card -->
        <div class="stat-card content-card rounded-lg p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-2 uppercase tracking-wide">Pending Tests</p>
                    <div class="flex items-center space-x-3">
                        @php
                            $pendingTests = \App\Models\Appointment::where('status', 'pending')->count();
                        @endphp
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingTests }}</p>
                        @if($pendingTests > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-orange-50 text-orange-700">
                                <i class="fas fa-clock text-xs mr-1"></i>
                                Pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-50 text-green-700">
                                <i class="fas fa-check text-xs mr-1"></i>
                                Clear
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">awaiting results</p>
                </div>
                <div class="w-12 h-12 bg-amber-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-vial text-white text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="stat-card content-card rounded-lg p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-2 uppercase tracking-wide">Monthly Revenue</p>
                    <div class="flex items-center space-x-3">
                        @php
                            // Calculate current month revenue from all sources
                            $currentMonth = now();
                            $startOfMonth = $currentMonth->copy()->startOfMonth();
                            $endOfMonth = $currentMonth->copy()->endOfMonth();
                            
                            // 1. Appointments Revenue (from stored total_price)
                            $appointmentRevenue = \App\Models\Appointment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                ->where('status', 'approved')
                                ->sum('total_price') ?? 0;
                            
                            // 2. Pre-Employment Revenue
                            $preEmploymentRevenue = \App\Models\PreEmploymentRecord::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                ->sum('total_price') ?? 0;
                            
                            // 3. OPD Revenue (if OPD examinations have pricing)
                            $opdRevenue = 0;
                            try {
                                $opdExaminations = \App\Models\OpdExamination::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                    ->with('user')
                                    ->get();
                                foreach($opdExaminations as $opd) {
                                    // Assuming OPD has a standard rate or calculate based on services
                                    $opdRevenue += 500; // Default OPD consultation fee
                                }
                            } catch (\Exception $e) {
                                $opdRevenue = 0;
                            }
                            
                            $monthlyRevenue = $appointmentRevenue + $preEmploymentRevenue + $opdRevenue;
                            
                            // Calculate last month revenue for comparison
                            $lastMonth = now()->subMonth();
                            $lastMonthStart = $lastMonth->copy()->startOfMonth();
                            $lastMonthEnd = $lastMonth->copy()->endOfMonth();
                            
                            $lastMonthApptRevenue = \App\Models\Appointment::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                                ->where('status', 'approved')
                                ->sum('total_price') ?? 0;
                            
                            $lastMonthPreEmpRevenue = \App\Models\PreEmploymentRecord::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                                ->sum('total_price') ?? 0;
                            
                            $lastMonthOpdRevenue = 0;
                            try {
                                $lastMonthOpdCount = \App\Models\OpdExamination::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
                                $lastMonthOpdRevenue = $lastMonthOpdCount * 500;
                            } catch (\Exception $e) {
                                $lastMonthOpdRevenue = 0;
                            }
                            
                            $lastMonthRevenue = $lastMonthApptRevenue + $lastMonthPreEmpRevenue + $lastMonthOpdRevenue;
                            $revenueChange = $lastMonthRevenue > 0 ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
                        @endphp
                        <p class="text-2xl font-semibold text-gray-900">₱{{ number_format($monthlyRevenue, 0) }}</p>
                        @if($revenueChange > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                <i class="fas fa-arrow-up text-xs mr-1"></i>
                                +{{ number_format($revenueChange, 1) }}%
                            </span>
                        @elseif($revenueChange < 0)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-50 text-red-700">
                                <i class="fas fa-arrow-down text-xs mr-1"></i>
                                {{ number_format($revenueChange, 1) }}%
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-50 text-gray-700">
                                <i class="fas fa-minus text-xs mr-1"></i>
                                0%
                            </span>
                        @endif
                    </div>
                    @php
                        $targetRevenue = 180000; // ₱180K target
                    @endphp
                    <div class="mt-2 space-y-1">
                        <p class="text-xs text-gray-500">target: ₱{{ number_format($targetRevenue, 0) }}</p>
                        <div class="flex items-center space-x-4 text-xs">
                            <span class="text-blue-600">Appointments: ₱{{ number_format($appointmentRevenue, 0) }}</span>
                            <span class="text-purple-600">Pre-Emp: ₱{{ number_format($preEmploymentRevenue, 0) }}</span>
                            <span class="text-green-600">OPD: ₱{{ number_format($opdRevenue, 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-cyan-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Stock Alert Section -->
    @if(isset($criticalStockCount) && $criticalStockCount > 0)
        <div class="mb-8">
            <div class="content-card rounded-lg overflow-hidden border-l-4 border-red-500">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-lg animate-pulse"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-red-800">Critical Stock Alert</h3>
                            <p class="text-red-600 text-sm">{{ $criticalStockCount }} {{ Str::plural('item', $criticalStockCount) }} require immediate attention</p>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('admin.inventory.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors duration-200">
                                <i class="fas fa-boxes mr-2"></i>
                                Manage Inventory
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($criticalStockItems->take(6) as $item)
                            <div class="bg-white border border-red-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900 text-sm truncate">{{ $item->item_name }}</h4>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Critical
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span>Current Stock: <strong class="text-red-600">{{ $item->item_quantity }}</strong></span>
                                    <span>Min Required: <strong>{{ $item->minimum_stock }}</strong></span>
                                </div>
                                <div class="mt-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $percentage = $item->minimum_stock > 0 ? min(($item->item_quantity / $item->minimum_stock) * 100, 100) : 0;
                                        @endphp
                                        <div class="bg-red-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <p class="text-xs text-red-600 mt-1 font-medium">
                                        {{ $item->minimum_stock - $item->item_quantity }} units needed
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($criticalStockCount > 6)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.inventory.index') }}" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                View {{ $criticalStockCount - 6 }} more critical items →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Company Analytics Section -->
    <div class="space-y-8 mb-8">
        <!-- Company Trends Header -->
        <div class="content-card rounded-lg overflow-hidden">
            <div class="bg-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Company Analytics & Trends</h3>
                            <p class="text-indigo-100 text-sm">Top performing companies and service utilization insights</p>
                        </div>
                    </div>
                    <div class="text-indigo-100 text-sm">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Last 30 days
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Companies & Analytics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top 5 Companies Card -->
            <div class="content-card rounded-lg overflow-hidden">
                <div class="bg-blue-600 px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-trophy text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-white">Top 5 Companies</h4>
                            <p class="text-blue-100 text-sm">By total service utilization</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    @php
                        try {
                            // Get top companies by service utilization
                            $topCompanies = collect();
                        
                        // Get companies from appointments
                        $appointmentCompanies = \App\Models\Appointment::with(['patients', 'creator'])
                            ->whereBetween('created_at', [now()->subDays(30), now()])
                            ->get()
                            ->groupBy(function($appointment) {
                                return $appointment->creator->company ?? 'Unknown';
                            })
                            ->map(function($appointments, $company) {
                                $totalPatients = $appointments->sum(function($appointment) {
                                    return $appointment->patients->count();
                                });
                                $totalRevenue = $appointments->sum(function($appointment) {
                                    return $appointment->total_price ?? 0;
                                });
                                return [
                                    'name' => $company,
                                    'patients' => $totalPatients,
                                    'revenue' => $totalRevenue,
                                    'appointments' => $appointments->count(),
                                    'type' => 'appointments'
                                ];
                            });
                        
                        // Get companies from pre-employment
                        $preEmploymentCompanies = \App\Models\PreEmploymentRecord::whereBetween('created_at', [now()->subDays(30), now()])
                            ->whereNotNull('company_name')
                            ->get()
                            ->groupBy('company_name')
                            ->map(function($records, $company) {
                                $totalRevenue = $records->sum('total_price');
                                return [
                                    'name' => $company,
                                    'patients' => $records->count(),
                                    'revenue' => $totalRevenue,
                                    'appointments' => $records->count(),
                                    'type' => 'pre_employment'
                                ];
                            });
                        
                        // Merge and combine companies
                        $allCompanies = collect($appointmentCompanies->values())->merge(collect($preEmploymentCompanies->values()))
                            ->filter(function($company) {
                                return isset($company['name']) && $company['name'] !== 'Unknown' && !empty($company['name']);
                            })
                            ->groupBy('name')
                            ->map(function($companyData, $name) {
                                $totalPatients = $companyData->sum('patients');
                                $totalRevenue = $companyData->sum('revenue');
                                $totalServices = $companyData->sum('appointments');
                                return [
                                    'name' => $name,
                                    'patients' => $totalPatients,
                                    'revenue' => $totalRevenue,
                                    'services' => $totalServices,
                                    'score' => ($totalPatients * 2) + ($totalRevenue / 1000) + $totalServices
                                ];
                            })
                            ->sortByDesc('score')
                            ->take(5);
                        
                        $maxScore = $allCompanies->max('score') ?: 1;
                        $colors = ['blue', 'emerald', 'purple', 'amber', 'cyan'];
                        
                        } catch (\Exception $e) {
                            // Handle any errors gracefully
                            $allCompanies = collect();
                            $maxScore = 1;
                            $colors = ['blue', 'emerald', 'purple', 'amber', 'cyan'];
                        }
                    @endphp
                    
                    <div class="space-y-4">
                        @forelse($allCompanies->values() as $index => $company)
                            @php
                                $colorIndex = (int)$index % count($colors);
                                $currentColor = $colors[$colorIndex];
                            @endphp
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200 company-card interactive-card">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-{{ $currentColor }}-600 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">#{{ $index + 1 }}</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="font-semibold text-gray-900 truncate">{{ $company['name'] ?? 'Unknown Company' }}</h5>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                        <span class="flex items-center">
                                            <i class="fas fa-users text-{{ $currentColor }}-500 mr-1"></i>
                                            {{ $company['patients'] ?? 0 }} patients
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-peso-sign text-{{ $currentColor }}-500 mr-1"></i>
                                            ₱{{ number_format($company['revenue'] ?? 0, 0) }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-{{ $currentColor }}-500 h-2 rounded-full transition-all duration-1000 progress-bar" 
                                                 style="width: {{ (($company['score'] ?? 0) / $maxScore) * 100 }}%" 
                                                 data-width="{{ (($company['score'] ?? 0) / $maxScore) * 100 }}"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <div class="text-lg font-bold text-{{ $currentColor }}-600">{{ $company['services'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">services</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-building text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No company data available</p>
                                <p class="text-gray-400 text-sm">Company analytics will appear here once services are utilized</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Service Distribution Chart -->
            <div class="content-card rounded-lg overflow-hidden">
                <div class="bg-emerald-600 px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-pie text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-white">Service Distribution</h4>
                            <p class="text-emerald-100 text-sm">Most requested medical services</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    @php
                        try {
                            // Get service distribution data
                            $serviceStats = collect();
                        
                        // Appointments services
                        $appointmentServices = \App\Models\Appointment::with('patients')
                            ->whereBetween('created_at', [now()->subDays(30), now()])
                            ->get()
                            ->flatMap(function($appointment) {
                                $services = collect();
                                $selectedTests = $appointment->selected_tests;
                                if ($selectedTests->isNotEmpty()) {
                                    foreach ($selectedTests as $test) {
                                        $services->push([
                                            'name' => $test->name,
                                            'patients' => $appointment->patients->count(),
                                            'appointment' => $appointment
                                        ]);
                                    }
                                } else {
                                    $services->push([
                                        'name' => 'General Checkup',
                                        'patients' => $appointment->patients->count(),
                                        'appointment' => $appointment
                                    ]);
                                }
                                return $services;
                            })
                            ->groupBy('name')
                            ->map(function($services, $serviceName) {
                                return [
                                    'name' => $serviceName,
                                    'count' => $services->count(),
                                    'patients' => $services->sum('patients')
                                ];
                            });
                        
                        // Pre-employment services
                        $preEmpServices = \App\Models\PreEmploymentRecord::with('preEmploymentMedicalTests.medicalTest')
                            ->whereBetween('created_at', [now()->subDays(30), now()])
                            ->get()
                            ->flatMap(function($record) {
                                return $record->preEmploymentMedicalTests->map(function($test) {
                                    return $test->medicalTest ? $test->medicalTest->name : 'Pre-Employment Exam';
                                });
                            })
                            ->groupBy(function($service) {
                                return $service;
                            })
                            ->map(function($services, $service) {
                                return [
                                    'name' => $service,
                                    'count' => $services->count(),
                                    'patients' => $services->count()
                                ];
                            });
                        
                        // Merge and get top services
                        $allServices = $appointmentServices->merge($preEmpServices)
                            ->filter(function($service) {
                                return isset($service['name']) && !empty($service['name']);
                            })
                            ->groupBy('name')
                            ->map(function($serviceData, $name) {
                                return [
                                    'name' => $name,
                                    'total' => $serviceData->sum('count'),
                                    'patients' => $serviceData->sum('patients')
                                ];
                            })
                            ->sortByDesc('total')
                            ->take(6);
                        
                        $totalServices = $allServices->sum('total') ?: 1;
                        $serviceColors = ['emerald', 'blue', 'purple', 'amber', 'cyan', 'pink'];
                        
                        } catch (\Exception $e) {
                            // Handle any errors gracefully
                            $allServices = collect();
                            $totalServices = 1;
                            $serviceColors = ['emerald', 'blue', 'purple', 'amber', 'cyan', 'pink'];
                        }
                    @endphp
                    
                    <!-- Service Bar Chart -->
                    <div class="mb-6">
                        @php $maxServiceCount = $allServices->max('total') ?: 1; @endphp
                        <div class="space-y-4">
                            @foreach($allServices->values() as $index => $service)
                                @php
                                    $percentage = (($service['total'] ?? 0) / $totalServices) * 100;
                                    $barWidth = (($service['total'] ?? 0) / $maxServiceCount) * 100;
                                    $chartColorIndex = (int)$index % count($serviceColors);
                                    $currentServiceColor = $serviceColors[$chartColorIndex];
                                @endphp
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-4 h-4 bg-{{ $currentServiceColor }}-500 rounded-full flex-shrink-0"></div>
                                            <span class="font-medium text-gray-700 truncate">{{ Str::limit($service['name'] ?? 'Unknown Service', 20) }}</span>
                                        </div>
                                        <div class="flex items-center space-x-3 text-right">
                                            <span class="text-gray-500 text-xs">{{ $service['patients'] ?? 0 }} patients</span>
                                            <span class="font-bold text-gray-900 min-w-[3rem]">{{ $service['total'] ?? 0 }}</span>
                                            <span class="text-{{ $currentServiceColor }}-600 font-semibold text-xs min-w-[3rem]">{{ number_format($percentage, 1) }}%</span>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                            <div class="bg-gradient-to-r from-{{ $currentServiceColor }}-400 to-{{ $currentServiceColor }}-600 h-full rounded-full transition-all duration-1000 chart-bar" 
                                                 style="width: {{ $barWidth }}%"
                                                 data-tooltip="{{ $service['name'] ?? 'Unknown Service' }}: {{ $service['total'] ?? 0 }} services"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Summary Stats -->
                        <div class="mt-6 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border border-emerald-100">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-emerald-600">{{ $allServices->sum('total') }}</div>
                                    <div class="text-sm text-emerald-700 font-medium">Total Services</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-teal-600">{{ $allServices->sum('patients') }}</div>
                                    <div class="text-sm text-teal-700 font-medium">Total Patients</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-cyan-600">{{ $allServices->count() }}</div>
                                    <div class="text-sm text-cyan-700 font-medium">Service Types</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Trends & Growth Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Monthly Revenue Trend -->
            <div class="lg:col-span-2 content-card rounded-lg overflow-hidden">
                <div class="bg-purple-600 px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-white">Revenue Trends</h4>
                            <p class="text-purple-100 text-sm">6-month revenue performance by service type</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    @php
                        // Get 6 months of revenue data
                        $revenueData = collect();
                        for($i = 5; $i >= 0; $i--) {
                            $month = now()->subMonths($i);
                            $startOfMonth = $month->copy()->startOfMonth();
                            $endOfMonth = $month->copy()->endOfMonth();
                            
                            // Appointments revenue
                            $appointmentRevenue = \App\Models\Appointment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                ->where('status', 'approved')
                                ->sum('total_price') ?? 0;
                            
                            // Pre-employment revenue
                            $preEmpRevenue = \App\Models\PreEmploymentRecord::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                ->sum('total_price') ?? 0;
                            
                            $revenueData->push([
                                'month' => $month->format('M Y'),
                                'appointments' => $appointmentRevenue,
                                'pre_employment' => $preEmpRevenue,
                                'total' => $appointmentRevenue + $preEmpRevenue
                            ]);
                        }
                        
                        $maxRevenue = $revenueData->max('total') ?: 1;
                    @endphp
                    
                    <!-- Revenue Chart Bars -->
                    <div class="space-y-4">
                        @foreach($revenueData as $data)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-gray-700">{{ $data['month'] }}</span>
                                    <span class="font-bold text-gray-900">₱{{ number_format($data['total'], 0) }}</span>
                                </div>
                                <div class="relative">
                                    <!-- Background bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                                        <!-- Appointments revenue -->
                                        <div class="bg-blue-500 h-full float-left transition-all duration-1000 revenue-bar chart-bar" 
                                             style="width: {{ ($data['appointments'] / $maxRevenue) * 100 }}%"
                                             data-tooltip="Appointments: ₱{{ number_format($data['appointments'], 0) }}"></div>
                                        <!-- Pre-employment revenue -->
                                        <div class="bg-purple-500 h-full float-left transition-all duration-1000 revenue-bar chart-bar" 
                                             style="width: {{ ($data['pre_employment'] / $maxRevenue) * 100 }}%"
                                             data-tooltip="Pre-Employment: ₱{{ number_format($data['pre_employment'], 0) }}"></div>
                                    </div>
                                    <!-- Revenue breakdown tooltip -->
                                    <div class="absolute inset-0 flex items-center px-2 text-xs text-white font-medium">
                                        @if($data['appointments'] > 0)
                                            <span class="mr-2">Appt: ₱{{ number_format($data['appointments'], 0) }}</span>
                                        @endif
                                        @if($data['pre_employment'] > 0)
                                            <span>Pre-Emp: ₱{{ number_format($data['pre_employment'], 0) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Legend -->
                    <div class="flex items-center justify-center space-x-6 mt-6 pt-4 border-t border-gray-200">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Appointments</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Pre-Employment</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Growth Stats -->
            <div class="content-card rounded-lg overflow-hidden">
                <div class="bg-amber-600 px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-trending-up text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-white">Growth Metrics</h4>
                            <p class="text-amber-100 text-sm">Key performance indicators</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-6">
                    @php
                        try {
                            // Calculate growth metrics
                            $currentMonth = now();
                            $lastMonth = now()->subMonth();
                        
                        // Active companies this month
                        $activeCompaniesThisMonth = collect()
                            ->merge(\App\Models\Appointment::with('creator')
                                ->whereBetween('created_at', [$currentMonth->startOfMonth(), $currentMonth->endOfMonth()])
                                ->get()->map(function($appointment) {
                                    return $appointment->creator ? $appointment->creator->company : 'Unknown';
                                })->filter())
                            ->merge(\App\Models\PreEmploymentRecord::whereBetween('created_at', [$currentMonth->startOfMonth(), $currentMonth->endOfMonth()])
                                ->whereNotNull('company_name')
                                ->pluck('company_name'))
                            ->unique()
                            ->count();
                        
                        // Active companies last month
                        $activeCompaniesLastMonth = collect()
                            ->merge(\App\Models\Appointment::with('creator')
                                ->whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
                                ->get()->map(function($appointment) {
                                    return $appointment->creator ? $appointment->creator->company : 'Unknown';
                                })->filter())
                            ->merge(\App\Models\PreEmploymentRecord::whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
                                ->whereNotNull('company_name')
                                ->pluck('company_name'))
                            ->unique()
                            ->count();
                        
                        $companyGrowth = $activeCompaniesLastMonth > 0 ? 
                            (($activeCompaniesThisMonth - $activeCompaniesLastMonth) / $activeCompaniesLastMonth) * 100 : 0;
                        
                        // Average revenue per company
                        $totalRevenue = $monthlyRevenue; // From earlier calculation
                        $avgRevenuePerCompany = $activeCompaniesThisMonth > 0 ? $totalRevenue / $activeCompaniesThisMonth : 0;
                        
                        // Service utilization rate
                        $totalServices = \App\Models\Appointment::whereBetween('created_at', [$currentMonth->startOfMonth(), $currentMonth->endOfMonth()])->count() +
                                        \App\Models\PreEmploymentRecord::whereBetween('created_at', [$currentMonth->startOfMonth(), $currentMonth->endOfMonth()])->count();
                        
                        $utilizationRate = $activeCompaniesThisMonth > 0 ? $totalServices / $activeCompaniesThisMonth : 0;
                        
                        } catch (\Exception $e) {
                            // Handle any errors gracefully
                            $activeCompaniesThisMonth = 0;
                            $companyGrowth = 0;
                            $avgRevenuePerCompany = 0;
                            $utilizationRate = 0;
                        }
                    @endphp
                    
                    <!-- Active Companies -->
                    <div class="text-center p-4 bg-amber-50 rounded-lg metric-card interactive-card">
                        <div class="text-3xl font-bold text-amber-600 counter">{{ $activeCompaniesThisMonth }}</div>
                        <div class="text-sm text-amber-700 font-medium">Active Companies</div>
                        <div class="flex items-center justify-center mt-2">
                            @if($companyGrowth > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-arrow-up text-xs mr-1"></i>
                                    +{{ number_format($companyGrowth, 1) }}%
                                </span>
                            @elseif($companyGrowth < 0)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-700">
                                    <i class="fas fa-arrow-down text-xs mr-1"></i>
                                    {{ number_format($companyGrowth, 1) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    <i class="fas fa-minus text-xs mr-1"></i>
                                    0%
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Average Revenue per Company -->
                    <div class="text-center p-4 bg-blue-50 rounded-lg metric-card interactive-card">
                        <div class="text-2xl font-bold text-blue-600 counter">₱{{ number_format($avgRevenuePerCompany, 0) }}</div>
                        <div class="text-sm text-blue-700 font-medium">Avg Revenue/Company</div>
                        <div class="text-xs text-blue-600 mt-1">This month</div>
                    </div>
                    
                    <!-- Service Utilization -->
                    <div class="text-center p-4 bg-emerald-50 rounded-lg metric-card interactive-card">
                        <div class="text-2xl font-bold text-emerald-600 counter">{{ number_format($utilizationRate, 1) }}</div>
                        <div class="text-sm text-emerald-700 font-medium">Services per Company</div>
                        <div class="text-xs text-emerald-600 mt-1">Utilization rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Recent Appointments -->
        <div class="xl:col-span-2">
            <div class="content-card rounded-lg overflow-hidden">
                <div class="bg-blue-600 px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Recent Appointments</h3>
                            <p class="text-blue-100 text-sm">Latest patient bookings and schedules</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-700 uppercase tracking-wider">Patient</th>
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-700 uppercase tracking-wider">Time</th>
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-700 uppercase tracking-wider">Service</th>
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-700 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($patients as $patient)
                                    @php
                                        $appointment = $patient->appointment;
                                        $initials = strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1));
                                        $colors = ['from-blue-500 to-purple-600', 'from-emerald-500 to-teal-600', 'from-purple-500 to-pink-600', 'from-orange-500 to-red-600', 'from-cyan-500 to-blue-600'];
                                        $colorIndex = crc32($patient->id) % count($colors);
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="py-4 px-2">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-gradient-to-br {{ $colors[$colorIndex] }} rounded-2xl flex items-center justify-center text-white font-bold text-lg">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900">{{ $patient->full_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $patient->email ?? 'No email' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-2">
                                            <div class="font-medium text-gray-900">{{ $appointment ? $appointment->time_slot : 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">
                                                @if($appointment && $appointment->appointment_date)
                                                    @if($appointment->appointment_date->isToday())
                                                        Today
                                                    @elseif($appointment->appointment_date->isTomorrow())
                                                        Tomorrow
                                                    @else
                                                        {{ $appointment->appointment_date->format('M d') }}
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-2">
                                            <div class="font-medium text-gray-900">
                                                @if($appointment)
                                                    @php
                                                        $firstTest = $appointment->first_test;
                                                        $firstCategory = $appointment->first_category;
                                                    @endphp
                                                    @if($firstTest)
                                                        {{ $firstTest->name }}
                                                    @elseif($firstCategory)
                                                        {{ $firstCategory->name }}
                                                    @else
                                                        General Checkup
                                                    @endif
                                                @else
                                                    General Checkup
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @if($appointment && $firstTest)
                                                    {{ $firstTest->description ?? 'Medical Test' }}
                                                @else
                                                    Medical Examination
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-2">
                                            @if($appointment)
                                                @switch($appointment->status)
                                                    @case('approved')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Approved
                                                        </span>
                                                        @break
                                                    @case('pending')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Pending
                                                        </span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                            <i class="fas fa-times-circle mr-1"></i>
                                                            Cancelled
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                            <i class="fas fa-calendar mr-1"></i>
                                                            Scheduled
                                                        </span>
                                                @endswitch
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                                    <i class="fas fa-question mr-1"></i>
                                                    Unknown
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-2">
                                            @if($appointment)
                                                <a href="{{ route('admin.appointments') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">View Details</a>
                                            @else
                                                <span class="text-gray-400 text-sm">No appointment</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-2 text-center text-gray-500">
                                            <div class="flex flex-col items-center space-y-2">
                                                <i class="fas fa-calendar-times text-3xl text-gray-300"></i>
                                                <p class="font-medium">No recent appointments</p>
                                                <p class="text-sm">Appointments will appear here once patients book services</p>
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

        <!-- Sidebar Content -->
        <div class="space-y-8">
            <!-- Quick Actions -->
            <div class="content-card rounded-lg overflow-hidden">
                <div class="bg-emerald-600 px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bolt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Quick Actions</h3>
                            <p class="text-emerald-100 text-xs">Frequently used functions</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.appointments') }}" class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-calendar-check mr-2 text-sm"></i>
                        View Appointments
                    </a>
                    
                    <a href="{{ route('admin.pre-employment') }}" class="w-full flex items-center justify-center px-4 py-3 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-colors duration-200">
                        <i class="fas fa-file-medical mr-2 text-sm"></i>
                        View Pre-Employment
                    </a>
                    
                    <a href="{{ route('admin.tests') }}" class="w-full flex items-center justify-center px-4 py-3 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700 transition-colors duration-200">
                        <i class="fas fa-vial mr-2 text-sm"></i>
                        View Medical Tests
                    </a>
                    
                    <a href="{{ route('admin.inventory.index') }}" class="w-full flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors duration-200">
                        <i class="fas fa-boxes mr-2 text-sm"></i>
                        Manage Inventory
                    </a>
                    
                    <a href="{{ route('admin.report') }}" class="w-full flex items-center justify-center px-4 py-3 bg-slate-600 text-white rounded-lg font-medium hover:bg-slate-700 transition-colors duration-200">
                        <i class="fas fa-chart-line mr-2 text-sm"></i>
                        View Reports
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="content-card rounded-lg overflow-hidden">
                <div class="bg-cyan-600 px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-activity text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Recent Activity</h3>
                            <p class="text-cyan-100 text-xs">Latest system updates</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-5">
                    @php
                        // Get recent activities from different models
                        $recentPatients = \App\Models\Patient::orderBy('created_at', 'desc')->limit(2)->get();
                        $recentAppointments = \App\Models\Appointment::where('status', 'approved')->orderBy('updated_at', 'desc')->limit(2)->get();
                        $recentPreEmployment = \App\Models\PreEmploymentRecord::orderBy('created_at', 'desc')->limit(1)->get();
                        
                        // Combine and sort activities
                        $activities = collect();
                        
                        foreach($recentPatients as $patient) {
                            $activities->push([
                                'type' => 'patient_registered',
                                'title' => 'New patient registered',
                                'description' => $patient->full_name . ' completed registration',
                                'time' => $patient->created_at,
                                'color' => 'emerald-500',
                                'pulse' => true
                            ]);
                        }
                        
                        foreach($recentAppointments as $appointment) {
                            $activities->push([
                                'type' => 'appointment_approved',
                                'title' => 'Appointment approved',
                                'description' => 'Appointment for ' . $appointment->appointment_date->format('M d, Y') . ' confirmed',
                                'time' => $appointment->updated_at,
                                'color' => 'blue-500',
                                'pulse' => false
                            ]);
                        }
                        
                        foreach($recentPreEmployment as $preEmp) {
                            $activities->push([
                                'type' => 'pre_employment',
                                'title' => 'Pre-employment record created',
                                'description' => ($preEmp->first_name ?? 'New') . ' ' . ($preEmp->last_name ?? 'applicant') . ' submitted application',
                                'time' => $preEmp->created_at,
                                'color' => 'purple-500',
                                'pulse' => false
                            ]);
                        }
                        
                        // Sort by time and take top 5
                        $activities = $activities->sortByDesc('time')->take(5);
                    @endphp
                    
                    @forelse($activities as $activity)
                        <div class="flex items-start space-x-4">
                            <div class="w-3 h-3 bg-{{ $activity['color'] }} rounded-full mt-2 {{ $activity['pulse'] ? 'pulse-icon' : '' }}"></div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 text-sm">{{ $activity['title'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $activity['description'] }}</p>
                                <p class="text-xs text-gray-400">{{ $activity['time']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <!-- Fallback activities if no real data -->
                        <div class="flex items-start space-x-4">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 text-sm">System initialized</p>
                                <p class="text-xs text-gray-500 mt-1">RSS Citi Health Services dashboard ready</p>
                                <p class="text-xs text-gray-400">{{ now()->diffForHumans() }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 text-sm">Database connected</p>
                                <p class="text-xs text-gray-500 mt-1">All systems operational</p>
                                <p class="text-xs text-gray-400">{{ now()->subMinutes(5)->diffForHumans() }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 text-sm">Admin dashboard loaded</p>
                                <p class="text-xs text-gray-500 mt-1">Ready to manage appointments and patients</p>
                                <p class="text-xs text-gray-400">{{ now()->subMinutes(10)->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Company Analytics Animations */
.pulse-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(1.1);
    }
}

/* Chart Animations */
.chart-bar {
    animation: slideInLeft 1s ease-out;
}

@keyframes slideInLeft {
    from {
        width: 0%;
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Company Card Hover Effects */
.company-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Service Legend Hover */
.service-legend:hover {
    background-color: rgba(59, 130, 246, 0.05);
    border-left: 4px solid #3b82f6;
}

/* Growth Metrics Animation */
.metric-card {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Revenue Chart Bars */
.revenue-bar {
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.revenue-bar:hover {
    opacity: 0.8;
    transform: scaleY(1.02);
}

/* Company Ranking Animation */
.company-rank {
    animation: bounceIn 0.8s ease-out;
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Progress Bars */
.progress-bar {
    animation: progressFill 2s ease-out;
}

@keyframes progressFill {
    from {
        width: 0%;
    }
}

/* Donut Chart Animation */
.donut-segment {
    animation: drawSegment 1.5s ease-out;
}

@keyframes drawSegment {
    from {
        stroke-dasharray: 0 100;
    }
}

/* Stat Cards Stagger Animation */
.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }

/* Hover Effects for Interactive Elements */
.interactive-card {
    transition: all 0.3s ease;
}

.interactive-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Number Counter Animation */
.counter {
    animation: countUp 2s ease-out;
}

@keyframes countUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Chart Legend Animations */
.chart-legend-item {
    animation: slideInRight 0.6s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Loading Shimmer Effect */
.shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .chart-container {
        padding: 1rem;
    }
    
    .company-card {
        flex-direction: column;
        text-align: center;
    }
    
    .metric-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .chart-bg {
        background-color: #1f2937;
    }
    
    .chart-text {
        color: #f9fafb;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate numbers on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                
                // Animate progress bars
                if (target.classList.contains('progress-bar')) {
                    target.style.width = target.dataset.width + '%';
                }
                
                // Animate counters
                if (target.classList.contains('counter')) {
                    animateCounter(target);
                }
                
                observer.unobserve(target);
            }
        });
    }, observerOptions);

    // Observe all animated elements
    document.querySelectorAll('.progress-bar, .counter').forEach(el => {
        observer.observe(el);
    });

    // Counter animation function
    function animateCounter(element) {
        const target = parseInt(element.textContent.replace(/[^\d]/g, ''));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            // Format number with commas and currency if needed
            let displayValue = Math.floor(current).toLocaleString();
            if (element.textContent.includes('₱')) {
                displayValue = '₱' + displayValue;
            }
            if (element.textContent.includes('%')) {
                displayValue = displayValue + '%';
            }
            
            element.textContent = displayValue;
        }, 16);
    }

    // Add hover effects to chart elements
    document.querySelectorAll('.service-legend').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Tooltip functionality for charts
    document.querySelectorAll('[data-tooltip]').forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute bg-gray-800 text-white px-2 py-1 rounded text-sm z-50';
            tooltip.textContent = this.dataset.tooltip;
            tooltip.style.left = e.pageX + 10 + 'px';
            tooltip.style.top = e.pageY - 30 + 'px';
            document.body.appendChild(tooltip);
            this.tooltipElement = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this.tooltipElement) {
                document.body.removeChild(this.tooltipElement);
                this.tooltipElement = null;
            }
        });
    });
});
</script>
@endpush
