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

        <!-- Medical Examination Details -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Medical Examination Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($examination->height)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Height</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->height }} cm</p>
                    </div>
                    @endif
                    
                    @if($examination->weight)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Weight</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->weight }} kg</p>
                    </div>
                    @endif
                    
                    @if($examination->blood_pressure)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Blood Pressure</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->blood_pressure }} mmHg</p>
                    </div>
                    @endif
                    
                    @if($examination->pulse_rate)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Pulse Rate</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->pulse_rate }} bpm</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Laboratory Results -->
        @if($examination->lab_findings)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Laboratory Results</h2>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    {!! nl2br(e($examination->lab_findings)) !!}
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
