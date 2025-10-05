@extends('layouts.patient')

@section('title', 'My Medical Results')

@section('content')
<div class="min-h-screen bg-gray-50" style="font-family: 'Poppins', sans-serif;">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">
                            <i class="fas fa-file-medical mr-3"></i>My Medical Results
                        </h1>
                        <p class="text-blue-100">View your medical examination results sent by RSS Citi Health Services</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-500 rounded-lg px-4 py-2">
                            <p class="text-blue-100 text-sm font-medium">Total Results</p>
                            <p class="text-white text-xl font-bold">{{ $preEmploymentResults->count() + $annualPhysicalResults->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pre-Employment Results -->
        @if($preEmploymentResults->count() > 0)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-green-600 to-green-700 border-l-4 border-green-800">
                <h2 class="text-xl font-bold text-white" style="font-family: 'Poppins', sans-serif;">
                    <i class="fas fa-briefcase mr-3"></i>Pre-Employment Medical Results
                </h2>
                <p class="text-green-100 mt-1">Medical examinations for employment purposes</p>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($preEmploymentResults as $exam)
                    <div class="bg-green-50 rounded-xl p-6 border-l-4 border-green-600 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-lg">
                                        {{ strtoupper(substr($exam->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-green-900">{{ $exam->name }}</h3>
                                    @if($exam->company_name)
                                        <p class="text-green-700 text-sm">{{ $exam->company_name }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($exam->fitness_assessment)
                                @if($exam->fitness_assessment === 'Fit to Work')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-600 text-white">
                                        <i class="fas fa-check mr-1"></i>
                                        Fit to Work
                                    </span>
                                @elseif($exam->fitness_assessment === 'Not Fit to Work')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-600 text-white">
                                        <i class="fas fa-times mr-1"></i>
                                        Not Fit to Work
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-600 text-white">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $exam->fitness_assessment }}
                                    </span>
                                @endif
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Examination Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($exam->created_at)->format('M d, Y') }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Received Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $exam->updated_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-600">{{ $exam->updated_at->format('g:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-eye mr-1"></i>
                                Available to View
                            </span>
                            <a href="{{ route('patient.view-pre-employment-result', $exam->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-file-alt mr-2"></i>View Details
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Annual Physical Results -->
        @if($annualPhysicalResults->count() > 0)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-purple-600 to-purple-700 border-l-4 border-purple-800">
                <h2 class="text-xl font-bold text-white" style="font-family: 'Poppins', sans-serif;">
                    <i class="fas fa-heartbeat mr-3"></i>Annual Physical Medical Results
                </h2>
                <p class="text-purple-100 mt-1">Yearly health checkups and medical assessments</p>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($annualPhysicalResults as $exam)
                    <div class="bg-purple-50 rounded-xl p-6 border-l-4 border-purple-600 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-lg">
                                        {{ strtoupper(substr($exam->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-purple-900">{{ $exam->name }}</h3>
                                    <p class="text-purple-700 text-sm">Annual Physical Examination</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-600 text-white">
                                <i class="fas fa-stethoscope mr-1"></i>
                                Annual Physical
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Examination Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($exam->created_at)->format('M d, Y') }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Received Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $exam->updated_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-600">{{ $exam->updated_at->format('g:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-eye mr-1"></i>
                                Available to View
                            </span>
                            <a href="{{ route('patient.view-annual-physical-result', $exam->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-file-alt mr-2"></i>View Details
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Empty State -->
        @if($preEmploymentResults->count() === 0 && $annualPhysicalResults->count() === 0)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-12 text-center">
                <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-file-medical text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Medical Results Available</h3>
                <p class="text-gray-600 mb-4">You don't have any medical examination results yet.</p>
                <div class="bg-blue-50 rounded-lg p-4 max-w-md mx-auto">
                    <p class="text-blue-800 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        Medical results will appear here once they are sent to you by RSS Citi Health Services.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
