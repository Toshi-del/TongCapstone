@extends('layouts.company')

@section('title', 'Pre-Employment Examination Results')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-green-700 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-briefcase text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-white">Pre-Employment Medical Report</h1>
                            <p class="text-green-100 text-sm">Applicant: {{ $examination->preEmploymentRecord->full_name ?? ($examination->preEmploymentRecord->first_name . ' ' . $examination->preEmploymentRecord->last_name) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Completed & Sent
                        </span>
                        <a href="{{ route('company.medical-results', ['status' => 'sent_results']) }}" 
                           class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-lg text-sm font-medium hover:bg-white/30 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white text-green-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Message -->
        @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                <span class="text-blue-800 font-medium">{{ session('info') }}</span>
            </div>
        </div>
        @endif

        <!-- Applicant Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Applicant Information -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 rounded-t-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-tie text-green-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-gray-900">Applicant Information</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                            <div class="text-base font-semibold text-gray-900">{{ $examination->preEmploymentRecord->full_name ?? ($examination->preEmploymentRecord->first_name . ' ' . $examination->preEmploymentRecord->last_name) }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-green-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Age</label>
                            <div class="text-base font-semibold text-gray-900">{{ $examination->preEmploymentRecord->age }} years</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Sex</label>
                            <div class="text-base font-semibold text-gray-900">{{ $examination->preEmploymentRecord->sex }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-indigo-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Company</label>
                            <div class="text-base font-semibold text-gray-900">{{ $examination->preEmploymentRecord->company_name }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-orange-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Examination Date</label>
                            <div class="text-base font-semibold text-gray-900">
                                @if($examination->date)
                                    @php
                                        $examDate = \Carbon\Carbon::parse($examination->date);
                                    @endphp
                                    {{ $examDate->format('F j, Y \a\t g:i A') }}
                                @else
                                    Not specified
                                @endif
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-red-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Report Sent</label>
                            <div class="text-base font-semibold text-gray-900">{{ $examination->updated_at->format('F j, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 rounded-t-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-green-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-gray-900">Billing Summary</h2>
                    </div>
                </div>
                <div class="p-6">
                    @php
                        $totalAmount = 0;
                        $selectedTests = [];
                        
                        // Get tests from the new pivot table system
                        if ($examination->preEmploymentRecord && $examination->preEmploymentRecord->preEmploymentMedicalTests->count() > 0) {
                            foreach ($examination->preEmploymentRecord->preEmploymentMedicalTests as $pivotTest) {
                                if ($pivotTest->medicalTest) {
                                    $selectedTests[] = [
                                        'category' => $pivotTest->medicalTestCategory->name ?? 'Unknown Category',
                                        'test' => $pivotTest->medicalTest->name,
                                        'price' => $pivotTest->medicalTest->price ?? 0
                                    ];
                                    $totalAmount += $pivotTest->medicalTest->price ?? 0;
                                }
                            }
                        } else {
                            // Fallback to direct relationship
                            if ($examination->preEmploymentRecord && $examination->preEmploymentRecord->medicalTest) {
                                $selectedTests[] = [
                                    'category' => $examination->preEmploymentRecord->medicalTestCategory->name ?? 'Pre-Employment',
                                    'test' => $examination->preEmploymentRecord->medicalTest->name,
                                    'price' => $examination->preEmploymentRecord->medicalTest->price ?? 0
                                ];
                                $totalAmount += $examination->preEmploymentRecord->medicalTest->price ?? 0;
                            }
                        }
                        
                        // Use stored total_price if available and different
                        if ($examination->preEmploymentRecord && $examination->preEmploymentRecord->total_price > 0) {
                            $totalAmount = $examination->preEmploymentRecord->total_price;
                        }
                    @endphp
                    
                    @if(!empty($selectedTests))
                        @foreach($selectedTests as $test)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $test['test'] }}</p>
                                <p class="text-sm text-gray-500">{{ $test['category'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">₱{{ number_format($test['price'], 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="pt-4 border-t border-gray-200">
                            @if($examination->preEmploymentRecord && $examination->preEmploymentRecord->age_adjusted && $examination->preEmploymentRecord->original_price > $examination->preEmploymentRecord->total_price)
                                <!-- Age Adjustment Notice -->
                                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                    <div class="flex items-start space-x-3">
                                        <i class="fas fa-info-circle text-amber-600 mt-1"></i>
                                        <div class="flex-1">
                                            <h4 class="text-amber-800 font-semibold mb-1">Age-Based Package Adjustment</h4>
                                            <p class="text-amber-700 text-sm">
                                                Since the patient is under 34 years old, the examination package was automatically adjusted from 
                                                <strong>"Pre-Employment with ECG and Drug Test"</strong> to <strong>"Pre-Employment with Drug Test"</strong> only. 
                                                The ECG examination was removed as it's not required for patients under 34, resulting in a price reduction.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pricing Breakdown -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Original Package:</span>
                                        <span class="text-gray-500 line-through">₱{{ number_format($examination->preEmploymentRecord->original_price, 2) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Age Adjustment:</span>
                                        <span class="text-red-600">-₱{{ number_format($examination->preEmploymentRecord->original_price - $examination->preEmploymentRecord->total_price, 2) }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-base font-medium text-gray-900">
                                        @if($examination->preEmploymentRecord && $examination->preEmploymentRecord->age_adjusted)
                                            Final Amount
                                        @else
                                            Total Amount
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-bold text-green-600">₱{{ number_format($totalAmount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-receipt text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No billing information available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Medical History -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200 rounded-t-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-history text-purple-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Applicant Medical History</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-400">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-virus text-red-600 mr-2"></i>
                            <label class="block text-sm font-semibold text-red-900">Past Illness & Diseases</label>
                        </div>
                        <div class="text-sm text-red-800">{{ $examination->illness_history ?: 'No past illness recorded' }}</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 border-l-4 border-orange-400">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-ambulance text-orange-600 mr-2"></i>
                            <label class="block text-sm font-semibold text-orange-900">Accidents & Operations</label>
                        </div>
                        <div class="text-sm text-orange-800">{{ $examination->accidents_operations ?: 'None reported' }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-400">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-notes-medical text-yellow-600 mr-2"></i>
                            <label class="block text-sm font-semibold text-yellow-900">General Medical History</label>
                        </div>
                        <div class="text-sm text-yellow-800">{{ $examination->past_medical_history ?: 'No major medical issues' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family History & Personal Habits -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200 rounded-t-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Family History & Personal Habits</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Family History -->
                    <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-400">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-dna text-blue-600 mr-2"></i>
                            <h3 class="text-base font-semibold text-blue-900">Family Medical History</h3>
                        </div>
                        @if($examination->family_history && count($examination->family_history) > 0)
                            <div class="space-y-2">
                                @foreach($examination->family_history as $condition)
                                    <span class="inline-block px-3 py-1 bg-blue-200 text-blue-900 rounded-full text-sm font-medium mr-2 mb-2">
                                        {{ ucwords(str_replace('_', ' ', $condition)) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center text-blue-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                <p class="text-sm">No family history recorded</p>
                            </div>
                        @endif
                    </div>

                    <!-- Personal Habits -->
                    <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-400">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-leaf text-green-600 mr-2"></i>
                            <h3 class="text-base font-semibold text-green-900">Personal Habits & Lifestyle</h3>
                        </div>
                        @if($examination->personal_habits && count($examination->personal_habits) > 0)
                            <div class="space-y-2">
                                @foreach($examination->personal_habits as $habit)
                                    <span class="inline-block px-3 py-1 bg-green-200 text-green-900 rounded-full text-sm font-medium mr-2 mb-2">
                                        {{ ucwords(str_replace('_', ' ', $habit)) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center text-green-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                <p class="text-sm">No personal habits recorded</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Collapsible Medical Report -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <button onclick="toggleMedicalReport()" class="flex items-center justify-between w-full text-left">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-stethoscope text-teal-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-gray-900">Detailed Medical Report</h2>
                    </div>
                    <i id="medicalReportIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-200"></i>
                </button>
            </div>
            <div id="medicalReportContent" class="hidden">
                <!-- Physical Examination -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900 mb-4">Physical Examination</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Visual Acuity</label>
                            <div class="text-sm text-gray-900">{{ $examination->visual ?: 'Not tested' }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ishihara Test</label>
                            <div class="text-sm text-gray-900">{{ $examination->ishihara_test ?: 'Not tested' }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">ECG</label>
                            <div class="text-sm text-gray-900">{{ $examination->ecg ?: 'Not performed' }}</div>
                        </div>
                    </div>
                    @if($examination->skin_marks)
                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Skin Marks/Tattoos</label>
                        <div class="text-sm text-gray-900">{{ $examination->skin_marks }}</div>
                    </div>
                    @endif
                </div>

                <!-- Physical Findings -->
                @if($examination->physical_findings && count($examination->physical_findings) > 0)
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900 mb-4">Physical Findings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($examination->physical_findings as $area => $finding)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900">{{ $area }}</h4>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $finding['result'] === 'Normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $finding['result'] }}
                                </span>
                            </div>
                            @if(isset($finding['findings']) && $finding['findings'])
                                <p class="text-sm text-gray-700">{{ $finding['findings'] }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Laboratory Test Results -->
                @if($examination->lab_report && count($examination->lab_report) > 0)
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-flask text-blue-600 text-sm"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Laboratory Test Results</h3>
                    </div>
                    
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
                    
                    @if(isset($examination->lab_report['additional_notes']) && $examination->lab_report['additional_notes'])
                    <div class="mt-4 bg-blue-50 rounded-lg p-4 border-l-4 border-blue-400">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">
                            <i class="fas fa-sticky-note mr-2"></i>Additional Laboratory Notes
                        </h4>
                        <p class="text-sm text-blue-800">{{ $examination->lab_report['additional_notes'] }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- ECG Report -->
                @if($examination->ecg || $examination->ecg_date || $examination->ecg_technician)
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heartbeat text-red-600 text-sm"></i>
                        </div>
                        <h3 class="text-base font-medium text-gray-900">ECG Report (ECG Technician)</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-400">
                            <h4 class="text-sm font-medium text-red-900 mb-3">
                                <i class="fas fa-heartbeat mr-2"></i>ECG Results
                            </h4>
                            @if($examination->ecg)
                                <p class="text-sm text-red-800 mb-3">{{ $examination->ecg }}</p>
                            @else
                                <p class="text-sm text-red-600 italic">No ECG results recorded</p>
                            @endif
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">
                                <i class="fas fa-info-circle mr-2"></i>ECG Information
                            </h4>
                            <div class="space-y-2">
                                @if($examination->ecg_date)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-calendar text-gray-500 mr-2 w-4"></i>
                                    <span class="text-gray-600">Date:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ \Carbon\Carbon::parse($examination->ecg_date)->format('F j, Y') }}</span>
                                </div>
                                @endif
                                @if($examination->ecg_technician)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-user-md text-gray-500 mr-2 w-4"></i>
                                    <span class="text-gray-600">Technician:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $examination->ecg_technician }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif


                <!-- Drug Test Results -->
                @if($examination->drug_test && count($examination->drug_test) > 0)
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900 mb-4">Drug Test Results</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($examination->drug_test as $drug => $result)
                            @if($result)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $drug)) }}</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $result === 'Negative' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $result }}
                                </span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Medical Assessment & Fitness to Work -->
                @if($examination->fitness_assessment || $examination->drug_positive_count !== null || $examination->medical_abnormal_count !== null || $examination->physical_abnormal_count !== null)
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-purple-600 text-sm"></i>
                        </div>
                        <h3 class="text-base font-medium text-gray-900">Medical Assessment & Fitness to Work</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Fitness Assessment -->
                        <div class="bg-white rounded-lg p-4 border-l-4 {{ $examination->fitness_assessment === 'Fit to work' ? 'border-green-400' : ($examination->fitness_assessment === 'Not fit for work' ? 'border-red-400' : 'border-gray-400') }}">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900">
                                    <i class="fas fa-stethoscope text-gray-600 mr-2"></i>Fitness Assessment
                                </h4>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $examination->fitness_assessment === 'Fit to work' ? 'bg-green-100 text-green-800' : ($examination->fitness_assessment === 'Not fit for work' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    @if($examination->fitness_assessment === 'Fit to work')
                                        <i class="fas fa-check-circle mr-1"></i>
                                    @elseif($examination->fitness_assessment === 'Not fit for work')
                                        <i class="fas fa-times-circle mr-1"></i>
                                    @else
                                        <i class="fas fa-clock mr-1"></i>
                                    @endif
                                    {{ $examination->fitness_assessment ?? 'For evaluation' }}
                                </span>
                            </div>
                            @if($examination->drug_positive_count !== null || $examination->medical_abnormal_count !== null || $examination->physical_abnormal_count !== null)
                            <div class="text-xs text-gray-600 mt-2">
                                <div class="grid grid-cols-1 gap-1">
                                    <div>Drug Tests: {{ $examination->drug_positive_count ?? 0 }} positive</div>
                                    <div>Medical Tests: {{ $examination->medical_abnormal_count ?? 0 }} abnormal</div>
                                    <div>Physical Exam: {{ $examination->physical_abnormal_count ?? 0 }} abnormal</div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Assessment Summary -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Assessment Summary
                            </h4>
                            <div class="text-sm text-gray-700">
                                @php
                                    $drugPositive = $examination->drug_positive_count ?? 0;
                                    $medicalAbnormal = $examination->medical_abnormal_count ?? 0;
                                    $physicalAbnormal = $examination->physical_abnormal_count ?? 0;
                                @endphp
                                
                                @if($examination->fitness_assessment === 'Fit to work')
                                    <div class="flex items-center text-green-700 mb-2">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        <span class="font-medium">Employee is medically cleared for work</span>
                                    </div>
                                    <p class="text-xs text-gray-600">All medical tests, drug screening, and physical examination results are within acceptable parameters for employment.</p>
                                @elseif($examination->fitness_assessment === 'Not fit for work')
                                    <div class="flex items-center text-red-700 mb-2">
                                        <i class="fas fa-times-circle mr-2"></i>
                                        <span class="font-medium">Employee is not medically cleared for work</span>
                                    </div>
                                    <p class="text-xs text-gray-600">Medical examination results indicate conditions that may affect work performance or safety.</p>
                                @else
                                    <div class="flex items-center text-gray-700 mb-2">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span class="font-medium">Medical assessment for evaluation</span>
                                    </div>
                                    <p class="text-xs text-gray-600">The examination results require further medical evaluation by the doctor to determine fitness for work.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Final Findings -->
                @if($examination->findings)
                <div class="p-6">
                    <h3 class="text-base font-medium text-gray-900 mb-4">Doctor's Findings</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-900">{{ $examination->findings }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Report Footer -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-certificate text-gray-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Report Information</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Generated By</label>
                        <div class="text-sm text-gray-900">RSS Citi Health Services</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Date</label>
                        <div class="text-sm text-gray-900">{{ $examination->updated_at->format('F j, Y g:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMedicalReport() {
    const content = document.getElementById('medicalReportContent');
    const icon = document.getElementById('medicalReportIcon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>
@endsection
