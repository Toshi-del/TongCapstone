@extends('layouts.admin')

@section('title', 'Patients - RSS Citi Health Services')
@section('page-title', 'Patient Management')

@section('content')
<div class="space-y-8">
    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="content-card rounded-xl p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Patients</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $patients->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="content-card rounded-xl p-6 border-l-4 border-emerald-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Approved</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $patients->filter(function($p) { return $p->appointment && $p->appointment->status === 'approved'; })->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="content-card rounded-xl p-6 border-l-4 border-amber-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $patients->filter(function($p) { return !$p->appointment || $p->appointment->status !== 'approved'; })->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600 text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="content-card rounded-xl p-6 border-l-4 border-cyan-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $patients->filter(function($p) { return $p->created_at && $p->created_at->isCurrentMonth(); })->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-plus text-cyan-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Annual Physical Examination Patients</h3>
                        <p class="text-blue-100 text-sm">Manage patient appointments and medical records</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Filter Dropdown -->
                    <div class="relative">
                        <select class="glass-morphism px-4 py-2 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-white/20 transition-all duration-200 border border-white/20">
                            <option value="all" class="text-gray-900">All Status</option>
                            <option value="approved" class="text-gray-900">Approved</option>
                            <option value="pending" class="text-gray-900">Pending</option>
                        </select>
                    </div>
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-white/60 text-sm"></i>
                        </div>
                        <input type="text" 
                               class="glass-morphism pl-12 pr-4 py-2 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/20 transition-all duration-200 w-72 text-sm border border-white/20" 
                               placeholder="Search by name, email, or company...">
                    </div>
                    <!-- Add Patient Button -->
                    <button class="bg-white/10 hover:bg-white/20 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 border border-white/20 backdrop-blur-sm">
                        <i class="fas fa-plus text-sm"></i>
                        <span>Add Patient</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full" id="patientsTable">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">ID</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Patient Name</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Company</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Email</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Appointment Date</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Time Slot</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Examination</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Status</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-blue-50 transition-all duration-200 border-l-4 border-transparent hover:border-blue-400">
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg text-sm font-bold text-gray-700">
                                        {{ $patient->id }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                                        <span class="text-white font-bold text-sm">
                                            {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $patient->first_name }} {{ $patient->last_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">Patient ID: #{{ $patient->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-building text-gray-400 text-xs"></i>
                                    <span class="text-sm text-gray-700">
                                        @if($patient->appointment && $patient->appointment->creator)
                                            {{ $patient->appointment->creator->company ?? 'N/A' }}
                                        @else
                                            <span class="text-gray-400 italic">No company</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                    <span class="text-sm text-gray-700">{{ $patient->email }}</span>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                @if($patient->appointment)
                                    <div class="flex items-center space-x-2 bg-blue-50 px-3 py-2 rounded-lg border border-blue-200">
                                        <i class="fas fa-calendar text-blue-500 text-xs"></i>
                                        <span class="text-sm font-medium text-blue-700">
                                            {{ \Carbon\Carbon::parse($patient->appointment->appointment_date)->format('M d, Y') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm italic">Not scheduled</span>
                                @endif
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                @if($patient->appointment)
                                    <div class="flex items-center space-x-2 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200">
                                        <i class="fas fa-clock text-emerald-500 text-xs"></i>
                                        <span class="text-sm font-medium text-emerald-700">
                                            {{ $patient->appointment->time_slot ?? 'TBD' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm italic">Not set</span>
                                @endif
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                @if($patient->appointment)
                                    <div class="bg-amber-50 px-3 py-2 rounded-lg border border-amber-200">
                                        @php
                                            $displayTests = [];
                                            
                                            // Check for selected_tests (multiple tests)
                                            if ($patient->appointment->selected_tests && count($patient->appointment->selected_tests) > 0) {
                                                foreach ($patient->appointment->selected_tests as $test) {
                                                    $displayTests[] = $test->name;
                                                }
                                            }
                                            // Fallback to single medicalTest
                                            elseif ($patient->appointment->medicalTest) {
                                                $displayTests[] = $patient->appointment->medicalTest->name;
                                            }
                                            // Final fallback to category
                                            else {
                                                $displayTests[] = optional($patient->appointment->medicalTestCategory)->name ?? 'General';
                                            }
                                        @endphp
                                        
                                        @if(count($displayTests) > 0)
                                            <div class="text-sm font-medium text-amber-800">
                                                {{ $displayTests[0] }}
                                            </div>
                                            @if(count($displayTests) > 1)
                                                <div class="text-xs text-amber-600 mt-1">
                                                    +{{ count($displayTests) - 1 }} more test{{ count($displayTests) > 2 ? 's' : '' }}
                                                </div>
                                            @endif
                                            @if($patient->age_adjusted)
                                                <div class="flex items-center space-x-1 mt-1">
                                                    <i class="fas fa-exchange-alt text-blue-500 text-xs"></i>
                                                    <span class="text-xs text-blue-600 font-medium">Changed to Drug Test only</span>
                                                </div>
                                                @if($patient->original_test_name && $patient->adjusted_test_name)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <span class="line-through text-red-500">{{ $patient->original_test_name }} (₱850)</span>
                                                        <br>
                                                        <span class="text-blue-600 font-medium">→ {{ $patient->adjusted_test_name }} (₱750)</span>
                                                        <br>
                                                        <span class="text-green-600 font-medium text-xs">
                                                            <i class="fas fa-calculator mr-1"></i>Saved ₱100
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="text-xs text-blue-600 mt-1 font-medium">
                                                        → Annual Medical with Drug Test (₱750)
                                                        <br>
                                                        <span class="text-green-600">
                                                            <i class="fas fa-calculator mr-1"></i>Saved ₱100
                                                        </span>
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            <div class="text-sm font-medium text-amber-800">General</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm italic">Not assigned</span>
                                @endif
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                @if($patient->appointment && $patient->appointment->status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-clock mr-1.5 text-xs"></i>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-5 px-6">
                                <div class="flex items-center">
                                    <button type="button" 
                                            class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium transition-all duration-150 border border-red-200"
                                            onclick="openPatientDeleteModal({{ $patient->id }})">
                                        <i class="fas fa-trash mr-1"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-16 text-center border-2 border-dashed border-gray-300">
                                <div class="flex flex-col items-center space-y-4">
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center border-4 border-blue-300">
                                        <i class="fas fa-users text-blue-500 text-3xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-semibold text-lg">No patients found</p>
                                        <p class="text-gray-500 text-sm mt-1">Get started by adding your first patient to the system</p>
                                    </div>
                                    <button class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center space-x-2 shadow-lg">
                                        <i class="fas fa-plus text-sm"></i>
                                        <span>Add First Patient</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Patient View Modal -->
<div id="patientViewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 transform transition-all duration-300">
        <div class="bg-blue-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white">Patient Details</h3>
                </div>
                <button onclick="closePatientViewModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div id="patientDetails" class="space-y-6">
                <!-- Patient details will be loaded here -->
            </div>
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <button type="button" 
                        onclick="closePatientViewModal()" 
                        class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Patient Edit Modal -->
<div id="patientEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 transform transition-all duration-300">
        <div class="bg-emerald-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white">Edit Patient</h3>
                </div>
                <button onclick="closePatientEditModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form id="patientEditForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="editFirstName" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="editFirstName" name="first_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                    <div>
                        <label for="editLastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="editLastName" name="last_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                    <div>
                        <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="editEmail" name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                    <div>
                        <label for="editPhone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" id="editPhone" name="phone" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" 
                            onclick="closePatientEditModal()" 
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Patient Delete Modal -->
<div id="patientDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-red-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trash text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Delete Patient</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Confirm Deletion</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Are you sure you want to delete this patient? This action will permanently remove all patient data and cannot be undone.
                    </p>
                </div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-red-800">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                    <span class="text-sm font-medium">This action cannot be undone</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closePatientDeleteModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmPatientDelete()" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Patient
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Delete Action -->
<form id="patientDeleteForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
let currentPatientId = null;

function openPatientViewModal(patientId) {
    currentPatientId = patientId;
    
    // In a real implementation, you would fetch patient details via AJAX
    const patientDetails = document.getElementById('patientDetails');
    patientDetails.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h5 class="font-semibold text-gray-900 mb-2">Personal Information</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID:</span>
                            <span class="font-medium">#${patientId}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-medium">Loading...</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h5 class="font-semibold text-gray-900 mb-2">Appointment Details</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">Loading...</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="bg-amber-50 p-4 rounded-lg">
                    <h5 class="font-semibold text-gray-900 mb-2">Medical Examination</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h5 class="font-semibold text-gray-900 mb-2">Status</h5>
                    <div class="space-y-2 text-sm">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>
                            Loading...
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('patientViewModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePatientViewModal() {
    document.getElementById('patientViewModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentPatientId = null;
}

function openPatientEditModal(patientId) {
    currentPatientId = patientId;
    
    // In a real implementation, you would populate the form with current patient data
    document.getElementById('editFirstName').value = '';
    document.getElementById('editLastName').value = '';
    document.getElementById('editEmail').value = '';
    document.getElementById('editPhone').value = '';
    
    document.getElementById('patientEditModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePatientEditModal() {
    document.getElementById('patientEditModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentPatientId = null;
}

function openPatientDeleteModal(patientId) {
    currentPatientId = patientId;
    document.getElementById('patientDeleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePatientDeleteModal() {
    document.getElementById('patientDeleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentPatientId = null;
}

function confirmPatientDelete() {
    if (currentPatientId) {
        const form = document.getElementById('patientDeleteForm');
        form.action = `/admin/patients/${currentPatientId}`;
        form.submit();
    }
}

// Handle edit form submission
document.getElementById('patientEditForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (currentPatientId) {
        // In a real implementation, you would submit the form data via AJAX or form submission
        console.log('Updating patient:', currentPatientId);
        // For now, just close the modal
        closePatientEditModal();
    }
});

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = ['patientViewModal', 'patientEditModal', 'patientDeleteModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentPatientId = null;
        }
    });
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['patientViewModal', 'patientEditModal', 'patientDeleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentPatientId = null;
            }
        });
    }
});
</script>
@endsection