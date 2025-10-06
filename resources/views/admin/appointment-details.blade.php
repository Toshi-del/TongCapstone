@extends('layouts.admin')

@section('title', 'Appointment Details - RSS Citi Health Services')
@section('page-title', 'Appointment Details')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Appointment Details</h3>
                        <p class="text-emerald-100 text-sm">Appointment ID: #{{ $appointment->id }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.appointments') }}" 
                       class="bg-white/10 hover:bg-white/20 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 border border-white/20 backdrop-blur-sm">
                        <i class="fas fa-arrow-left text-sm"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Appointment Information -->
            <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar text-emerald-600 text-lg"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Appointment Information</h4>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-calendar text-gray-400"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Appointment Date</p>
                                    <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F d, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-clock text-gray-400"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Time Slot</p>
                                    <p class="font-medium text-gray-900">{{ $appointment->time_slot ?? 'To be determined' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-users text-gray-400"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Number of Patients</p>
                                    <p class="font-medium text-gray-900">{{ $appointment->patient_count }} {{ $appointment->patient_count == 1 ? 'patient' : 'patients' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-building text-gray-400"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Company</p>
                                    <p class="font-medium text-gray-900">{{ $appointment->creator->company ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-envelope text-gray-400"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Company Email</p>
                                    <p class="font-medium text-gray-900">{{ $appointment->creator->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-calendar-plus text-gray-400"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Request Date</p>
                                    <p class="font-medium text-gray-900">{{ $appointment->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Examination Details -->
            <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
                <div class="bg-gradient-to-r from-amber-50 to-amber-100 px-6 py-4 border-b border-amber-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-stethoscope text-amber-600 text-lg"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Medical Examination</h4>
                    </div>
                </div>
                <div class="p-6">
                    @if($appointment->medicalTestCategory && $appointment->medicalTest)
                        <!-- Single test (legacy) -->
                        <div class="space-y-4">
                            <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h5 class="font-semibold text-amber-800">{{ $appointment->medicalTestCategory->name }}</h5>
                                        <p class="text-sm text-amber-600 mt-1">{{ $appointment->medicalTest->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center space-x-2 text-green-700">
                                            <i class="fas fa-peso-sign text-sm"></i>
                                            <span class="font-semibold">{{ number_format((float)($appointment->medicalTest->price ?? 0), 2) }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">per patient</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-green-800">Total Cost:</span>
                                    <div class="text-right">
                                        <div class="flex items-center space-x-2 text-green-700">
                                            <i class="fas fa-peso-sign"></i>
                                            <span class="text-lg font-bold">{{ number_format((float)($appointment->medicalTest->price ?? 0) * $appointment->patient_count, 2) }}</span>
                                        </div>
                                        <p class="text-xs text-green-600">{{ $appointment->patient_count }} × ₱{{ number_format((float)($appointment->medicalTest->price ?? 0), 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($appointment->selected_categories->count() > 0)
                        <!-- Multiple tests (new system) -->
                        <div class="space-y-4">
                            @php
                                // Group tests by their categories
                                $testsByCategory = [];
                                foreach($appointment->selected_tests as $test) {
                                    $categoryId = $test->medical_test_category_id;
                                    if (!isset($testsByCategory[$categoryId])) {
                                        $testsByCategory[$categoryId] = [];
                                    }
                                    $testsByCategory[$categoryId][] = $test;
                                }
                            @endphp
                            
                            @foreach($appointment->selected_categories as $category)
                                @php
                                    $categoryTests = $testsByCategory[$category->id] ?? [];
                                    $categoryTotalPrice = collect($categoryTests)->sum('price');
                                @endphp
                                <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h5 class="font-semibold text-amber-800 flex items-center space-x-2">
                                                <i class="fas fa-clipboard-list text-amber-600 text-sm"></i>
                                                <span>{{ $category->name }}</span>
                                            </h5>
                                            <p class="text-xs text-amber-600 mt-1">{{ count($categoryTests) }} {{ count($categoryTests) == 1 ? 'test' : 'tests' }} selected</p>
                                        </div>
                                        @if($categoryTotalPrice > 0)
                                            <div class="text-right">
                                                <div class="flex items-center space-x-2 text-green-700">
                                                    <i class="fas fa-peso-sign text-sm"></i>
                                                    <span class="font-semibold">{{ number_format($categoryTotalPrice, 2) }}</span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">per patient</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if(count($categoryTests) > 0)
                                        <div class="space-y-2 ml-6">
                                            @foreach($categoryTests as $test)
                                                <div class="flex items-center justify-between bg-white p-2 rounded border border-amber-100">
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-stethoscope text-amber-500 text-xs"></i>
                                                        <span class="text-sm text-amber-700">{{ $test->name }}</span>
                                                    </div>
                                                    @if($test->price)
                                                        <span class="text-xs text-green-600 font-medium">₱{{ number_format((float)$test->price, 2) }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            
                            <!-- Estimated Billing -->
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-green-800">Estimated Total Cost:</span>
                                    <div class="text-right">
                                        <div class="flex items-center space-x-2 text-green-700">
                                            <i class="fas fa-peso-sign"></i>
                                            <span class="text-lg font-bold">{{ number_format((float)($appointment->total_price ?? 0) * $appointment->patient_count, 2) }}</span>
                                        </div>
                                        <p class="text-xs text-green-600">
                                            {{ $appointment->patient_count }} {{ $appointment->patient_count == 1 ? 'patient' : 'patients' }} × ₱{{ number_format((float)($appointment->total_price ?? 0), 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- No tests specified -->
                        <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                            <div class="text-center py-4">
                                <i class="fas fa-stethoscope text-amber-400 text-3xl mb-2"></i>
                                <p class="text-amber-700 font-medium">No medical test specified</p>
                                <p class="text-amber-600 text-sm">Examination type: {{ $appointment->appointment_type ?? 'General Checkup' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Patient List -->
            @if($appointment->patients && $appointment->patients->count() > 0)
                <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-lg"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900">Patient List</h4>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($appointment->patients as $patient)
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">
                                                {{ substr($patient->first_name ?? 'N', 0, 1) }}{{ substr($patient->last_name ?? 'A', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-blue-800">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                            <p class="text-sm text-blue-600">{{ $patient->email ?? 'No email' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Test Assignments -->
            @if($appointment->status === 'approved' && $appointment->hasTestAssignments())
                <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tasks text-purple-600 text-lg"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900">Test Assignments</h4>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $assignmentsByRole = $appointment->getTestAssignmentsByStaffRole();
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($assignmentsByRole as $role => $assignments)
                                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-semibold text-purple-800">{{ ucfirst(str_replace('_', ' ', $role)) }}</h5>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ count($assignments) }} {{ count($assignments) == 1 ? 'assignment' : 'assignments' }}
                                        </span>
                                    </div>
                                    <div class="space-y-1">
                                        @foreach($assignments as $assignment)
                                            <p class="text-sm text-purple-600">• {{ $assignment->description ?? 'Test assignment' }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-purple-200">
                            <a href="{{ route('admin.test-assignments.show', $appointment->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-all duration-150">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                View All Assignments
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Status & Actions -->
        <div class="space-y-8">
            <!-- Status Card -->
            <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info-circle text-gray-600 text-lg"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Status Information</h4>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Appointment Status</p>
                        @if($appointment->status === 'approved')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                <i class="fas fa-check-circle mr-2"></i>
                                Approved
                            </span>
                        @elseif($appointment->status === 'declined')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                <i class="fas fa-times-circle mr-2"></i>
                                Declined
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                <i class="fas fa-clock mr-2"></i>
                                Pending
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
                <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 px-6 py-4 border-b border-emerald-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cogs text-emerald-600 text-lg"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Actions</h4>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                    @if($appointment->status !== 'approved')
                        <button type="button" 
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg font-medium transition-all duration-150 border border-green-200"
                                onclick="openApproveModal({{ $appointment->id }})">
                            <i class="fas fa-check mr-2"></i>
                            Approve Appointment
                        </button>
                    @endif
                    
                    @if($appointment->status !== 'declined')
                        <button type="button" 
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition-all duration-150 border border-red-200"
                                onclick="openDeclineModal({{ $appointment->id }})">
                            <i class="fas fa-times mr-2"></i>
                            Decline Appointment
                        </button>
                    @endif
                    
                    @if($appointment->status === 'approved' && $appointment->hasTestAssignments())
                        <a href="{{ route('admin.test-assignments.show', $appointment->id) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-3 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg font-medium transition-all duration-150 border border-purple-200">
                            <i class="fas fa-tasks mr-2"></i>
                            Manage Assignments
                        </a>
                    @endif
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-indigo-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-history text-indigo-600 text-lg"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Timeline</h4>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-plus text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Appointment Requested</p>
                                <p class="text-sm text-gray-500">{{ $appointment->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        
                        @if($appointment->updated_at != $appointment->created_at)
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-edit text-yellow-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Last Updated</p>
                                    <p class="text-sm text-gray-500">{{ $appointment->updated_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the same modals from the main page for actions -->
<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-green-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Approve Appointment</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Confirm Approval</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Are you sure you want to approve this appointment? This action will notify the company and confirm the appointment slot.
                    </p>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-green-800">
                    <i class="fas fa-info-circle text-sm"></i>
                    <span class="text-sm font-medium">This action cannot be undone</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closeApproveModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmApprove()" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-check mr-2"></i>
                    Approve Appointment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Decline Modal -->
<div id="declineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-red-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Decline Appointment</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Confirm Decline</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Are you sure you want to decline this appointment? The company will be notified of the rejection.
                    </p>
                </div>
            </div>
            <div class="mb-6">
                <label for="declineReason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for declining (optional)
                </label>
                <textarea id="declineReason" 
                          rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                          placeholder="Provide a reason for declining this appointment..."></textarea>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-red-800">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                    <span class="text-sm font-medium">This action cannot be undone</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closeDeclineModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmDecline()" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-times mr-2"></i>
                    Decline Appointment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms for Actions -->
<form id="approveForm" action="" method="POST" style="display: none;">
    @csrf
</form>

<form id="declineForm" action="" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="reason" id="declineReasonInput">
</form>

<script>
let currentAppointmentId = {{ $appointment->id }};

function openApproveModal(appointmentId) {
    currentAppointmentId = appointmentId;
    document.getElementById('approveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmApprove() {
    if (currentAppointmentId) {
        const form = document.getElementById('approveForm');
        form.action = `/admin/appointments/${currentAppointmentId}/approve`;
        form.submit();
    }
}

function openDeclineModal(appointmentId) {
    currentAppointmentId = appointmentId;
    document.getElementById('declineModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeclineModal() {
    document.getElementById('declineModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('declineReason').value = '';
}

function confirmDecline() {
    if (currentAppointmentId) {
        const reason = document.getElementById('declineReason').value;
        document.getElementById('declineReasonInput').value = reason;
        
        const form = document.getElementById('declineForm');
        form.action = `/admin/appointments/${currentAppointmentId}/decline`;
        form.submit();
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = ['approveModal', 'declineModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (modalId === 'declineModal') {
                document.getElementById('declineReason').value = '';
            }
        }
    });
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['approveModal', 'declineModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                if (modalId === 'declineModal') {
                    document.getElementById('declineReason').value = '';
                }
            }
        });
    }
});
</script>
@endsection
