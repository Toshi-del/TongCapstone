@extends('layouts.doctor')

@section('title', 'Doctor Dashboard - RSS Citi Health Services')
@section('page-title', 'Doctor Dashboard')
@section('page-description', 'Medical professional dashboard and patient management')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="bg-white rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center border border-white border-opacity-30">
                        <i class="fas fa-user-md text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Welcome, Dr. {{ Auth::user()->fname }} {{ Auth::user()->lname }}</h2>
                        <p class="text-purple-100 text-sm">Medical professional dashboard and patient management system</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-white text-opacity-90 text-sm">{{ now()->format('l, F d, Y') }}</div>
                    <div id="current-time" class="text-white font-bold text-lg"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Patients -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Patients</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $patientCount + $preEmploymentCount }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                        <span class="text-gray-600">{{ $patientCount }} Annual Physical</span>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm mt-1">
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></span>
                        <span class="text-gray-600">{{ $preEmploymentCount }} Pre-Employment</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Annual Physical -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-medical text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Annual Physical</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $patientCount }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm">
                        <span class="text-blue-600 font-medium">
                            <i class="fas fa-stethoscope mr-1"></i>Active
                        </span>
                        <span class="text-gray-600 ml-2">examinations</span>
                    </div>
                    <a href="{{ route('doctor.annual-physical') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Pre-Employment -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pre-Employment</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $preEmploymentCount }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm">
                        <span class="text-green-600 font-medium">
                            <i class="fas fa-user-tie mr-1"></i>Applicants
                        </span>
                        <span class="text-gray-600 ml-2">screening</span>
                    </div>
                    <a href="{{ route('doctor.pre-employment') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-amber-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $patientCount + $preEmploymentCount }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm">
                        <span class="text-amber-600 font-medium">
                            <i class="fas fa-hourglass-half mr-1"></i>Awaiting
                        </span>
                        <span class="text-gray-600 ml-2">your review</span>
                    </div>
                    <a href="#" class="text-amber-600 hover:text-amber-800 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Patient Distribution Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-pie text-purple-600 mr-2"></i>Patient Distribution
                </h3>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-sync-alt mr-1"></i>Updated today
                </div>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="distributionChart"></canvas>
                <div id="emptyDistributionChart" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 bg-opacity-80 rounded-lg {{ ($patientCount + $preEmploymentCount) > 0 ? 'hidden' : '' }}">
                    <div class="w-20 h-20 text-gray-300 mb-4">
                        <i class="fas fa-chart-pie text-5xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No patient data available</p>
                    <p class="text-gray-400 text-sm mt-1">Patient distribution will appear here</p>
                </div>
            </div>
        </div>

        <!-- Examination Trends Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-line text-purple-600 mr-2"></i>Examination Overview
                </h3>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-sync-alt mr-1"></i>Updated today
                </div>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="trendsChart"></canvas>
                <div id="emptyTrendsChart" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 bg-opacity-80 rounded-lg {{ ($patientCount + $preEmploymentCount) > 0 ? 'hidden' : '' }}">
                    <div class="w-20 h-20 text-gray-300 mb-4">
                        <i class="fas fa-chart-bar text-5xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No examination data available</p>
                    <p class="text-gray-400 text-sm mt-1">Examination trends will appear here</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Examination Trends -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>Monthly Examination Trends
                </h3>
                <div class="text-sm text-gray-500">
                    <select id="yearSelector" class="border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                    </select>
                </div>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="monthlyTrendsChart"></canvas>
                <div id="emptyMonthlyChart" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 bg-opacity-80 rounded-lg">
                    <div class="w-20 h-20 text-gray-300 mb-4">
                        <i class="fas fa-calendar-check text-5xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No monthly data available</p>
                    <p class="text-gray-400 text-sm mt-1">Monthly examination trends will appear here</p>
                </div>
            </div>
        </div>

        <!-- Health Conditions Overview -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-heartbeat text-red-600 mr-2"></i>Health Conditions Overview
                </h3>
                <div class="text-sm text-gray-500">
                    <select id="conditionPeriod" class="border-gray-300 rounded-md text-sm focus:ring-red-500 focus:border-red-500">
                        <option value="all">All Time</option>
                        <option value="year">This Year</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="healthConditionsChart"></canvas>
                <div id="emptyHealthChart" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 bg-opacity-80 rounded-lg">
                    <div class="w-20 h-20 text-gray-300 mb-4">
                        <i class="fas fa-heartbeat text-5xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No health condition data available</p>
                    <p class="text-gray-400 text-sm mt-1">Health condition statistics will appear here</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Annual Physical Action -->
        <a href="{{ route('doctor.annual-physical') }}" class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-medical text-white text-xl"></i>
                </div>
                <i class="fas fa-arrow-right text-white text-opacity-60"></i>
            </div>
            <h3 class="text-white font-bold text-lg mb-1">Annual Physical</h3>
            <p class="text-purple-100 text-sm">Review patient examinations</p>
            <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                <span class="text-white font-semibold text-2xl">{{ $patientCount }}</span>
                <span class="text-purple-100 text-sm ml-2">patients</span>
            </div>
        </a>

        <!-- Pre-Employment Action -->
        <a href="{{ route('doctor.pre-employment') }}" class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-briefcase text-white text-xl"></i>
                </div>
                <i class="fas fa-arrow-right text-white text-opacity-60"></i>
            </div>
            <h3 class="text-white font-bold text-lg mb-1">Pre-Employment</h3>
            <p class="text-green-100 text-sm">Screen job applicants</p>
            <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                <span class="text-white font-semibold text-2xl">{{ $preEmploymentCount }}</span>
                <span class="text-green-100 text-sm ml-2">applicants</span>
            </div>
        </a>

        <!-- Pending Reviews Action -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 transform hover:scale-105 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-white text-xl"></i>
                </div>
                <i class="fas fa-arrow-right text-white text-opacity-60"></i>
            </div>
            <h3 class="text-white font-bold text-lg mb-1">Pending Reviews</h3>
            <p class="text-blue-100 text-sm">Examinations awaiting review</p>
            <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                <span class="text-white font-semibold text-2xl">{{ $patientCount + $preEmploymentCount }}</span>
                <span class="text-blue-100 text-sm ml-2">total</span>
            </div>
        </div>

        <!-- Reports Action -->
        <div class="bg-orange-500 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 transform hover:scale-105 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-white text-xl"></i>
                </div>
                <i class="fas fa-arrow-right text-white text-opacity-60"></i>
            </div>
            <h3 class="text-white font-bold text-lg mb-1">Reports</h3>
            <p class="text-orange-100 text-sm">Generate medical reports</p>
            <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                <span class="text-white font-semibold text-2xl">{{ $patientCount + $preEmploymentCount }}</span>
                <span class="text-orange-100 text-sm ml-2">available</span>
            </div>
        </div>
    </div>

    <!-- Recent Patients Section -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-friends text-purple-600 mr-2"></i>Recent Patients
                </h3>
                <a href="#" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="p-6">
            <div id="recentPatients" class="divide-y divide-gray-200">
                @if($recentPatients->count() > 0)
                    @foreach($recentPatients as $patient)
                    <div class="py-4 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-{{ $patient['color'] }}-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-{{ $patient['color'] }}-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $patient['name'] }}</p>
                                <p class="text-sm text-gray-500">{{ $patient['type'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $patient['time'] }}</p>
                            <p class="text-xs text-gray-500">{{ $patient['date'] }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <!-- Empty state for recent patients -->
                    <div class="py-8 text-center" id="emptyPatients">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                            <i class="fas fa-user-md text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="text-gray-500 font-medium mb-1">No recent patients</h3>
                        <p class="text-gray-400 text-sm">Recent patient activity will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-activity text-purple-600 mr-2"></i>Activity Summary
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-check text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Patients</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $patientCount + $preEmploymentCount }}</p>
                        <p class="text-xs text-gray-500 mt-1">Active examinations</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Today's Date</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ now()->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ now()->format('l') }}</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clipboard-check text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Examinations</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $patientCount + $preEmploymentCount }}</p>
                        <p class="text-xs text-gray-500 mt-1">Awaiting review</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    console.log('Dashboard script loaded');
    
    // Update current time every minute
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    
    // Update time immediately and then every minute
    updateTime();
    setInterval(updateTime, 60000);
    
    // Initialize Charts
    console.log('Setting up DOMContentLoaded listener');
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - Starting chart initialization');
        
        const hasData = {{ ($patientCount + $preEmploymentCount) > 0 ? 'true' : 'false' }};
        const patientCount = {{ $patientCount ?: 0 }};
        const preEmploymentCount = {{ $preEmploymentCount ?: 0 }};
        
        console.log('Has data:', hasData);
        console.log('Patient count:', patientCount);
        console.log('Pre-employment count:', preEmploymentCount);
        console.log('Chart.js loaded:', typeof Chart !== 'undefined');
        
        // Hide/show empty states based on data
        if (hasData) {
            document.getElementById('emptyDistributionChart').style.display = 'none';
            document.getElementById('emptyTrendsChart').style.display = 'none';
        }
        
        // Patient Distribution Chart (Doughnut)
        const distributionCtx = document.getElementById('distributionChart');
        if (distributionCtx) {
            new Chart(distributionCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Annual Physical', 'Pre-Employment'],
                    datasets: [{
                        data: [patientCount, preEmploymentCount],
                        backgroundColor: ['#9333ea', '#059669'],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 13,
                                family: "'Inter', sans-serif"
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
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
                        },
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                }
                }
            });
        }

        // Examination Overview Chart (Bar)
        const trendsCtx = document.getElementById('trendsChart');
        if (trendsCtx) {
            new Chart(trendsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Annual Physical', 'Pre-Employment', 'Total Patients'],
                    datasets: [{
                        label: 'Count',
                        data: [patientCount, preEmploymentCount, patientCount + preEmploymentCount],
                        backgroundColor: ['#9333ea', '#059669', '#3b82f6'],
                        borderRadius: 8,
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
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return `Patients: ${context.parsed.y}`;
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
                                size: 12
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
                }
            });
        }
        
        // Monthly Examination Trends Chart (Line)
        const monthlyCtxElement = document.getElementById('monthlyTrendsChart');
        @php
            $defaultMonthlyData = ['annual_physical' => array_fill(0, 12, 0), 'pre_employment' => array_fill(0, 12, 0)];
        @endphp
        const monthlyData = @json($monthlyData ?? $defaultMonthlyData);
        const hasMonthlyData = monthlyData.annual_physical.some(val => val > 0) || monthlyData.pre_employment.some(val => val > 0);
        
        if (monthlyCtxElement) {
            const monthlyChart = new Chart(monthlyCtxElement.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'Annual Physical',
                            data: monthlyData.annual_physical,
                            borderColor: '#9333ea',
                            backgroundColor: 'rgba(147, 51, 234, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Pre-Employment',
                            data: monthlyData.pre_employment,
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
                }
            });
            
            // Show/hide empty state for monthly chart
            if (!hasMonthlyData) {
                document.getElementById('emptyMonthlyChart').classList.remove('hidden');
            } else {
                document.getElementById('emptyMonthlyChart').classList.add('hidden');
            }
        }
        
        // Health Conditions Chart (Horizontal Bar)
        const healthCtxElement = document.getElementById('healthConditionsChart');
        @php
            $defaultHealthConditions = ['hypertension' => 0, 'diabetes' => 0, 'respiratory' => 0, 'cardiac' => 0, 'others' => 0];
        @endphp
        const healthConditions = @json($healthConditions ?? $defaultHealthConditions);
        const hasHealthData = Object.values(healthConditions).some(val => val > 0);
        
        if (healthCtxElement) {
            const healthChart = new Chart(healthCtxElement.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Hypertension', 'Diabetes', 'Respiratory Issues', 'Cardiac Issues', 'Others'],
                    datasets: [{
                        label: 'Patients',
                        data: [
                            healthConditions.hypertension || 0,
                            healthConditions.diabetes || 0,
                            healthConditions.respiratory || 0,
                            healthConditions.cardiac || 0,
                            healthConditions.others || 0
                        ],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(107, 114, 128, 0.7)'
                        ],
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
                }
            });
            
            // Show/hide empty state for health conditions chart
            if (!hasHealthData) {
                document.getElementById('emptyHealthChart').classList.remove('hidden');
            } else {
                document.getElementById('emptyHealthChart').classList.add('hidden');
            }
        }
        
        // Year selector for monthly trends
        const yearSelector = document.getElementById('yearSelector');
        if (yearSelector) {
            yearSelector.addEventListener('change', function() {
                // This would typically fetch data for the selected year
                // For now, just show the empty state
                const emptyMonthly = document.getElementById('emptyMonthlyChart');
                if (emptyMonthly) emptyMonthly.classList.remove('hidden');
            });
        }
        
        // Period selector for health conditions
        const conditionPeriod = document.getElementById('conditionPeriod');
        if (conditionPeriod) {
            conditionPeriod.addEventListener('change', function() {
                // This would typically fetch data for the selected period
                // For now, just show the empty state
                const emptyHealth = document.getElementById('emptyHealthChart');
                if (emptyHealth) emptyHealth.classList.remove('hidden');
            });
        }
        
        // Show empty states if no data
        if (!hasData) {
            const emptyDist = document.getElementById('emptyDistributionChart');
            const emptyTrends = document.getElementById('emptyTrendsChart');
            if (emptyDist) emptyDist.classList.remove('hidden');
            if (emptyTrends) emptyTrends.classList.remove('hidden');
        }
    });
</script>

<style>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out forwards;
    }
    
    /* Custom animations and transitions */
    .transition-all {
        transition: all 0.3s ease;
    }
    
    /* Pulse animation for empty states */
    @keyframes pulse {
        0% {
            opacity: 0.6;
        }
        50% {
            opacity: 1;
        }
        100% {
            opacity: 0.6;
        }
    }
    
    .animate-pulse {
        animation: pulse 2s infinite ease-in-out;
    }
    
    /* Hover effects for cards */
    .hover\:scale-105:hover {
        transform: scale(1.05);
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush