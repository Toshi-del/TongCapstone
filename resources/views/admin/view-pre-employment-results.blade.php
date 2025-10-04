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
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <i class="fas fa-vial text-gray-600 mt-0.5"></i>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 mb-3">Drug Test Results</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @foreach($details['drug_results'] as $drug => $result)
                                                    @if($drug !== 'positive_count')
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
                        @if($value && $value !== 'No')
                        <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                            <span class="text-sm font-medium text-indigo-900">{{ ucfirst(str_replace('_', ' ', $condition)) }}</span>
                            <span class="text-sm text-indigo-700 font-medium">{{ $value }}</span>
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
                        @if($value && $value !== 'No')
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <span class="text-sm font-medium text-orange-900">{{ ucfirst(str_replace('_', ' ', $habit)) }}</span>
                            <span class="text-sm text-orange-700 font-medium">{{ $value }}</span>
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
                            <label class="block text-xs font-medium text-teal-700 uppercase tracking-wider mb-2">{{ ucfirst(str_replace('_', ' ', $exam)) }}</label>
                            <div class="text-sm font-semibold text-teal-900">{{ $value }}</div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Physical Findings -->
        @if($examination->physical_findings && is_array($examination->physical_findings))
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-cyan-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-search text-cyan-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Physical Findings</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($examination->physical_findings as $area => $findings)
                        @if(is_array($findings) && (isset($findings['result']) || isset($findings['findings'])))
                        <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-200">
                            <h4 class="text-sm font-semibold text-cyan-900 mb-3">{{ ucfirst(str_replace('_', ' ', $area)) }}</h4>
                            @if(isset($findings['result']))
                                <div class="mb-2">
                                    <span class="text-xs text-cyan-700 font-medium">Result:</span>
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $findings['result'] === 'Normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $findings['result'] }}
                                    </span>
                                </div>
                            @endif
                            @if(isset($findings['findings']) && $findings['findings'])
                                <div>
                                    <span class="text-xs text-cyan-700 font-medium">Findings:</span>
                                    <p class="text-sm text-cyan-800 mt-1">{{ $findings['findings'] }}</p>
                                </div>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Laboratory Results -->
        @if($examination->lab_findings && is_array($examination->lab_findings))
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
                    @foreach($examination->lab_findings as $test => $results)
                        @if(is_array($results) && (isset($results['result']) || isset($results['findings'])))
                        <div class="bg-pink-50 rounded-lg p-4 border border-pink-200">
                            <h4 class="text-sm font-semibold text-pink-900 mb-3">{{ ucfirst(str_replace('_', ' ', $test)) }}</h4>
                            @if(isset($results['result']))
                                <div class="mb-2">
                                    <span class="text-xs text-pink-700 font-medium">Result:</span>
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $results['result'] === 'Normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $results['result'] }}
                                    </span>
                                </div>
                            @endif
                            @if(isset($results['findings']) && $results['findings'])
                                <div>
                                    <span class="text-xs text-pink-700 font-medium">Findings:</span>
                                    <p class="text-sm text-pink-800 mt-1">{{ $results['findings'] }}</p>
                                </div>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Drug Test Results -->
        @if($examination->drug_test && is_array($examination->drug_test))
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($examination->drug_test as $drug => $result)
                        @if($result)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <span class="text-sm font-medium text-yellow-900">{{ ucfirst(str_replace('_', ' ', $drug)) }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $result === 'Negative' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $result }}
                            </span>
                        </div>
                        @endif
                    @endforeach
                </div>
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

        <!-- Billing Information & Send to Company -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-receipt text-green-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Billing Information & Send to Company</h2>
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
                
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Medical examination completed and ready for company review</p>
                        <p class="text-gray-500 text-xs mt-1">Please review and confirm billing details before sending</p>
                    </div>
                    <button type="button" 
                            id="sendToCompanyBtn"
                            onclick="sendToCompany()"
                            disabled
                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send to Company
                    </button>
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
    const sendButton = document.getElementById('sendToCompanyBtn');
    
    if (billingCheckbox && sendButton) {
        billingCheckbox.addEventListener('change', function() {
            if (this.checked) {
                sendButton.disabled = false;
                sendButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                sendButton.classList.add('bg-green-600', 'hover:bg-green-700', 'focus:outline-none', 'focus:ring-2', 'focus:ring-offset-2', 'focus:ring-green-500');
            } else {
                sendButton.disabled = true;
                sendButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                sendButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'focus:outline-none', 'focus:ring-2', 'focus:ring-offset-2', 'focus:ring-green-500');
            }
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
            }
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
