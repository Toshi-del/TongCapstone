@extends('layouts.admin')

@section('title', 'Pre-Employment Medical Results - RSS Citi Health Services')
@section('page-title', 'Pre-Employment Medical Results')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-briefcase text-blue-600"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">Pre-Employment Medical Results</h1>
                            <p class="text-sm text-gray-500">Exam ID: #{{ $examination->id }}</p>
                            @if($examination->date)
                                <p class="text-sm text-gray-500">
                                    Examination Date: {{ \Carbon\Carbon::parse($examination->date)->format('F j, Y \a\t h:i A') }}
                                </p>
                            @endif
                            @if($examination->created_at)
                                <p class="text-sm text-gray-500">
                                    Created: {{ $examination->created_at->format('F j, Y \a\t h:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('admin.tests') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Tests
                    </a>
                </div>
            </div>
        </div>
        
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
                <div class="mb-4">
                    <span class="text-2xl font-semibold {{ $examination->fitness_assessment === 'Fit to Work' ? 'text-green-700' : ($examination->fitness_assessment === 'Not Fit to Work' ? 'text-red-700' : 'text-yellow-700') }}">
                        {{ $examination->fitness_assessment }}
                    </span>
                </div>
                
                @if($examination->assessment_details)
                    @php
                        // Handle both string and JSON string formats
                        if (is_string($examination->assessment_details)) {
                            $details = json_decode($examination->assessment_details, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                $details = $examination->assessment_details; // Keep as string if not valid JSON
                            }
                        } else {
                            $details = $examination->assessment_details;
                        }
                    @endphp
                    
                    @if(is_array($details))
                        <div class="space-y-4">
                            @if(isset($details['applied_rule']))
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <i class="fas fa-gavel text-blue-600 mt-0.5"></i>
                                        <div>
                                            <h4 class="font-medium text-blue-900">Applied Rule</h4>
                                            <p class="text-blue-800 text-sm mt-1">{{ $details['applied_rule'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($details['drug_results']) && is_array($details['drug_results']))
                                @php
                                    // Check if there are any actual drug test results (not empty strings)
                                    $hasDrugResults = false;
                                    foreach($details['drug_results'] as $drug => $result) {
                                        if ($drug !== 'positive_count' && !empty($result)) {
                                            $hasDrugResults = true;
                                            break;
                                        }
                                    }
                                @endphp
                                
                                @if($hasDrugResults)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <i class="fas fa-vial text-gray-600 mt-0.5"></i>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 mb-3">Drug Test Results</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @foreach($details['drug_results'] as $drug => $result)
                                                    @if($drug !== 'positive_count' && !empty($result))
                                                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                                                            <span class="text-sm font-medium text-gray-700">{{ ucfirst($drug) }}</span>
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $result === 'Positive' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $result }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endif
                            
                            @if(isset($details['physical_results']['abnormal_examinations']) && is_array($details['physical_results']['abnormal_examinations']))
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <i class="fas fa-stethoscope text-orange-600 mt-0.5"></i>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-orange-900 mb-3">Physical Examination Abnormalities</h4>
                                            <div class="space-y-2">
                                                @foreach($details['physical_results']['abnormal_examinations'] as $exam)
                                                    <div class="bg-white border border-orange-200 rounded p-3">
                                                        <div class="flex items-start justify-between">
                                                            <div>
                                                                <span class="font-medium text-orange-900">{{ $exam['examination'] }}</span>
                                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                    {{ $exam['result'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        @if(isset($exam['findings']) && $exam['findings'])
                                                            <p class="text-sm text-orange-800 mt-2">{{ $exam['findings'] }}</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-600">{{ $details }}</p>
                    @endif
                @endif
            </div>
        </div>
        @endif
        
        <!-- Applicant Information -->
        @if($examination->preEmploymentRecord)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-tie text-green-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Applicant Information</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                        <div class="text-base font-semibold text-gray-900">{{ $examination->preEmploymentRecord->full_name ?? ($examination->preEmploymentRecord->first_name . ' ' . $examination->preEmploymentRecord->last_name) }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Age</label>
                        <div class="text-base font-semibold text-gray-900">{{ $examination->preEmploymentRecord->age }} years</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Sex</label>
                        <div class="text-base font-semibold text-gray-900">{{ $examination->preEmploymentRecord->sex }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Company</label>
                        <div class="text-base font-semibold text-gray-900">{{ $examination->preEmploymentRecord->company_name }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Medical History -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-history text-purple-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Medical History</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Past Illness/Disease</label>
                        <div class="text-sm text-gray-900 min-h-[60px]">
                            {{ $examination->illness_history ?: 'No past illness recorded' }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Accidents/Operations</label>
                        <div class="text-sm text-gray-900 min-h-[60px]">
                            {{ $examination->accidents_operations ?: 'No accidents or operations recorded' }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Past Medical History</label>
                    <div class="text-sm text-gray-900 min-h-[60px]">
                        {{ $examination->past_medical_history ?: 'No past medical history recorded' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Family History -->
        @if($examination->family_history && is_array($examination->family_history))
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Family History</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($examination->family_history as $condition => $value)
                        @if($value && $value !== 'No' && $value !== '0' && $value !== 0)
                        <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                            <span class="text-sm font-medium text-indigo-900">{{ is_numeric($condition) ? ucwords(str_replace('_', ' ', $value)) : ucwords(str_replace('_', ' ', $condition)) }}</span>
                            @if(!is_numeric($condition) && $value !== '1' && $value !== 1 && $value !== true && strtolower($value) !== 'yes')
                                <span class="text-sm text-indigo-700 font-medium">{{ $value }}</span>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Personal Habits -->
        @if($examination->personal_habits && is_array($examination->personal_habits))
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-smoking text-orange-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Personal Habits</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($examination->personal_habits as $habit => $value)
                        @if($value && $value !== 'No' && $value !== '0' && $value !== 0)
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <span class="text-sm font-medium text-orange-900">{{ is_numeric($habit) ? ucwords(str_replace('_', ' ', $value)) : ucwords(str_replace('_', ' ', $habit)) }}</span>
                            @if(!is_numeric($habit) && $value !== '1' && $value !== 1 && $value !== true && strtolower($value) !== 'yes')
                                <span class="text-sm text-orange-700 font-medium">{{ $value }}</span>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Physical Examination -->
        @if($examination->physical_exam && is_array($examination->physical_exam))
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-stethoscope text-teal-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Physical Examination</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($examination->physical_exam as $exam => $value)
                        @if($value && $value !== 'Not available')
                        <div class="bg-teal-50 rounded-lg p-4 border border-teal-200">
                            <label class="block text-xs font-medium text-teal-700 uppercase tracking-wider mb-2">{{ ucwords(str_replace('_', ' ', $exam)) }}</label>
                            <div class="text-sm font-semibold text-teal-900">{{ $value }}</div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Laboratory Results -->
        @if(($examination->lab_findings && is_array($examination->lab_findings)) || ($examination->lab_report && is_array($examination->lab_report)))
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-flask text-pink-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Laboratory Results</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Radiologist Results (from lab_findings) --}}
                    @if($examination->lab_findings && is_array($examination->lab_findings))
                        @foreach($examination->lab_findings as $test => $results)
                            @if(is_array($results) && (isset($results['result']) || isset($results['finding']) || isset($results['findings'])))
                            <div class="bg-pink-50 rounded-lg p-4 border border-pink-200">
                                <h4 class="text-sm font-semibold text-pink-900 mb-3">{{ ucwords(str_replace('_', ' ', $test)) }}</h4>
                                @if(isset($results['result']))
                                    <div class="mb-2">
                                        <span class="text-xs text-pink-700 font-medium">Result:</span>
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $results['result'] === 'Normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $results['result'] }}
                                        </span>
                                    </div>
                                @endif
                                @if((isset($results['finding']) && $results['finding'] && $results['finding'] !== '—') || (isset($results['findings']) && $results['findings']))
                                    <div>
                                        <span class="text-xs text-pink-700 font-medium">Findings:</span>
                                        <p class="text-sm text-pink-800 mt-1">{{ $results['finding'] ?? $results['findings'] }}</p>
                                    </div>
                                @endif
                                @if(isset($results['reviewed_at']))
                                    <div class="mt-2 pt-2 border-t border-pink-200">
                                        <span class="text-xs text-pink-600">Reviewed: {{ \Carbon\Carbon::parse($results['reviewed_at'])->format('M d, Y \a\t h:i A') }}</span>
                                    </div>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    @endif

                    {{-- Pathologist Results (from lab_report) --}}
                    @if($examination->lab_report && is_array($examination->lab_report))
                        @php
                            $labTestMapping = [
                                'cbc' => 'CBC',
                                'fecalysis' => 'Fecalysis',
                                'stool_exam' => 'Fecalysis',
                                'urinalysis' => 'Urinalysis',
                                'hba1c' => 'HBA1C',
                                'sodium' => 'Sodium',
                                'calcium' => 'Calcium',
                                'fbs' => 'FBS',
                                'bun' => 'BUN',
                                'creatinine' => 'Creatinine'
                            ];
                            
                            // Fields to skip (metadata, not test results)
                            $skipFields = [
                                'collection_completed_at',
                                'blood_extraction_completed',
                                'phlebotomist',
                                'additional_notes'
                            ];
                        @endphp
                        @php
                            // Get all test keys that have results
                            $processedTests = [];
                        @endphp
                        @foreach($examination->lab_report as $key => $value)
                            @if(!in_array($key, $skipFields) && str_ends_with($key, '_result') && $value && $value !== 'Not available' && $value !== '')
                                @php
                                    // Extract test name from key (remove _result suffix)
                                    $testKey = str_replace('_result', '', $key);
                                    $testName = $labTestMapping[$testKey] ?? ucfirst(str_replace('_', ' ', $testKey));
                                    
                                    // Skip if already processed
                                    if (in_array($testKey, $processedTests)) {
                                        continue;
                                    }
                                    $processedTests[] = $testKey;
                                @endphp
                                
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <h4 class="text-sm font-semibold text-blue-900 mb-3">{{ $testName }}</h4>
                                    <div class="mb-2">
                                        <span class="text-xs text-blue-700 font-medium">Result:</span>
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $value === 'Normal' ? 'bg-green-100 text-green-800' : ($value === 'Not normal' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $value }}
                                        </span>
                                    </div>
                                    @if(isset($examination->lab_report[$testKey . '_findings']) && $examination->lab_report[$testKey . '_findings'])
                                        <div>
                                            <span class="text-xs text-blue-700 font-medium">Findings:</span>
                                            <p class="text-sm text-blue-800 mt-1">{{ $examination->lab_report[$testKey . '_findings'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
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
                    <div class="w-8 h-8 bg-cyan-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-search text-cyan-600 text-sm"></i>
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
                                        {{ ucwords(str_replace('_', ' ', $area)) }}
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

        <!-- Drug Test Results -->
        @if($examination->drug_test && is_array($examination->drug_test) && (isset($examination->drug_test['methamphetamine_result']) || isset($examination->drug_test['marijuana_result'])))
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-vial text-yellow-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Drug Test Results</h2>
                </div>
            </div>
            <div class="p-6">
                <!-- Patient Information Table -->
                @if(isset($examination->drug_test['patient_name']) || isset($examination->drug_test['examination_datetime']))
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Patient Information</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-300 rounded-lg">
                            <tbody class="bg-white">
                                @if(isset($examination->drug_test['patient_name']))
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50">Patient name</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $examination->drug_test['patient_name'] }}</td>
                                </tr>
                                @endif
                                @if(isset($examination->drug_test['address']))
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50">Address</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $examination->drug_test['address'] }}</td>
                                </tr>
                                @endif
                                @if(isset($examination->drug_test['age']))
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50">Age</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $examination->drug_test['age'] }}</td>
                                </tr>
                                @endif
                                @if(isset($examination->drug_test['gender']))
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50">Gender</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $examination->drug_test['gender'] }}</td>
                                </tr>
                                @endif
                                @if(isset($examination->drug_test['examination_datetime']))
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50">Examination datetime</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
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
                                    </td>
                                </tr>
                                @endif
                                @if(isset($examination->drug_test['test_method']))
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50">Test method</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $examination->drug_test['test_method'] }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Drug Test Results -->
                <h4 class="text-sm font-semibold text-gray-700 mb-4">Test Results</h4>
                @php
                    // Define which fields are actual drug test results
                    $drugResultFields = [
                        'methamphetamine_result' => ['label' => 'METHAMPHETAMINE (Meth)', 'remarks_field' => 'methamphetamine_remarks'],
                        'marijuana_result' => ['label' => 'TETRAHYDROCANNABINOL (Marijuana)', 'remarks_field' => 'marijuana_remarks']
                    ];
                    
                    // Check if we have any results
                    $hasResults = false;
                    foreach ($drugResultFields as $field => $data) {
                        if (isset($examination->drug_test[$field]) && $examination->drug_test[$field]) {
                            $hasResults = true;
                            break;
                        }
                    }
                @endphp
                
                @if($hasResults)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border-2 border-gray-300 bg-white rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-6 py-4 text-left font-semibold text-gray-900 text-sm">Drug/Metabolites</th>
                                <th class="border border-gray-300 px-6 py-4 text-left font-semibold text-gray-900 text-sm">Result</th>
                                <th class="border border-gray-300 px-6 py-4 text-left font-semibold text-gray-900 text-sm">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($drugResultFields as $field => $data)
                                @if(isset($examination->drug_test[$field]) && $examination->drug_test[$field])
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="border border-gray-300 px-6 py-4 font-medium text-gray-900">{{ $data['label'] }}</td>
                                    <td class="border border-gray-300 px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $examination->drug_test[$field] === 'Negative' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $examination->drug_test[$field] }}
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-6 py-4 text-gray-700">
                                        {{ $examination->drug_test[$data['remarks_field']] ?? '—' }}
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500 text-sm">No drug test results available.</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Additional Tests -->
        @if($examination->visual || $examination->ishihara_test || $examination->ecg)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-eye text-emerald-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Additional Tests</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($examination->visual)
                    <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-200">
                        <label class="block text-sm font-medium text-emerald-700 mb-2">Visual Acuity</label>
                        <div class="text-sm font-semibold text-emerald-900">{{ $examination->visual }}</div>
                    </div>
                    @endif
                    @if($examination->ishihara_test)
                    <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-200">
                        <label class="block text-sm font-medium text-emerald-700 mb-2">Ishihara Test</label>
                        <div class="text-sm font-semibold text-emerald-900">{{ $examination->ishihara_test }}</div>
                    </div>
                    @endif
                    @if($examination->ecg)
                    <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-200">
                        <label class="block text-sm font-medium text-emerald-700 mb-2">ECG</label>
                        <div class="text-sm font-semibold text-emerald-900">{{ $examination->ecg }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Skin Marks -->
        @if($examination->skin_marks)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-hand-paper text-gray-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Skin Marks/Tattoos</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-gray-900 bg-gray-50 rounded-lg p-4">
                    {{ $examination->skin_marks }}
                </div>
            </div>
        </div>
        @endif

        <!-- Doctor's Findings -->
        @if($examination->findings)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-md text-blue-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Doctor's Findings</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-gray-900 bg-blue-50 rounded-lg p-4 border border-blue-200">
                    {{ $examination->findings }}
                </div>
            </div>
        </div>
        @endif

        <!-- Billing Information & Send Results -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-paper-plane text-green-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Send Medical Results</h2>
                </div>
            </div>
            <div class="p-6">
                <!-- Billing Details -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-600 text-sm"></i>
                        </div>
                        <h3 class="text-lg font-medium text-green-800">Review Billing Details</h3>
                    </div>
                    
                    <div id="billingDetails" class="space-y-4">
                        <div class="bg-white rounded-lg p-4 border border-green-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Patient Name</label>
                                    <div id="billingPatientName" class="text-lg font-semibold text-gray-900">Loading...</div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Company</label>
                                    <div id="billingCompanyName" class="text-lg font-semibold text-gray-900">Loading...</div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Medical Test</label>
                                        <div id="billingTestName" class="text-base font-medium text-gray-800">Loading...</div>
                                    </div>
                                    <div class="text-right">
                                        <label class="text-sm font-medium text-gray-600">Total Amount</label>
                                        <div id="billingTotalAmount" class="text-2xl font-bold text-green-600">₱0.00</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" id="billingConfirmed" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                    <label for="billingConfirmed" class="text-sm font-medium text-gray-700">
                                        I confirm the billing information is correct
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-gray-600 text-sm">Medical examination completed and ready for distribution</p>
                        <p class="text-gray-500 text-xs mt-1">Please review and confirm billing details before sending</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button type="button" 
                                id="sendToCompanyBtn"
                                onclick="sendToCompany()"
                                disabled
                                class="w-full inline-flex items-center justify-center px-6 py-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed transition-all duration-200">
                            <i class="fas fa-building mr-3 text-lg"></i>
                            <div class="text-left">
                                <div class="font-semibold">Send to Company</div>
                                <div class="text-xs opacity-90">Send results to hiring company</div>
                            </div>
                        </button>
                        
                        <button type="button" 
                                id="sendToPatientBtn"
                                onclick="sendToPatient()"
                                disabled
                                class="w-full inline-flex items-center justify-center px-6 py-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed transition-all duration-200">
                            <i class="fas fa-user mr-3 text-lg"></i>
                            <div class="text-left">
                                <div class="font-semibold">Send to Patient</div>
                                <div class="text-xs opacity-90">Send results directly to patient</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load billing data when page loads
    loadBillingData();
    
    // Add event listener for billing confirmation checkbox
    const billingCheckbox = document.getElementById('billingConfirmed');
    const sendToCompanyBtn = document.getElementById('sendToCompanyBtn');
    const sendToPatientBtn = document.getElementById('sendToPatientBtn');
    
    if (billingCheckbox) {
        billingCheckbox.addEventListener('change', function() {
            const buttons = [sendToCompanyBtn, sendToPatientBtn];
            
            buttons.forEach(button => {
                if (button) {
                    if (this.checked) {
                        button.disabled = false;
                        button.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        
                        if (button.id === 'sendToCompanyBtn') {
                            button.classList.add('bg-blue-600', 'hover:bg-blue-700', 'focus:outline-none', 'focus:ring-2', 'focus:ring-offset-2', 'focus:ring-blue-500');
                        } else {
                            button.classList.add('bg-green-600', 'hover:bg-green-700', 'focus:outline-none', 'focus:ring-2', 'focus:ring-offset-2', 'focus:ring-green-500');
                        }
                    } else {
                        button.disabled = true;
                        button.classList.add('bg-gray-400', 'cursor-not-allowed');
                        button.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'bg-green-600', 'hover:bg-green-700', 'focus:outline-none', 'focus:ring-2', 'focus:ring-offset-2', 'focus:ring-blue-500', 'focus:ring-green-500');
                    }
                }
            });
        });
    }
});

// Load billing data
async function loadBillingData() {
    try {
        const response = await fetch(`/admin/examinations/pre-employment/{{ $examination->id }}/billing`);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('billingPatientName').textContent = data.patient_name;
            document.getElementById('billingCompanyName').textContent = data.company_name;
            document.getElementById('billingTestName').textContent = data.test_name;
            document.getElementById('billingTotalAmount').textContent = `₱${parseFloat(data.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        } else {
            showBillingError(data.message || 'Failed to load billing data');
        }
    } catch (error) {
        console.error('Error loading billing data:', error);
        showBillingError('Failed to load billing data');
    }
}

// Show billing error
function showBillingError(message) {
    document.getElementById('billingPatientName').textContent = 'Error loading data';
    document.getElementById('billingCompanyName').textContent = message;
    document.getElementById('billingTestName').textContent = 'N/A';
    document.getElementById('billingTotalAmount').textContent = '₱0.00';
}

// Send to company
async function sendToCompany() {
    const billingCheckbox = document.getElementById('billingConfirmed');
    
    if (!billingCheckbox.checked) {
        alert('Please confirm the billing information is correct before sending.');
        return;
    }
    
    try {
        const response = await fetch(`/admin/examinations/pre-employment/{{ $examination->id }}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                send_to: 'company'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccessMessage('Pre-employment examination sent to company successfully!', 'The medical examination results have been successfully transmitted to the company for review.');
            setTimeout(() => {
                window.location.href = '{{ route("admin.tests") }}';
            }, 2000);
        } else {
            showErrorMessage('Failed to send examination', data.message || 'An error occurred while sending the examination to the company.');
        }
    } catch (error) {
        console.error('Error sending examination:', error);
        showErrorMessage('Network Error', 'Failed to send examination to company. Please check your connection and try again.');
    }
}

// Send to patient
async function sendToPatient() {
    const billingCheckbox = document.getElementById('billingConfirmed');
    
    if (!billingCheckbox.checked) {
        alert('Please confirm the billing information is correct before sending.');
        return;
    }
    
    try {
        const response = await fetch(`/admin/examinations/pre-employment/{{ $examination->id }}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                send_to: 'patient'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccessMessage('Pre-employment examination sent to patient successfully!', 'The medical examination results have been successfully sent to the patient via email.');
            setTimeout(() => {
                window.location.href = '{{ route("admin.tests") }}';
            }, 2000);
        } else {
            showErrorMessage('Failed to send examination', data.message || 'An error occurred while sending the examination to the patient.');
        }
    } catch (error) {
        console.error('Error sending examination:', error);
        showErrorMessage('Network Error', 'Failed to send examination to patient. Please check your connection and try again.');
    }
}

// Show success message
function showSuccessMessage(title, message) {
    // Create success notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 z-50 bg-white border-l-4 border-green-500 rounded-lg shadow-xl max-w-md transform transition-all duration-300 translate-x-full';
    notification.innerHTML = `
        <div class="p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-green-800">${title}</h3>
                    <p class="text-green-700 text-sm mt-1">${message}</p>
                    <div class="mt-3 flex items-center space-x-2">
                        <div class="w-full bg-green-200 rounded-full h-1">
                            <div class="bg-green-500 h-1 rounded-full transition-all duration-100" style="width: 100%" id="successProgress"></div>
                        </div>
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-600 ml-4">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Progress bar animation
    const progressBar = notification.querySelector('#successProgress');
    let width = 100;
    const interval = setInterval(() => {
        width -= 5;
        progressBar.style.width = width + '%';
        
        if (width <= 0) {
            clearInterval(interval);
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 100);
}

// Show error message
function showErrorMessage(title, message) {
    // Create error notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 z-50 bg-white border-l-4 border-red-500 rounded-lg shadow-xl max-w-md transform transition-all duration-300 translate-x-full';
    notification.innerHTML = `
        <div class="p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-red-800">${title}</h3>
                    <p class="text-red-700 text-sm mt-1">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 ml-4">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}
</script>
    </div>
</div>
@endsection
