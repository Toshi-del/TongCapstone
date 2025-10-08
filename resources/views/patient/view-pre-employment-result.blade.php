@extends('layouts.patient')

@section('title', 'Pre-Employment Medical Result')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-briefcase text-green-600"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">Pre-Employment Medical Result</h1>
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
                    @if($examination->company_name)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Company</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->company_name }}</p>
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

        <!-- Examination Package & Pricing Information -->
        @if($examination->preEmploymentRecord)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-emerald-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Examination Package & Pricing</h2>
                </div>
            </div>
            <div class="p-6">
                @php
                    $record = $examination->preEmploymentRecord;
                    $isAgeAdjusted = $record->age_adjusted ?? false;
                    $originalPrice = $record->original_price ?? $record->total_price;
                    $finalPrice = $record->total_price;
                    $priceDifference = $originalPrice - $finalPrice;
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Package Information -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg">
                        <div class="flex items-center space-x-3 mb-3">
                            <i class="fas fa-box text-blue-600"></i>
                            <h3 class="font-semibold text-blue-700">Examination Package</h3>
                        </div>
                        @if($record->medicalTest)
                            <p class="text-gray-900 font-medium mb-2">{{ $record->medicalTest->name }}</p>
                            @if($record->medicalTest->description)
                                <p class="text-gray-600 text-sm">{{ $record->medicalTest->description }}</p>
                            @endif
                        @else
                            <p class="text-gray-900 font-medium">Pre-Employment Medical Examination</p>
                        @endif
                    </div>
                    
                    <!-- Pricing Information -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg">
                        <div class="flex items-center space-x-3 mb-3">
                            <i class="fas fa-calculator text-green-600"></i>
                            <h3 class="font-semibold text-green-700">Pricing Details</h3>
                        </div>
                        @if($isAgeAdjusted && $priceDifference > 0)
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">Original Package:</span>
                                    <span class="text-gray-500 line-through text-sm">₱{{ number_format($originalPrice, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">Age Adjustment:</span>
                                    <span class="text-red-600 text-sm">-₱{{ number_format($priceDifference, 2) }}</span>
                                </div>
                                <hr class="border-gray-300">
                                <div class="flex justify-between items-center">
                                    <span class="text-green-700 font-semibold">Final Amount:</span>
                                    <span class="text-green-700 font-semibold text-lg">₱{{ number_format($finalPrice, 2) }}</span>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-between items-center">
                                <span class="text-green-700 font-semibold">Total Amount:</span>
                                <span class="text-green-700 font-semibold text-lg">₱{{ number_format($finalPrice, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Age Adjustment Notice -->
                @if($isAgeAdjusted && $priceDifference > 0)
                <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-info-circle text-amber-600 mt-1"></i>
                        <div class="flex-1">
                            <h4 class="text-amber-800 font-semibold mb-1">Age-Based Package Adjustment</h4>
                            <p class="text-amber-700 text-sm">
                                Since you are under 34 years old, your examination package was automatically adjusted from 
                                <strong>"Pre-Employment with ECG and Drug Test"</strong> to <strong>"Pre-Employment with Drug Test"</strong> only. 
                                The ECG examination was removed as it's not required for patients under 34, resulting in a price reduction of 
                                <strong>₱{{ number_format($priceDifference, 2) }}</strong>.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Fitness Assessment -->
        @if($examination->fitness_assessment)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    @if($examination->fitness_assessment === 'Fit to Work')
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-green-800">Fitness Assessment</h2>
                    @elseif($examination->fitness_assessment === 'Not Fit to Work')
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times text-red-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-red-800">Fitness Assessment</h2>
                    @else
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-yellow-800">Fitness Assessment</h2>
                    @endif
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-center p-8">
                    @if($examination->fitness_assessment === 'Fit to Work')
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-green-800 mb-2">{{ $examination->fitness_assessment }}</h3>
                            <p class="text-green-600">You are medically fit for employment</p>
                        </div>
                    @elseif($examination->fitness_assessment === 'Not Fit to Work')
                        <div class="text-center">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-times text-red-600 text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-red-800 mb-2">{{ $examination->fitness_assessment }}</h3>
                            <p class="text-red-600">Please consult with your healthcare provider</p>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-yellow-800 mb-2">{{ $examination->fitness_assessment }}</h3>
                            <p class="text-yellow-600">Additional evaluation may be required</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Physical Examination Findings -->
        @if($examination->physical_findings && is_array($examination->physical_findings))
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-stethoscope text-cyan-600"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Physical Examination Findings</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-cyan-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cyan-800 uppercase tracking-wider border-b border-cyan-200">Examination Area</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cyan-800 uppercase tracking-wider border-b border-cyan-200">Result</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-cyan-800 uppercase tracking-wider border-b border-cyan-200">Findings</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($examination->physical_findings as $area => $findings)
                                @if(is_array($findings) && (isset($findings['result']) || isset($findings['findings'])))
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ $area }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if(isset($findings['result']))
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $findings['result'] === 'Normal' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                                <i class="fas {{ $findings['result'] === 'Normal' ? 'fa-check-circle' : 'fa-exclamation-triangle' }} mr-1 text-xs"></i>
                                                {{ $findings['result'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-xs">Not specified</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $findings['findings'] ?? 'No findings recorded' }}
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
                                // Get all unique test names from lab_report data
                                $labTests = [];
                                $testIcons = [
                                    'chest_xray' => ['icon' => 'fas fa-lungs', 'color' => 'gray'],
                                    'cbc' => ['icon' => 'fas fa-tint', 'color' => 'red'],
                                    'fecalysis' => ['icon' => 'fas fa-vial', 'color' => 'yellow'],
                                    'urinalysis' => ['icon' => 'fas fa-flask', 'color' => 'orange'],
                                    'fbs' => ['icon' => 'fas fa-cube', 'color' => 'blue'],
                                    'bua' => ['icon' => 'fas fa-cube', 'color' => 'blue'],
                                    'hba1c' => ['icon' => 'fas fa-chart-line', 'color' => 'blue'],
                                    'sodium' => ['icon' => 'fas fa-atom', 'color' => 'blue'],
                                    'calcium' => ['icon' => 'fas fa-bone', 'color' => 'blue']
                                ];
                                
                                // Extract all test names from lab_report
                                if ($examination->lab_report && is_array($examination->lab_report)) {
                                    foreach ($examination->lab_report as $key => $value) {
                                        if (str_ends_with($key, '_result')) {
                                            $testKey = str_replace('_result', '', $key);
                                            if (!isset($labTests[$testKey])) {
                                                $displayName = ucwords(str_replace('_', ' ', $testKey));
                                                $labTests[$testKey] = [
                                                    'name' => $displayName,
                                                    'icon' => $testIcons[$testKey]['icon'] ?? 'fas fa-flask',
                                                    'color' => $testIcons[$testKey]['color'] ?? 'blue'
                                                ];
                                            }
                                        }
                                    }
                                }
                                
                                // Always include chest_xray even if not in lab_report
                                if (!isset($labTests['chest_xray'])) {
                                    $labTests['chest_xray'] = [
                                        'name' => 'Chest X-Ray',
                                        'icon' => 'fas fa-lungs',
                                        'color' => 'gray'
                                    ];
                                }
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
                
                @if(isset($examination->lab_report['additional_notes']) && $examination->lab_report['additional_notes'])
                <div class="mt-4 bg-blue-50 rounded-lg p-4 border-l-4 border-blue-400">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">
                        <i class="fas fa-sticky-note mr-2"></i>Additional Laboratory Notes
                    </h4>
                    <p class="text-sm text-blue-800">{{ $examination->lab_report['additional_notes'] }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif


        <!-- Drug Test Results -->
        @if($examination->drug_test && is_array($examination->drug_test))
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-vial text-yellow-600"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Drug Test Results</h2>
                </div>
            </div>
            <div class="p-6">
                <!-- Patient Information -->
                @if(isset($examination->drug_test['patient_name']) || isset($examination->drug_test['examination_datetime']))
                <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Test Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        @if(isset($examination->drug_test['patient_name']))
                        <div>
                            <span class="text-gray-600">Patient:</span>
                            <span class="font-medium text-gray-900 block">{{ $examination->drug_test['patient_name'] }}</span>
                        </div>
                        @endif
                        @if(isset($examination->drug_test['examination_datetime']))
                        <div>
                            <span class="text-gray-600">Test Date:</span>
                            <span class="font-medium text-gray-900 block">
                                @php
                                    $datetime = $examination->drug_test['examination_datetime'];
                                    if (is_string($datetime)) {
                                        try {
                                            $formatted = \Carbon\Carbon::parse($datetime)->format('F j, Y \a\t h:i A');
                                        } catch (\Exception $e) {
                                            $formatted = $datetime;
                                        }
                                    } else {
                                        $formatted = $datetime;
                                    }
                                @endphp
                                {{ $formatted }}
                            </span>
                        </div>
                        @endif
                        @if(isset($examination->drug_test['test_method']))
                        <div>
                            <span class="text-gray-600">Method:</span>
                            <span class="font-medium text-gray-900 block">{{ $examination->drug_test['test_method'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Drug Test Results -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($examination->drug_test as $drug => $result)
                        @if($result && !in_array($drug, ['patient_name', 'address', 'age', 'gender', 'examination_datetime', 'test_method']))
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $drug)) }}</h4>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $result === 'Negative' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas {{ $result === 'Negative' ? 'fa-check' : 'fa-exclamation-triangle' }} mr-1"></i>
                                    {{ $result }}
                                </span>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @elseif($examination->drugTestResults && $examination->drugTestResults->count() > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-vial text-yellow-600"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Drug Test Results</h2>
                </div>
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

        <!-- Medical History -->
        @if($examination->illness_history || $examination->past_medical_history || $examination->accidents_operations)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-history text-purple-600"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Medical History</h2>
                </div>
            </div>
            <div class="p-6 space-y-6">
                @if($examination->illness_history)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Past Illness/Disease</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm text-gray-700">{{ $examination->illness_history }}</p>
                    </div>
                </div>
                @endif
                @if($examination->accidents_operations)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Accidents/Operations</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm text-gray-700">{{ $examination->accidents_operations }}</p>
                    </div>
                </div>
                @endif
                @if($examination->past_medical_history)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Past Medical History</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm text-gray-700">{{ $examination->past_medical_history }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Doctor's Findings -->
        @if($examination->findings)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-md text-blue-600"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Doctor's Findings</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <p class="text-sm text-blue-800">{{ $examination->findings }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Additional Medical Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            @if($examination->visual)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-indigo-100 rounded flex items-center justify-center">
                            <i class="fas fa-eye text-indigo-600 text-xs"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Visual Acuity</h3>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-700">{{ $examination->visual }}</p>
                </div>
            </div>
            @endif

            @if($examination->ishihara_test)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-green-100 rounded flex items-center justify-center">
                            <i class="fas fa-palette text-green-600 text-xs"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Color Vision Test</h3>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-700">{{ $examination->ishihara_test }}</p>
                </div>
            </div>
            @endif

            @if($examination->ecg)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-red-100 rounded flex items-center justify-center">
                            <i class="fas fa-heartbeat text-red-600 text-xs"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">ECG Results</h3>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-700">{{ $examination->ecg }}</p>
                </div>
            </div>
            @endif

            @if($examination->skin_marks)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-orange-100 rounded flex items-center justify-center">
                            <i class="fas fa-search text-orange-600 text-xs"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Skin Marks</h3>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-700">{{ $examination->skin_marks }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-blue-50 rounded-lg p-6 text-center">
            <p class="text-blue-800 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                This medical examination was conducted by RSS Citi Health Services. 
                If you have any questions about your results, please contact our medical team.
            </p>
        </div>
    </div>
</div>
@endsection
