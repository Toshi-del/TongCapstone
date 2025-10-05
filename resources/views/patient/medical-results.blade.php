@extends('layouts.patient')

@section('title', 'My Medical Results')

@section('content')
<!-- Remove the wrapper div to use full width from layout -->
<div class="space-y-8">
    <!-- Header Section with Enhanced Design -->
    <div class="content-card patient-gradient rounded-2xl p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-medical text-white text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2 text-white">My Medical Results</h1>
                    <p class="text-white/90 text-lg">View and manage your medical examination results</p>
                    <div class="mt-3 flex items-center space-x-4">
                        <span class="bg-white/20 text-white text-sm px-3 py-1 rounded-full border border-white/30">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Secure & Confidential
                        </span>
                        <span class="bg-white/20 text-white text-sm px-3 py-1 rounded-full border border-white/30">
                            <i class="fas fa-clock mr-1"></i>
                            Real-time Updates
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right bg-white/10 rounded-2xl p-6">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <p class="text-3xl font-bold text-white">{{ $preEmploymentResults->count() }}</p>
                        <p class="text-white/80 text-sm">Pre-Employment</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">{{ $annualPhysicalResults->count() }}</p>
                        <p class="text-white/80 text-sm">Annual Physical</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-xl font-bold text-white">{{ $preEmploymentResults->count() + $annualPhysicalResults->count() }}</p>
                    <p class="text-white/80 text-sm">Total Results</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-search text-blue-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Search Results</h3>
                    <p class="text-sm text-gray-600">Find specific records</p>
                </div>
            </div>
        </div>
        <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-download text-green-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Download All</h3>
                    <p class="text-sm text-gray-600">Export as PDF</p>
                </div>
            </div>
        </div>
        <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-share text-purple-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Share Results</h3>
                    <p class="text-sm text-gray-600">Send to doctor</p>
                </div>
            </div>
        </div>
        <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-plus text-orange-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Schedule Follow-up</h3>
                    <p class="text-sm text-gray-600">Book appointment</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pre-Employment Results -->
    @if($preEmploymentResults->count() > 0)
    <div class="content-card rounded-2xl overflow-hidden">
        <div class="px-8 py-6 bg-gradient-to-r from-emerald-500 to-emerald-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-briefcase text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Pre-Employment Medical Results</h2>
                        <p class="text-emerald-100 mt-1">Medical examinations for employment purposes</p>
                    </div>
                </div>
                <div class="bg-white/20 rounded-xl px-4 py-2">
                    <p class="text-white font-bold text-lg">{{ $preEmploymentResults->count() }} Results</p>
                </div>
            </div>
        </div>
        
        <div class="p-8">
            <div class="grid grid-cols-1 xl:grid-cols-3 lg:grid-cols-2 gap-6">
                @foreach($preEmploymentResults as $exam)
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-6 border border-emerald-200 hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                    <!-- Header with Status -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-lg">
                                    {{ strtoupper(substr($exam->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-emerald-900 group-hover:text-emerald-700 transition-colors">
                                    {{ $exam->name }}
                                </h3>
                                @if($exam->company_name)
                                    <p class="text-emerald-700 text-sm font-medium">{{ $exam->company_name }}</p>
                                @endif
                            </div>
                        </div>
                        @if($exam->fitness_assessment)
                            @if($exam->fitness_assessment === 'Fit to Work')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-md">
                                    <i class="fas fa-check mr-1.5"></i>
                                    Fit to Work
                                </span>
                            @elseif($exam->fitness_assessment === 'Not Fit to Work')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-500 text-white shadow-md">
                                    <i class="fas fa-times mr-1.5"></i>
                                    Not Fit to Work
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500 text-white shadow-md">
                                    <i class="fas fa-clock mr-1.5"></i>
                                    {{ $exam->fitness_assessment }}
                                </span>
                            @endif
                        @endif
                    </div>
                    
                    <!-- Date Information -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-white/50">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-calendar text-emerald-500"></i>
                                <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Examination</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($exam->created_at)->format('M d, Y') }}</p>
                        </div>
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-white/50">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-clock text-emerald-500"></i>
                                <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Received</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">{{ $exam->updated_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-600">{{ $exam->updated_at->format('g:i A') }}</p>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-emerald-200">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-eye mr-2"></i>
                            Available
                        </span>
                        <div class="flex space-x-2">
                            <button class="p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors">
                                <i class="fas fa-download"></i>
                            </button>
                            <a href="{{ route('patient.view-pre-employment-result', $exam->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-lg">
                                <i class="fas fa-file-alt mr-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Annual Physical Results -->
    @if($annualPhysicalResults->count() > 0)
    <div class="content-card rounded-2xl overflow-hidden">
        <div class="px-8 py-6 bg-gradient-to-r from-violet-500 to-purple-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heartbeat text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Annual Physical Medical Results</h2>
                        <p class="text-violet-100 mt-1">Yearly health checkups and medical assessments</p>
                    </div>
                </div>
                <div class="bg-white/20 rounded-xl px-4 py-2">
                    <p class="text-white font-bold text-lg">{{ $annualPhysicalResults->count() }} Results</p>
                </div>
            </div>
        </div>
        
        <div class="p-8">
            <div class="grid grid-cols-1 xl:grid-cols-3 lg:grid-cols-2 gap-6">
                @foreach($annualPhysicalResults as $exam)
                <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-2xl p-6 border border-violet-200 hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                    <!-- Header with Badge -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-14 h-14 bg-violet-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-lg">
                                    {{ strtoupper(substr($exam->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-violet-900 group-hover:text-violet-700 transition-colors">
                                    {{ $exam->name }}
                                </h3>
                                <p class="text-violet-700 text-sm font-medium">Annual Physical Examination</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-violet-500 text-white shadow-md">
                            <i class="fas fa-stethoscope mr-1.5"></i>
                            Annual Physical
                        </span>
                    </div>
                    
                    <!-- Date Information -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-white/50">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-calendar text-violet-500"></i>
                                <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Examination</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($exam->created_at)->format('M d, Y') }}</p>
                        </div>
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-white/50">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-clock text-violet-500"></i>
                                <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Received</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">{{ $exam->updated_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-600">{{ $exam->updated_at->format('g:i A') }}</p>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-violet-200">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-eye mr-2"></i>
                            Available
                        </span>
                        <div class="flex space-x-2">
                            <button class="p-2 text-violet-600 hover:bg-violet-100 rounded-lg transition-colors">
                                <i class="fas fa-download"></i>
                            </button>
                            <a href="{{ route('patient.view-annual-physical-result', $exam->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-violet-500 hover:bg-violet-600 text-white rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-lg">
                                <i class="fas fa-file-alt mr-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Enhanced Empty State -->
    @if($preEmploymentResults->count() === 0 && $annualPhysicalResults->count() === 0)
    <div class="content-card rounded-2xl overflow-hidden">
        <div class="p-16 text-center">
            <div class="relative mb-8">
                <div class="w-32 h-32 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto shadow-lg">
                    <i class="fas fa-file-medical text-blue-500 text-5xl"></i>
                </div>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                    <i class="fas fa-plus text-white text-sm"></i>
                </div>
            </div>
            
            <h3 class="text-3xl font-bold text-gray-900 mb-4">No Medical Results Available</h3>
            <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
                You don't have any medical examination results yet. Results will appear here once they are sent by RSS Citi Health Services.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-briefcase text-white"></i>
                    </div>
                    <h4 class="font-bold text-blue-900 mb-2">Pre-Employment Results</h4>
                    <p class="text-blue-700 text-sm">Medical examinations for employment purposes will appear here</p>
                </div>
                
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-200">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heartbeat text-white"></i>
                    </div>
                    <h4 class="font-bold text-purple-900 mb-2">Annual Physical Results</h4>
                    <p class="text-purple-700 text-sm">Yearly health checkup results will be displayed here</p>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200 max-w-lg mx-auto">
                <div class="flex items-center justify-center space-x-3 mb-3">
                    <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                    <h5 class="font-bold text-blue-900">What happens next?</h5>
                </div>
                <p class="text-blue-800 text-sm leading-relaxed">
                    Once your medical examinations are completed and reviewed by our medical team, 
                    the results will be automatically sent to your patient portal and you'll receive an email notification.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
