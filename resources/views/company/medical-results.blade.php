@extends('layouts.company')

@section('title', 'Medical Results')

@section('content')
<div class="min-h-screen" style="font-family: 'Poppins', sans-serif;">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">
                            <i class="fas fa-file-medical mr-3"></i>Medical Results
                        </h1>
                        <p class="text-blue-100">View annual physical examination and pre-employment examination results</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 rounded-lg px-4 py-2">
                            <p class="text-blue-100 text-sm font-medium">Total Results</p>
                            <p class="text-white text-xl font-bold">{{ ($totalSentAnnualPhysical ?? 0) + ($totalSentPreEmployment ?? 0) }}</p>
                        </div>
                        <div class="bg-purple-500 rounded-lg px-4 py-2">
                            <p class="text-purple-100 text-xs font-medium">Annual Physical Total</p>
                            <p class="text-white text-lg font-bold">₱{{ number_format($totalPriceAnnualPhysical ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-green-500 rounded-lg px-4 py-2">
                            <p class="text-green-100 text-xs font-medium">Pre-Employment Total</p>
                            <p class="text-white text-lg font-bold">₱{{ number_format($totalPricePreEmployment ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-emerald-600 to-emerald-700">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-white text-xl mr-3"></i>
                    <span class="text-white font-medium">{{ session('success') }}</span>
                </div>
            </div>
        </div>
        @endif

   

        <!-- Filter Navigation -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-indigo-600 to-indigo-700 border-l-4 border-indigo-800">
                <h2 class="text-xl font-bold text-white" style="font-family: 'Poppins', sans-serif;">
                    <i class="fas fa-filter mr-3"></i>Filter Results
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('company.medical-results') }}" 
                       class="p-4 rounded-lg border-2 transition-all duration-200 {{ !$statusFilter ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50' }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-list text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">All Results</p>
                                <p class="text-sm text-gray-600">View everything</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('company.medical-results', ['status' => 'annual_physical']) }}" 
                       class="p-4 rounded-lg border-2 transition-all duration-200 {{ $statusFilter === 'annual_physical' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300 hover:bg-purple-50' }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-stethoscope text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Annual Physical</p>
                                <p class="text-sm text-gray-600">₱{{ number_format($totalPriceAnnualPhysical ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('company.medical-results', ['status' => 'pre_employment']) }}" 
                       class="p-4 rounded-lg border-2 transition-all duration-200 {{ $statusFilter === 'pre_employment' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300 hover:bg-green-50' }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-briefcase text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Pre-Employment</p>
                                <p class="text-sm text-gray-600">₱{{ number_format($totalPricePreEmployment ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('company.medical-results', ['status' => 'sent_results']) }}" 
                       class="p-4 rounded-lg border-2 transition-all duration-200 {{ $statusFilter === 'sent_results' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-50' }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-paper-plane text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Sent Results</p>
                                <p class="text-sm text-gray-600">
                                    @if(($totalSentAnnualPhysical ?? 0) > 0 || ($totalSentPreEmployment ?? 0) > 0)
                                        {{ ($totalSentAnnualPhysical ?? 0) + ($totalSentPreEmployment ?? 0) }} available
                                    @else
                                        Ready to view
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Actions Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-amber-600 to-amber-700 border-l-4 border-amber-800">
                <h2 class="text-xl font-bold text-white" style="font-family: 'Poppins', sans-serif;">
                    <i class="fas fa-search mr-3"></i>Search & Actions
                </h2>
            </div>
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <!-- Search Section -->
                    <div class="flex-1 max-w-2xl">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Results</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input id="search" name="search" type="text" 
                                   class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                   placeholder="Search by patient name, email, or examination type...">
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <button type="button" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                            <i class="fas fa-sliders-h mr-2"></i>
                            Advanced Filter
                        </button>
                        <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                            <i class="fas fa-download mr-2"></i>
                            Export Results
                        </button>
                    </div>
                </div>
            </div>
        </div>



        @if(!$statusFilter || $statusFilter === 'sent_results')
        <!-- Sent Examination Results -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-emerald-600 to-emerald-700 border-l-4 border-emerald-800">
                <h2 class="text-xl font-bold text-white" style="font-family: 'Poppins', sans-serif;">
                    <i class="fas fa-paper-plane mr-3"></i>Sent Examination Results
                </h2>
                <p class="text-emerald-100 mt-1">Results sent by admin are ready for viewing</p>
            </div>
            
            @if($sentAnnualPhysicalResults->count() > 0 || $sentPreEmploymentResults->count() > 0)
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Annual Physical Results -->
                    @foreach($sentAnnualPhysicalResults as $exam)
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
                                    <p class="text-purple-700 text-sm">Patient ID: {{ $exam->patient_id }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-600 text-white">
                                <i class="fas fa-stethoscope mr-1"></i>
                                Annual Physical
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Examination Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($exam->date)->format('M d, Y') }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Sent Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $exam->updated_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-600">{{ $exam->updated_at->format('g:i A') }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Price</p>
                                <p class="text-sm font-semibold text-purple-900">₱{{ number_format($exam->patient && $exam->patient->appointment ? $exam->patient->appointment->total_price : 0, 2) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Result Available
                            </span>
                            <div class="flex space-x-2">
                                <button class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200" onclick="viewSentResult('annual_physical', {{ $exam->id }})">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors duration-200" onclick="downloadResult('annual_physical', {{ $exam->id }})">
                                    <i class="fas fa-download mr-1"></i>Download
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Pre-Employment Results -->
                    @foreach($sentPreEmploymentResults as $exam)
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
                                    <p class="text-green-700 text-sm">{{ $exam->company_name }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-600 text-white">
                                <i class="fas fa-briefcase mr-1"></i>
                                Pre-Employment
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Examination Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($exam->date)->format('M d, Y') }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Sent Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $exam->updated_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-600">{{ $exam->updated_at->format('g:i A') }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Price</p>
                                <p class="text-sm font-semibold text-green-900">₱{{ number_format($exam->preEmploymentRecord ? $exam->preEmploymentRecord->total_price : 0, 2) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            @if($exam->status === 'sent_to_both')
                                <div class="flex space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-building mr-1"></i>Company
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-user mr-1"></i>Patient
                                    </span>
                                </div>
                            @elseif($exam->status === 'sent_to_company')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-building mr-1"></i>Sent to Company
                                </span>
                            @elseif($exam->status === 'sent_to_patient')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-user mr-1"></i>Sent to Patient
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Result Available
                                </span>
                            @endif
                            <div class="flex space-x-2">
                                <button class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200" onclick="viewSentResult('pre_employment', {{ $exam->id }})">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors duration-200" onclick="downloadResult('pre_employment', {{ $exam->id }})">
                                    <i class="fas fa-download mr-1"></i>Download
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <!-- Enhanced Empty State -->
            <div class="p-12 text-center">
                <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-paper-plane text-emerald-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Results Available Yet</h3>
                <p class="text-gray-600 mb-4">Medical examination results sent by admin will appear here.</p>
                <div class="bg-blue-50 rounded-lg p-4 max-w-md mx-auto">
                    <p class="text-blue-800 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        Results are typically available within 24-48 hours after your examination.
                    </p>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const tableRows = document.querySelectorAll('tbody tr');
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Export functionality
    document.querySelectorAll('button').forEach(button => {
        if (button.textContent.includes('Export')) {
            button.addEventListener('click', function() {
                // This would typically trigger a download of filtered results
                alert('Export functionality would be implemented here');
            });
        }
        if (button.textContent.includes('Filter')) {
            button.addEventListener('click', function() {
                // This would typically open a filter modal
                alert('Filter functionality would be implemented here');
            });
        }
    });
});

// Functions for sent results
function viewSentResult(type, id) {
    if (type === 'annual_physical') {
        // Open annual physical examination details in a modal or new page
        window.open(`/company/view-sent-annual-physical/${id}`, '_blank');
    } else if (type === 'pre_employment') {
        // Open pre-employment examination details in a modal or new page
        window.open(`/company/view-sent-pre-employment/${id}`, '_blank');
    }
}

function downloadResult(type, id) {
    if (type === 'annual_physical') {
        // Download annual physical examination results
        window.open(`/company/download-sent-annual-physical/${id}`, '_blank');
    } else if (type === 'pre_employment') {
        // Download pre-employment examination results
        window.open(`/company/download-sent-pre-employment/${id}`, '_blank');
    }
}
</script>
@endpush
@endsection
