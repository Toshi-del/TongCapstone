@extends('layouts.patient')

@section('title', 'Annual Physical Medical Result')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-file-medical-alt text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-1">Annual Physical Examination</h1>
                            <p class="text-blue-100">Complete Medical Assessment Report</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="bg-white/20 text-white text-sm px-3 py-1 rounded-full">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ \Carbon\Carbon::parse($examination->created_at)->format('M d, Y') }}
                                </span>
                                <span class="bg-white/20 text-white text-sm px-3 py-1 rounded-full">
                                    <i class="fas fa-hashtag mr-1"></i>
                                    Exam ID: {{ $examination->id }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('patient.medical-results') }}" 
                       class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 border border-white/20 backdrop-blur-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Results
                    </a>
                </div>
            </div>
        </div>

        @php
            $fitnessAssessment = $examination->fitness_assessment ?? 'For Evaluation';
            
            // Check assessment details for more accurate fitness determination
            $assessmentData = null;
            if (is_string($examination->assessment_details)) {
                $assessmentData = json_decode($examination->assessment_details, true);
            } elseif (is_array($examination->assessment_details)) {
                $assessmentData = $examination->assessment_details;
            }
            
            // Determine fitness status based on assessment details if available
            $isFitToWork = false;
            $isNotFitToWork = false;
            $isForEvaluation = false;
            
            if ($assessmentData && isset($assessmentData['applied_rule'])) {
                // Use the applied rule to determine fitness status
                $appliedRule = $assessmentData['applied_rule'];
                if (stripos($appliedRule, 'not fit') !== false || stripos($appliedRule, 'unfit') !== false) {
                    $isNotFitToWork = true;
                    $fitnessAssessment = $appliedRule; // Update the display text
                } elseif (stripos($appliedRule, 'fit') !== false) {
                    $isFitToWork = true;
                    $fitnessAssessment = $appliedRule; // Update the display text
                } else {
                    $isForEvaluation = true;
                }
            } else {
                // Fallback to original text-based logic
                $isFitToWork = stripos($fitnessAssessment, 'fit to work') !== false || stripos($fitnessAssessment, 'fit for work') !== false;
                $isNotFitToWork = stripos($fitnessAssessment, 'not fit') !== false || stripos($fitnessAssessment, 'unfit') !== false;
                $isForEvaluation = stripos($fitnessAssessment, 'evaluation') !== false || stripos($fitnessAssessment, 'further') !== false || $fitnessAssessment === 'For Evaluation';
            }
        @endphp

        <!-- FITNESS ASSESSMENT - PROMINENT DISPLAY -->
        <div class="mb-8">
            @if($isFitToWork)
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-2xl p-8 text-white">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-check-circle text-white text-4xl"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h2 class="text-4xl font-bold mb-4">FIT TO WORK</h2>
                        <p class="text-xl text-green-100 mb-4">{{ $fitnessAssessment }}</p>
                        <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                            <p class="text-lg font-medium">✓ You are medically cleared for employment</p>
                        </div>
                    </div>
                </div>
            @elseif($isNotFitToWork)
                <div class="bg-gradient-to-r from-red-500 to-pink-600 rounded-2xl shadow-2xl p-8 text-white">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-exclamation-triangle text-white text-4xl"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h2 class="text-4xl font-bold mb-4">NOT FIT TO WORK</h2>
                        <p class="text-xl text-red-100 mb-4">{{ $fitnessAssessment }}</p>
                        <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                            <p class="text-lg font-medium">⚠ Medical clearance required before employment</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl shadow-2xl p-8 text-white">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-hourglass-half text-white text-4xl"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h2 class="text-4xl font-bold mb-4">FOR EVALUATION</h2>
                        <p class="text-xl text-amber-100 mb-4">{{ $fitnessAssessment }}</p>
                        <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                            <p class="text-lg font-medium">📋 Further medical evaluation required</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Patient Information -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Patient Information</h2>
                </div>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-user text-blue-600"></i>
                            <label class="text-sm font-medium text-blue-700">Patient Name</label>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ $examination->name }}</p>
                    </div>
                    @if($examination->patient)
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-id-card text-purple-600"></i>
                            <label class="text-sm font-medium text-purple-700">Patient ID</label>
                        </div>
                        <p class="text-xl font-bold text-gray-900">#{{ $examination->patient->id }}</p>
                    </div>
                    @endif
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-calendar-alt text-green-600"></i>
                            <label class="text-sm font-medium text-green-700">Examination Date</label>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($examination->created_at)->format('M d, Y') }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-clock text-orange-600"></i>
                            <label class="text-sm font-medium text-orange-700">Result Received</label>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ $examination->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Vital Signs & Physical Measurements -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-green-50 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heartbeat text-green-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Vital Signs & Physical Measurements</h2>
                </div>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if($examination->height)
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-ruler-vertical text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900">{{ $examination->height }}</p>
                                <p class="text-sm text-blue-600 font-medium">cm</p>
                            </div>
                        </div>
                        <p class="text-blue-700 font-medium">Height</p>
                    </div>
                    @endif
                    
                    @if($examination->weight)
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-weight text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900">{{ $examination->weight }}</p>
                                <p class="text-sm text-green-600 font-medium">kg</p>
                            </div>
                        </div>
                        <p class="text-green-700 font-medium">Weight</p>
                    </div>
                    @endif
                    
                    @if($examination->blood_pressure)
                    <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-xl border border-red-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-heartbeat text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900">{{ $examination->blood_pressure }}</p>
                                <p class="text-sm text-red-600 font-medium">mmHg</p>
                            </div>
                        </div>
                        <p class="text-red-700 font-medium">Blood Pressure</p>
                    </div>
                    @endif
                    
                    @if($examination->pulse_rate)
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-heart-pulse text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900">{{ $examination->pulse_rate }}</p>
                                <p class="text-sm text-purple-600 font-medium">bpm</p>
                            </div>
                        </div>
                        <p class="text-purple-700 font-medium">Pulse Rate</p>
                    </div>
                    @endif
                </div>
                
                <!-- Additional Physical Measurements -->
                @if($examination->visual || $examination->ishihara_test)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Vision Assessment</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($examination->visual)
                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-6 rounded-xl border border-indigo-200">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-eye text-white"></i>
                                </div>
                                <h4 class="text-lg font-bold text-indigo-800">Visual Acuity</h4>
                            </div>
                            <p class="text-gray-800 font-medium">{{ $examination->visual }}</p>
                        </div>
                        @endif
                        
                        @if($examination->ishihara_test)
                        <div class="bg-gradient-to-br from-teal-50 to-teal-100 p-6 rounded-xl border border-teal-200">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-10 h-10 bg-teal-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-palette text-white"></i>
                                </div>
                                <h4 class="text-lg font-bold text-teal-800">Color Vision</h4>
                            </div>
                            <p class="text-gray-800 font-medium">{{ $examination->ishihara_test }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Laboratory Test Results -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-flask text-blue-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Laboratory Test Results</h2>
                </div>
            </div>
            <div class="p-8">
                @php
                    $labTests = [
                        'urinalysis' => ['name' => 'Urinalysis', 'icon' => 'fas fa-flask', 'color' => 'yellow'],
                        'cbc' => ['name' => 'Complete Blood Count (CBC)', 'icon' => 'fas fa-tint', 'color' => 'red'],
                        'xray' => ['name' => 'Chest X-Ray', 'icon' => 'fas fa-lungs', 'color' => 'gray'],
                        'fecalysis' => ['name' => 'Fecalysis', 'icon' => 'fas fa-vial', 'color' => 'orange'],
                        'blood_chemistry' => ['name' => 'Blood Chemistry', 'icon' => 'fas fa-chart-line', 'color' => 'purple'],
                        'others' => ['name' => 'Other Tests', 'icon' => 'fas fa-plus-circle', 'color' => 'indigo'],
                        'hbsag_screening' => ['name' => 'HBsAg Screening', 'icon' => 'fas fa-shield-virus', 'color' => 'green'],
                        'hepa_a_igg_igm' => ['name' => 'Hepatitis A IgG/IgM', 'icon' => 'fas fa-virus', 'color' => 'teal']
                    ];
                    
                    // Check if we have any valid lab results
                    $hasLabResults = false;
                    if ($examination->lab_report && is_array($examination->lab_report)) {
                        foreach ($labTests as $testKey => $testInfo) {
                            if (isset($examination->lab_report[$testKey]) && 
                                $examination->lab_report[$testKey] !== 'Not available' && 
                                !empty($examination->lab_report[$testKey]) &&
                                $examination->lab_report[$testKey] !== null) {
                                $hasLabResults = true;
                                break;
                            }
                        }
                    }
                @endphp
                
                @if($hasLabResults)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($labTests as $testKey => $testInfo)
                            @if(isset($examination->lab_report[$testKey]) && $examination->lab_report[$testKey] !== 'Not available' && !empty($examination->lab_report[$testKey]) && $examination->lab_report[$testKey] !== null)
                            <div class="bg-gradient-to-br from-{{ $testInfo['color'] }}-50 to-{{ $testInfo['color'] }}-100 p-6 rounded-xl border border-{{ $testInfo['color'] }}-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-{{ $testInfo['color'] }}-500 rounded-xl flex items-center justify-center">
                                            <i class="{{ $testInfo['icon'] }} text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-{{ $testInfo['color'] }}-800">{{ $testInfo['name'] }}</h3>
                                        </div>
                                    </div>
                                    @php
                                        $result = $examination->lab_report[$testKey];
                                        $isNormal = stripos($result, 'normal') !== false;
                                        $isAbnormal = stripos($result, 'abnormal') !== false || stripos($result, 'positive') !== false;
                                    @endphp
                                    @if($isNormal)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Normal
                                        </span>
                                    @elseif($isAbnormal)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Abnormal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            Result
                                        </span>
                                    @endif
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm font-medium text-{{ $testInfo['color'] }}-700 mb-1">Result:</p>
                                        <p class="text-lg font-bold text-gray-900">{{ $examination->lab_report[$testKey] }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <!-- Empty State Design -->
                    <div class="text-center py-12">
                        <div class="flex items-center justify-center mb-6">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-flask text-gray-400 text-3xl"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 mb-3">No Laboratory Results Available</h3>
                        <p class="text-gray-500 mb-6 max-w-md mx-auto">
                            Laboratory test results have not been completed or uploaded yet. Please check back later or contact your healthcare provider for more information.
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <i class="fas fa-flask text-gray-400 text-lg mb-2"></i>
                                <p class="text-xs text-gray-600">Urinalysis</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <i class="fas fa-tint text-gray-400 text-lg mb-2"></i>
                                <p class="text-xs text-gray-600">Blood Count</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <i class="fas fa-lungs text-gray-400 text-lg mb-2"></i>
                                <p class="text-xs text-gray-600">Chest X-Ray</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <i class="fas fa-vial text-gray-400 text-lg mb-2"></i>
                                <p class="text-xs text-gray-600">Other Tests</p>
                            </div>
                        </div>
                        <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-center space-x-2 text-blue-700">
                                <i class="fas fa-info-circle"></i>
                                <p class="text-sm font-medium">Laboratory results will appear here once they are available</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Medical Imaging & Diagnostic Tests -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- X-Ray Results -->
            @if($examination->xray_findings || (isset($examination->lab_report['chest_xray_result']) && $examination->lab_report['chest_xray_result'] !== 'Not available'))
            <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-lungs text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Chest X-Ray</h3>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($examination->lab_report['chest_xray_result']))
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-600 mb-2">Result:</p>
                            <p class="text-lg font-bold text-gray-900">{{ $examination->lab_report['chest_xray_result'] }}</p>
                        </div>
                    @endif
                    @if($examination->xray_findings)
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-2">Findings:</p>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-800 leading-relaxed">{!! nl2br(e($examination->xray_findings)) !!}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- ECG Results -->
            @if($examination->ecg)
            <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-heartbeat text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-red-800">Electrocardiogram (ECG)</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="bg-red-50 rounded-lg p-4">
                        <p class="text-gray-800 leading-relaxed">{!! nl2br(e($examination->ecg)) !!}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Drug Test Results -->
        @if($examination->drugTestResults && $examination->drugTestResults->count() > 0)
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-vial text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-purple-800">Drug Test Results</h2>
                </div>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($examination->drugTestResults as $drugTest)
                    <div class="bg-gradient-to-br from-{{ $drugTest->result === 'Negative' ? 'green' : 'red' }}-50 to-{{ $drugTest->result === 'Negative' ? 'green' : 'red' }}-100 p-6 rounded-xl border border-{{ $drugTest->result === 'Negative' ? 'green' : 'red' }}-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-{{ $drugTest->result === 'Negative' ? 'green' : 'red' }}-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-{{ $drugTest->result === 'Negative' ? 'check' : 'exclamation-triangle' }} text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-{{ $drugTest->result === 'Negative' ? 'green' : 'red' }}-800">{{ $drugTest->test_name }}</h3>
                                    <p class="text-sm text-{{ $drugTest->result === 'Negative' ? 'green' : 'red' }}-600">{{ $drugTest->test_date }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-bold {{ $drugTest->result === 'Negative' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                {{ $drugTest->result }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Medical History & Physical Findings -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Medical History -->
            @if($examination->illness_history || $examination->past_medical_history || $examination->family_history)
            <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-history text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-indigo-800">Medical History</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    @if($examination->illness_history)
                    <div>
                        <p class="text-sm font-medium text-indigo-600 mb-2">Illness History:</p>
                        <div class="bg-indigo-50 rounded-lg p-3">
                            <p class="text-gray-800">{!! nl2br(e($examination->illness_history)) !!}</p>
                        </div>
                    </div>
                    @endif
                    @if($examination->past_medical_history)
                    <div>
                        <p class="text-sm font-medium text-indigo-600 mb-2">Past Medical History:</p>
                        <div class="bg-indigo-50 rounded-lg p-3">
                            <p class="text-gray-800">{!! nl2br(e($examination->past_medical_history)) !!}</p>
                        </div>
                    </div>
                    @endif
                    @if($examination->family_history)
                    <div>
                        <p class="text-sm font-medium text-indigo-600 mb-2">Family History:</p>
                        <div class="bg-indigo-50 rounded-lg p-3">
                            @if(is_array($examination->family_history))
                                @php
                                    $formattedHistory = array_map(function($item) {
                                        return ucwords(str_replace('_', ' ', $item));
                                    }, $examination->family_history);
                                @endphp
                                <p class="text-gray-800">{{ implode(', ', $formattedHistory) }}</p>
                            @else
                                @php
                                    $formattedHistory = ucwords(str_replace('_', ' ', $examination->family_history));
                                @endphp
                                <p class="text-gray-800">{{ $formattedHistory }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Physical Findings -->
            @php
                $hasPhysicalFindings = false;
                $physicalFindingsDisplay = [];
                
                if ($examination->physical_findings) {
                    if (is_array($examination->physical_findings)) {
                        foreach ($examination->physical_findings as $key => $value) {
                            if (!empty($value) && $value !== 'Normal' && $value !== 'normal') {
                                $hasPhysicalFindings = true;
                                $physicalFindingsDisplay[ucwords(str_replace('_', ' ', $key))] = $value;
                            }
                        }
                    } else {
                        $hasPhysicalFindings = true;
                        $physicalFindingsDisplay['Physical Findings'] = $examination->physical_findings;
                    }
                }
            @endphp
            
            @if($hasPhysicalFindings || $examination->findings)
            <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-teal-50 to-teal-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-stethoscope text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-teal-800">Physical Examination</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    @if($hasPhysicalFindings)
                    <div>
                        <p class="text-sm font-medium text-teal-600 mb-3">Physical Findings:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($physicalFindingsDisplay as $area => $finding)
                            <div class="bg-teal-50 rounded-lg p-4 border border-teal-200">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                        <i class="fas fa-stethoscope text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-teal-800 mb-1">{{ $area }}</h4>
                                        <p class="text-gray-800 text-sm">
                                            @if(is_array($finding))
                                                {{ implode(', ', array_filter($finding, 'is_string')) }}
                                            @else
                                                {{ $finding }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if($examination->findings)
                    <div>
                        <p class="text-sm font-medium text-teal-600 mb-2">General Findings:</p>
                        <div class="bg-teal-50 rounded-lg p-3">
                            @if(is_array($examination->findings))
                                @php
                                    $findingsText = array_filter($examination->findings, function($value) {
                                        return !empty($value) && is_string($value);
                                    });
                                @endphp
                                @if(count($findingsText) > 0)
                                    <p class="text-gray-800">{!! nl2br(e(implode(', ', $findingsText))) !!}</p>
                                @else
                                    <p class="text-gray-500 italic">No specific findings recorded</p>
                                @endif
                            @else
                                <p class="text-gray-800">{!! nl2br(e($examination->findings)) !!}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Doctor's Assessment & Recommendations -->
        @if($examination->lab_findings || $examination->recommendations || $examination->assessment_details)
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-md text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-emerald-800">Doctor's Assessment</h2>
                </div>
            </div>
            <div class="p-8 space-y-6">
                @if($examination->lab_findings)
                <div>
                    <h3 class="text-lg font-bold text-emerald-700 mb-3">Laboratory Findings:</h3>
                    <div class="bg-emerald-50 rounded-xl p-4">
                        @if(is_array($examination->lab_findings))
                            @php
                                $labFindings = array_filter($examination->lab_findings, function($value) {
                                    return !empty($value) && is_string($value);
                                });
                            @endphp
                            @if(count($labFindings) > 0)
                                <p class="text-gray-800 leading-relaxed">{{ implode(', ', $labFindings) }}</p>
                            @else
                                <p class="text-gray-500 italic">No specific laboratory findings recorded</p>
                            @endif
                        @else
                            <p class="text-gray-800 leading-relaxed">{{ $examination->lab_findings }}</p>
                        @endif
                    </div>
                </div>
                @endif
                @if($examination->recommendations)
                <div>
                    <h3 class="text-lg font-bold text-emerald-700 mb-3">Recommendations:</h3>
                    <div class="bg-emerald-50 rounded-xl p-4">
                        <p class="text-gray-800 leading-relaxed">{!! nl2br(e($examination->recommendations)) !!}</p>
                    </div>
                </div>
                @endif
                @if($examination->assessment_details)
                <div>
                    <h3 class="text-lg font-bold text-emerald-700 mb-3">Assessment Details:</h3>
                    <div class="bg-emerald-50 rounded-xl p-4">
                        @php
                            $assessmentData = null;
                            if (is_string($examination->assessment_details)) {
                                $assessmentData = json_decode($examination->assessment_details, true);
                            } elseif (is_array($examination->assessment_details)) {
                                $assessmentData = $examination->assessment_details;
                            }
                        @endphp
                        
                        @if($assessmentData && is_array($assessmentData))
                            <!-- Assessment Summary -->
                            <div class="mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    @if(isset($assessmentData['drug_results']))
                                    <div class="bg-white rounded-lg p-4 border border-emerald-200">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <i class="fas fa-vial text-purple-600"></i>
                                            <h4 class="font-bold text-gray-800">Drug Test</h4>
                                        </div>
                                        <p class="text-2xl font-bold {{ $assessmentData['drug_results']['positive_count'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $assessmentData['drug_results']['positive_count'] }} Positive
                                        </p>
                                    </div>
                                    @endif
                                    
                                    @if(isset($assessmentData['medical_results']))
                                    <div class="bg-white rounded-lg p-4 border border-emerald-200">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <i class="fas fa-flask text-blue-600"></i>
                                            <h4 class="font-bold text-gray-800">Medical Tests</h4>
                                        </div>
                                        <p class="text-2xl font-bold {{ $assessmentData['medical_results']['abnormal_count'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $assessmentData['medical_results']['abnormal_count'] }} Abnormal
                                        </p>
                                    </div>
                                    @endif
                                    
                                    @if(isset($assessmentData['physical_results']))
                                    <div class="bg-white rounded-lg p-4 border border-emerald-200">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <i class="fas fa-stethoscope text-teal-600"></i>
                                            <h4 class="font-bold text-gray-800">Physical Exam</h4>
                                        </div>
                                        <p class="text-2xl font-bold {{ $assessmentData['physical_results']['abnormal_count'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $assessmentData['physical_results']['abnormal_count'] }} Abnormal
                                        </p>
                                    </div>
                                    @endif
                                </div>
                                
                                @if(isset($assessmentData['applied_rule']))
                                <div class="bg-white rounded-lg p-4 border-l-4 {{ strpos($assessmentData['applied_rule'], 'Not Fit') !== false ? 'border-red-500 bg-red-50' : 'border-green-500 bg-green-50' }}">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-gavel {{ strpos($assessmentData['applied_rule'], 'Not Fit') !== false ? 'text-red-600' : 'text-green-600' }}"></i>
                                        <h4 class="font-bold text-gray-800">Applied Rule:</h4>
                                    </div>
                                    <p class="text-lg font-medium {{ strpos($assessmentData['applied_rule'], 'Not Fit') !== false ? 'text-red-800' : 'text-green-800' }} mt-2">
                                        {{ $assessmentData['applied_rule'] }}
                                    </p>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Detailed Results Tables -->
                            @if(isset($assessmentData['physical_results']['abnormal_examinations']) && count($assessmentData['physical_results']['abnormal_examinations']) > 0)
                            <div class="mb-6">
                                <h4 class="text-lg font-bold text-red-700 mb-3">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Abnormal Physical Examinations
                                </h4>
                                <div class="bg-white rounded-lg overflow-hidden border border-red-200">
                                    <table class="w-full">
                                        <thead class="bg-red-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Examination</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Result</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Findings</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-red-100">
                                            @foreach($assessmentData['physical_results']['abnormal_examinations'] as $exam)
                                            <tr class="hover:bg-red-25">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-stethoscope text-red-500 mr-3"></i>
                                                        <span class="text-sm font-medium text-gray-900">{{ $exam['examination'] }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ $exam['result'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-sm text-gray-900">{{ $exam['findings'] }}</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                            
                            @if(isset($assessmentData['medical_results']['abnormal_tests']) && count($assessmentData['medical_results']['abnormal_tests']) > 0)
                            <div class="mb-6">
                                <h4 class="text-lg font-bold text-red-700 mb-3">
                                    <i class="fas fa-flask mr-2"></i>Abnormal Medical Tests
                                </h4>
                                <div class="bg-white rounded-lg overflow-hidden border border-red-200">
                                    <table class="w-full">
                                        <thead class="bg-red-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Test</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Result</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Findings</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-red-100">
                                            @foreach($assessmentData['medical_results']['abnormal_tests'] as $test)
                                            <tr class="hover:bg-red-25">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-flask text-red-500 mr-3"></i>
                                                        <span class="text-sm font-medium text-gray-900">{{ $test['test'] ?? 'N/A' }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ $test['result'] ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-sm text-gray-900">{{ $test['findings'] ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                            
                            @if(isset($assessmentData['calculated_at']))
                            <div class="text-center pt-4 border-t border-emerald-200">
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-clock mr-1"></i>
                                    Assessment calculated on {{ \Carbon\Carbon::parse($assessmentData['calculated_at'])->format('F d, Y \a\t g:i A') }}
                                </p>
                            </div>
                            @endif
                        @else
                            <!-- Fallback for non-JSON data -->
                            <p class="text-gray-800 leading-relaxed">{!! nl2br(e($examination->assessment_details)) !!}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-center text-white shadow-xl">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-hospital text-white text-2xl"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold mb-2">RSS Citi Health Services</h3>
            <p class="text-blue-100 text-lg mb-4">
                This comprehensive annual physical examination was conducted by our certified medical professionals.
            </p>
            <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                <p class="text-white font-medium">
                    <i class="fas fa-phone mr-2"></i>
                    For questions about your results, please contact our medical team
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
