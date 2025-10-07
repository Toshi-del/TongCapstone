@extends('layouts.doctor')

@section('title', 'Annual Physical Examinations - RSS Citi Health Services')
@section('page-title', 'Annual Physical Examinations')
@section('page-description', 'Manage and monitor annual physical examination patients')

@section('content')
<div class="space-y-8">
    
    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-8 py-4 bg-green-600">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <span class="text-white font-medium">{{ session('success') }}</span>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="px-8 py-6 bg-purple-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center border border-white border-opacity-30">
                        <i class="fas fa-file-medical text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-1">Annual Physical Examinations</h1>
                        <p class="text-purple-100 text-sm">Comprehensive health examinations and patient management</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl px-6 py-4 border border-white border-opacity-30">
                    <p class="text-purple-100 text-sm font-medium">Total Patients</p>
                    <p class="text-white text-2xl font-bold">{{ $patients->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Patients Section -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="px-8 py-6 bg-purple-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center border border-white border-opacity-30">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Patient Management</h2>
                        <p class="text-purple-100 text-sm">Annual physical examination patients and their status</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 border border-white border-opacity-30">
                    <p class="text-purple-100 text-xs font-medium">Active Patients</p>
                    <p class="text-white text-lg font-bold">{{ $patients->count() }}</p>
                </div>
            </div>
        </div>
        
        @if($patients->count() > 0)
        <div class="p-8">
            <div class="space-y-6">
                @foreach($patients as $patient)
                @php $canSend = $canSendByPatientId[$patient->id] ?? false; @endphp
                <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-200">
                    <!-- Patient Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            @php
                                $initials = strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1));
                                $colors = ['bg-purple-500', 'bg-blue-500', 'bg-indigo-500', 'bg-pink-500', 'bg-red-500'];
                                $colorIndex = crc32($patient->id) % count($colors);
                            @endphp
                            <div class="w-14 h-14 {{ $colors[$colorIndex] }} rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold text-lg">{{ $initials }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</h3>
                                <p class="text-gray-500 text-sm">{{ $patient->age }} years, {{ $patient->sex }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-file-medical mr-1"></i>Annual Physical
                            </span>
                            @if($canSend)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Ready to Submit
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>In Progress
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Medical Information -->
                    @php
                        $appointment = $patient->appointment;
                        $medicalTests = collect();
                        
                        // Get primary medical test from appointment
                        if ($appointment && $appointment->medicalTest) {
                            $medicalTests->push($appointment->medicalTest);
                        }
                        
                        // Get all additional medical tests from patient's medicalTests relationship
                        if ($patient->medicalTests && $patient->medicalTests->count() > 0) {
                            foreach ($patient->medicalTests as $test) {
                                // Avoid duplicates
                                if (!$medicalTests->contains('id', $test->id)) {
                                    $medicalTests->push($test);
                                }
                            }
                        }
                    @endphp
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-4 mb-6 border border-purple-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-stethoscope text-purple-600"></i>
                                </div>
                                <span class="text-sm font-bold text-purple-900">Medical Test Information</span>
                            </div>
                            @if($medicalTests->count() > 0)
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                                    {{ $medicalTests->count() }} {{ $medicalTests->count() === 1 ? 'Test' : 'Tests' }}
                                </span>
                            @endif
                        </div>
                        @if($medicalTests->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($medicalTests as $index => $medicalTest)
                                    <div class="bg-white rounded-lg p-3 border border-purple-100 hover:shadow-md transition-shadow">
                                        <div class="flex items-start mb-2">
                                            <div class="flex-shrink-0 w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                                                <span class="text-purple-700 font-bold text-xs">{{ $index + 1 }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-gray-900 truncate" title="{{ $medicalTest->name }}">
                                                    {{ $medicalTest->name }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($medicalTest->medicalTestCategory)
                                            <div class="flex items-center text-xs text-gray-600 mb-1">
                                                <i class="fas fa-folder text-purple-500 mr-1.5 text-xs"></i>
                                                <span class="truncate">{{ $medicalTest->medicalTestCategory->name }}</span>
                                            </div>
                                        @endif
                                        @if($medicalTest->description)
                                            <div class="flex items-start text-xs text-gray-500">
                                                <i class="fas fa-info-circle text-purple-500 mr-1.5 text-xs mt-0.5"></i>
                                                <span class="line-clamp-2">{{ $medicalTest->description }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center justify-center py-3">
                                <i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>
                                <p class="text-sm text-gray-600">No medical test assigned</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between space-x-3 mb-4">
                        <!-- Send to Admin -->
                        <form action="{{ route('doctor.annual-physical.by-patient.submit', $patient->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center {{ $canSend ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}" 
                                    title="Send to Admin" 
                                    {{ $canSend ? '' : 'disabled' }}>
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit to Admin
                            </button>
                        </form>
                        
                        <!-- Update Results -->
                        <a href="{{ route('doctor.annual-physical.by-patient.edit', $patient->id) }}" 
                           class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-200 text-sm font-medium flex items-center justify-center" 
                           title="Update Results">
                            <i class="fas fa-edit mr-2"></i>
                            Update Results
                        </a>
                        
                        <!-- Medical Checklist -->
                        <a href="{{ route('doctor.medical-checklist.annual-physical', $patient->id) }}" 
                           class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 text-sm font-medium flex items-center justify-center" 
                           title="Medical Checklist">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            Checklist
                        </a>
                    </div>
                    
                    <!-- Patient Status Footer -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                <span>Last Updated: {{ $patient->updated_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($canSend)
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-green-600 font-medium text-sm">Complete</span>
                                @else
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                    <span class="text-yellow-600 font-medium text-sm">In Progress</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination if needed -->
            @if(method_exists($patients, 'links'))
            <div class="mt-8">
                {{ $patients->links() }}
            </div>
            @endif
        </div>
        @else
        <!-- Empty State -->
        <div class="p-16 text-center">
            <div class="w-24 h-24 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-file-medical text-purple-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Annual Physical Patients</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">No annual physical examination patients found. Patients will appear here when they schedule appointments.</p>
            <div class="flex justify-center">
                <a href="{{ route('doctor.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 