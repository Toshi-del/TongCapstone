@extends('layouts.admin')

@section('title', 'Tests - RSS Citi Health Services')
@section('page-title', 'Tests')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Success Message -->
        @if(session('success'))
        <div id="successMessage" class="bg-white border-l-4 border-green-500 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-green-800">Success!</h3>
                                <p class="text-green-700 mt-1">{{ session('success') }}</p>
                            </div>
                            <button type="button" onclick="closeSuccessMessage()" class="text-green-400 hover:text-green-600 transition-colors">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        <div class="mt-4 flex items-center space-x-2">
                            <div class="w-full bg-green-200 rounded-full h-1">
                                <div id="progressBar" class="bg-green-500 h-1 rounded-full transition-all duration-100" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Pre-Employment Examinations Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-blue-600 px-8 py-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-briefcase text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Pre-Employment Examinations</h2>
                        <p class="text-blue-100 text-sm mt-1">Medical examinations for employment candidates</p>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-gray-400"></i>
                                    <span>Name</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-building text-gray-400"></i>
                                    <span>Company</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    <span>Date</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-info-circle text-gray-400"></i>
                                    <span>Status</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-cogs text-gray-400"></i>
                                    <span>Action</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($preEmploymentResults as $exam)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold">
                                            {{ $exam->id }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $exam->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-building text-gray-400 text-sm"></i>
                                        <span class="text-sm text-gray-700 font-medium">{{ $exam->company_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                        <span class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($exam->date)->format('M d, Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    @php
                                        $status = $exam->status ?? 'Pending';
                                    @endphp
                                    @if($status === 'Approved')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                            <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                            Ready to Send
                                        </span>
                                    @elseif($status === 'sent_to_both')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                            <i class="fas fa-check-double mr-1.5 text-xs"></i>
                                            Sent to Both
                                        </span>
                                    @elseif($status === 'sent_to_company')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                            <i class="fas fa-building mr-1.5 text-xs"></i>
                                            Sent to Company
                                        </span>
                                    @elseif($status === 'sent_to_patient')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                            <i class="fas fa-user mr-1.5 text-xs"></i>
                                            Sent to Patient
                                        </span>
                                    @elseif($status === 'sent_to_admin')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800 border border-teal-200">
                                            <i class="fas fa-paper-plane mr-1.5 text-xs"></i>
                                            Submitted by Doctor
                                        </span>
                                    @elseif($status === 'completed')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fas fa-check-double mr-1.5 text-xs"></i>
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.view-pre-employment-results', $exam->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all duration-150 shadow-md hover:shadow-lg">
                                            <i class="fas fa-eye mr-2 text-xs"></i>
                                            View
                                        </a>
                                        <button type="button" onclick="openSendModal('pre-employment', {{ $exam->id }}, '{{ $exam->name }}', '{{ $exam->company_name }}')" 
                                                class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-all duration-150 shadow-md hover:shadow-lg">
                                            <i class="fas fa-paper-plane mr-2 text-xs"></i>
                                            Send Results
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center space-y-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-briefcase text-gray-400 text-2xl"></i>
                                        </div>
                                        <div class="text-gray-500 text-sm">No pre-employment examinations found</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Annual Physical Examinations Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-emerald-600 px-8 py-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heartbeat text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Annual Physical Examinations</h2>
                        <p class="text-emerald-100 text-sm mt-1">Yearly health checkups and medical assessments</p>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-gray-400"></i>
                                    <span>Name</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    <span>Date</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-info-circle text-gray-400"></i>
                                    <span>Status</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-cogs text-gray-400"></i>
                                    <span>Action</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($annualPhysicalResults as $exam)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-semibold">
                                            {{ $exam->id }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $exam->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                        <span class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($exam->date)->format('M d, Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    @php
                                        $status = $exam->status ?? 'Pending';
                                    @endphp
                                    @if($status === 'sent_to_both')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                            <i class="fas fa-check-double mr-1.5 text-xs"></i>
                                            Sent to Both
                                        </span>
                                    @elseif($status === 'sent_to_company')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                            <i class="fas fa-building mr-1.5 text-xs"></i>
                                            Sent to Company
                                        </span>
                                    @elseif($status === 'sent_to_patient')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                            <i class="fas fa-user mr-1.5 text-xs"></i>
                                            Sent to Patient
                                        </span>
                                    @elseif($status === 'sent_to_admin')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800 border border-teal-200">
                                            <i class="fas fa-paper-plane mr-1.5 text-xs"></i>
                                            Submitted by Doctor
                                        </span>
                                    @elseif($status === 'Completed')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                            <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                            Completed
                                        </span>
                                    @elseif($status === 'In Progress')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                                            In Progress
                                        </span>
                                    @elseif($status === 'Sent')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                            <i class="fas fa-paper-plane mr-1.5 text-xs"></i>
                                            Sent
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.view-annual-physical-results', $exam->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all duration-150 shadow-md hover:shadow-lg">
                                            <i class="fas fa-eye mr-2 text-xs"></i>
                                            View
                                        </a>
                                        <button type="button" onclick="openSendModal('annual-physical', {{ $exam->id }}, '{{ $exam->name }}', '')" 
                                                class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-all duration-150 shadow-md hover:shadow-lg">
                                            <i class="fas fa-paper-plane mr-2 text-xs"></i>
                                            Send Results
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center space-y-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-heartbeat text-gray-400 text-2xl"></i>
                                        </div>
                                        <div class="text-gray-500 text-sm">No annual physical examinations found</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Send Results Modal -->
<div id="sendResultsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6 rounded-t-2xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-paper-plane text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Send Medical Results</h3>
                    <p class="text-blue-100 text-sm">Choose where to send the examination results</p>
                </div>
            </div>
        </div>
        
        <div class="p-8">
            <div class="mb-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900" id="modalPatientName">Patient Name</h4>
                        <p class="text-sm text-gray-600" id="modalExaminationType">Examination Type</p>
                        <p class="text-xs text-gray-500" id="modalCompanyName">Company Name</p>
                    </div>
                </div>
                
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-info-circle text-amber-600 text-lg mt-0.5"></i>
                        <div>
                            <h5 class="text-amber-800 font-medium mb-1">Send Confirmation</h5>
                            <p class="text-amber-700 text-sm">
                                Please choose where you want to send these medical examination results. This action will notify the recipient via email.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="space-y-3">
                <button type="button" onclick="sendToCompany()"
                        class="w-full flex items-center justify-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-building mr-3 text-lg"></i>
                    <div class="text-left">
                        <div class="font-semibold">Send to Company</div>
                        <div class="text-sm text-blue-100">Send results to the hiring company</div>
                    </div>
                </button>
                
                <button type="button" onclick="sendToPatient()"
                        class="w-full flex items-center justify-center px-6 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-user mr-3 text-lg"></i>
                    <div class="text-left">
                        <div class="font-semibold">Send to Patient</div>
                        <div class="text-sm text-green-100">Send results directly to the patient</div>
                    </div>
                </button>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <button type="button" 
                        onclick="closeSendModal()" 
                        class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-hide success message after 5 seconds with progress bar
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('successMessage');
    const progressBar = document.getElementById('progressBar');
    
    if (successMessage && progressBar) {
        let width = 100;
        const interval = setInterval(() => {
            width -= 2;
            progressBar.style.width = width + '%';
            
            if (width <= 0) {
                clearInterval(interval);
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 300);
            }
        }, 100); // 5 seconds total (100ms * 50 iterations)
    }
});

function closeSuccessMessage() {
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        successMessage.style.opacity = '0';
        successMessage.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 300);
    }
}

// Store current examination data for sending
let currentExamination = null;

function openSendModal(examinationType, examId, patientName, companyName) {
    // Store examination data
    currentExamination = {
        type: examinationType,
        id: examId,
        patientName: patientName,
        companyName: companyName
    };
    
    // Set modal content
    document.getElementById('modalPatientName').textContent = patientName;
    document.getElementById('modalExaminationType').textContent = examinationType === 'pre-employment' ? 'Pre-Employment Examination' : 'Annual Physical Examination';
    document.getElementById('modalCompanyName').textContent = companyName || 'N/A';
    
    // Show modal
    document.getElementById('sendResultsModal').classList.remove('hidden');
}

function closeSendModal() {
    document.getElementById('sendResultsModal').classList.add('hidden');
}

// Send to company function
async function sendToCompany() {
    if (!currentExamination) {
        showErrorMessage('Error', 'No examination selected');
        return;
    }
    
    try {
        const response = await fetch(`/admin/examinations/${currentExamination.type}/${currentExamination.id}/send`, {
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
            closeSendModal();
            showSuccessMessage('Success!', data.message);
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showErrorMessage('Failed to send examination', data.message || 'An error occurred while sending the examination to the company.');
        }
    } catch (error) {
        console.error('Error sending examination:', error);
        showErrorMessage('Network Error', 'Failed to send examination to company. Please check your connection and try again.');
    }
}

// Send to patient function
async function sendToPatient() {
    if (!currentExamination) {
        showErrorMessage('Error', 'No examination selected');
        return;
    }
    
    try {
        const response = await fetch(`/admin/examinations/${currentExamination.type}/${currentExamination.id}/send`, {
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
            closeSendModal();
            showSuccessMessage('Success!', data.message);
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showErrorMessage('Failed to send examination', data.message || 'An error occurred while sending the examination to the patient.');
        }
    } catch (error) {
        console.error('Error sending examination:', error);
        showErrorMessage('Network Error', 'Failed to send examination to patient. Please check your connection and try again.');
    }
}

// Close modal when clicking outside
document.getElementById('sendResultsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSendModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSendModal();
    }
});

// Show success message function
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

// Show error message function
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
    
    // Auto-hide after 8 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 8000);
}
</script>
@endsection
