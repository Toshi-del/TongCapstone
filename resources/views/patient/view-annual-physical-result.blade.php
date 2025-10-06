@extends('layouts.patient')

@section('title', 'Annual Physical Medical Result')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heartbeat text-purple-600"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">Annual Physical Medical Result</h1>
                            <p class="text-sm text-gray-500">Exam ID: #{{ $examination->id }}</p>
                        </div>
                    </div>
                    <a href="{{ route('patient.medical-results') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Results
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Patient Information -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Patient Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Patient Name</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->name }}</p>
                    </div>
                    @if($examination->patient)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Patient ID</label>
                        <p class="text-lg font-semibold text-gray-900">#{{ $examination->patient->id }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Examination Date</label>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($examination->created_at)->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Result Received</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->updated_at->format('F d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Overall Health Status -->
        @if($examination->overall_health_status)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    @if(str_contains(strtolower($examination->overall_health_status), 'good') || str_contains(strtolower($examination->overall_health_status), 'excellent'))
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-green-800">Overall Health Status</h2>
                    @elseif(str_contains(strtolower($examination->overall_health_status), 'poor') || str_contains(strtolower($examination->overall_health_status), 'concern'))
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-red-800">Overall Health Status</h2>
                    @else
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-heartbeat text-blue-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-blue-800">Overall Health Status</h2>
                    @endif
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-center p-8">
                    <div class="text-center">
                        @if(str_contains(strtolower($examination->overall_health_status), 'good') || str_contains(strtolower($examination->overall_health_status), 'excellent'))
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-heart text-green-600 text-2xl"></i>
                            </div>
                        @elseif(str_contains(strtolower($examination->overall_health_status), 'poor') || str_contains(strtolower($examination->overall_health_status), 'concern'))
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                            </div>
                        @else
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-heartbeat text-blue-600 text-2xl"></i>
                            </div>
                        @endif
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $examination->overall_health_status }}</h3>
                        <p class="text-gray-600">Your annual physical examination status</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Vital Signs -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Vital Signs</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if($examination->height)
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-ruler-vertical text-blue-600"></i>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Height</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $examination->height }} cm</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($examination->weight)
                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-weight text-green-600"></i>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Weight</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $examination->weight }} kg</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($examination->blood_pressure)
                    <div class="p-4 bg-red-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-heartbeat text-red-600"></i>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Blood Pressure</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $examination->blood_pressure }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($examination->pulse_rate)
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-heart-pulse text-purple-600"></i>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Pulse Rate</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $examination->pulse_rate }} bpm</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Laboratory Test Results -->
        @if($examination->lab_report && count($examination->lab_report) > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-flask text-blue-600"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Laboratory Test Results</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-200">
                            @php
                                $labTests = [
                                    'chest_xray' => ['name' => 'Chest X-Ray', 'icon' => 'fas fa-lungs', 'color' => 'gray'],
                                    'cbc' => ['name' => 'CBC', 'icon' => 'fas fa-tint', 'color' => 'red'],
                                    'fecalysis' => ['name' => 'Fecalysis', 'icon' => 'fas fa-vial', 'color' => 'yellow'],
                                    'urinalysis' => ['name' => 'Urinalysis', 'icon' => 'fas fa-flask', 'color' => 'orange'],
                                    'hba1c' => ['name' => 'HbA1C', 'icon' => 'fas fa-chart-line', 'color' => 'blue'],
                                    'sodium' => ['name' => 'Sodium', 'icon' => 'fas fa-atom', 'color' => 'blue'],
                                    'calcium' => ['name' => 'Calcium', 'icon' => 'fas fa-bone', 'color' => 'blue']
                                ];
                            @endphp
                            
                            @foreach($labTests as $testKey => $testInfo)
                                @if(isset($examination->lab_report[$testKey . '_result']) || ($testKey === 'chest_xray'))
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-{{ $testInfo['color'] }}-100 flex items-center justify-center mr-3">
                                                <i class="{{ $testInfo['icon'] }} text-{{ $testInfo['color'] }}-600 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $testInfo['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div>
                                            <div class="text-xs text-gray-500 mb-1">Result</div>
                                            @if($testKey === 'chest_xray')
                                                <span class="text-sm font-medium text-gray-900">Normal</span>
                                            @elseif(isset($examination->lab_report[$testKey . '_result']))
                                                <span class="text-sm font-medium text-gray-900">{{ $examination->lab_report[$testKey . '_result'] }}</span>
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div>
                                            <div class="text-xs text-gray-500 mb-1">Findings</div>
                                            @if($testKey === 'chest_xray')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    NORMAL
                                                </span>
                                            @elseif(isset($examination->lab_report[$testKey . '_findings']) && $examination->lab_report[$testKey . '_findings'])
                                                <span class="text-sm text-gray-700">{{ $examination->lab_report[$testKey . '_findings'] }}</span>
                                            @elseif(isset($examination->lab_report[$testKey . '_result']))
                                                @if($examination->lab_report[$testKey . '_result'] === 'Normal')
                                                    <span class="text-sm text-gray-500">No findings</span>
                                                @elseif($examination->lab_report[$testKey . '_result'] === 'Not normal')
                                                    <span class="text-sm text-gray-500">No findings</span>
                                                @else
                                                    <span class="text-sm text-gray-500">No findings</span>
                                                @endif
                                            @else
                                                <span class="text-sm text-gray-500">No findings</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- X-Ray Results -->
        @if($examination->xray_findings)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">X-Ray Results</h2>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    {!! nl2br(e($examination->xray_findings)) !!}
                </div>
            </div>
        </div>
        @endif

        <!-- ECG Results -->
        @if($examination->ecg)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">ECG Results</h2>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    {!! nl2br(e($examination->ecg)) !!}
                </div>
            </div>
        </div>
        @endif

        <!-- Drug Test Results -->
        @if($examination->drugTestResults && $examination->drugTestResults->count() > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Drug Test Results</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($examination->drugTestResults as $drugTest)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $drugTest->test_name }}</h4>
                                <p class="text-sm text-gray-600">{{ $drugTest->test_date }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $drugTest->result === 'Negative' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $drugTest->result }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Doctor's Recommendations -->
        @if($examination->recommendations)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Doctor's Recommendations</h2>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    {!! nl2br(e($examination->recommendations)) !!}
                </div>
            </div>
        </div>
        @endif

        <!-- Health Summary -->
        @if($examination->health_summary)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Health Summary</h2>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    {!! nl2br(e($examination->health_summary)) !!}
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="bg-purple-50 rounded-lg p-6 text-center">
            <p class="text-purple-800 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                This annual physical examination was conducted by RSS Citi Health Services. 
                If you have any questions about your results, please contact our medical team.
            </p>
        </div>
    </div>
</div>
@endsection
