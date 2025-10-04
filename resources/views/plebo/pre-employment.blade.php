@extends('layouts.plebo')

@section('title', 'Pre-Employment Blood Collection')

@section('page-title', 'Pre-Employment Records')

@section('content')
<!-- Success/Error Messages -->
@if(session('success'))
<div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 flex items-center space-x-3">
    <div class="flex-shrink-0">
        <i class="fas fa-check-circle text-green-600 text-xl"></i>
    </div>
    <div>
        <p class="text-green-800 font-medium">{{ session('success') }}</p>
    </div>
    <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 flex items-center space-x-3">
    <div class="flex-shrink-0">
        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
    </div>
    <div>
        <p class="text-red-800 font-medium">{{ session('error') }}</p>
    </div>
    <button onclick="this.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

<!-- Blood Collection Status Tabs -->
<div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200 mb-8">
    @php
        $currentTab = request('blood_status', 'needs_attention');
    @endphp
    
    <!-- Tab Navigation -->
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex space-x-1">
                <a href="{{ request()->fullUrlWithQuery(['blood_status' => 'needs_attention']) }}" 
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'needs_attention' ? 'bg-red-600 text-white' : 'text-gray-600 hover:text-red-600 hover:bg-red-50' }}">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Needs Attention
                    @php
                        // Phlebotomy-focused count: only blood_extraction_done_by required
                        $needsAttentionCount = \App\Models\PreEmploymentRecord::where('status', 'approved')
                            ->where(function($query) {
                                // Check medical test relationships OR other_exams column
                                $query->whereHas('medicalTest', function($q) {
                                    $q->where(function($subQ) {
                                        $subQ->where('name', 'like', '%Pre-Employment%')
                                             ->orWhere('name', 'like', '%CBC%')
                                             ->orWhere('name', 'like', '%FECA%')
                                             ->orWhere('name', 'like', '%Urine%')
                                             ->orWhere('name', 'like', '%Blood%')
                                             ->orWhere('name', 'like', '%Laboratory%');
                                    });
                                })->orWhereHas('medicalTests', function($q) {
                                    $q->where(function($subQ) {
                                        $subQ->where('name', 'like', '%Pre-Employment%')
                                             ->orWhere('name', 'like', '%CBC%')
                                             ->orWhere('name', 'like', '%FECA%')
                                             ->orWhere('name', 'like', '%Urine%')
                                             ->orWhere('name', 'like', '%Blood%')
                                             ->orWhere('name', 'like', '%Laboratory%');
                                    });
                                })->orWhere(function($q) {
                                    $q->where('other_exams', 'like', '%Pre-Employment%')
                                      ->orWhere('other_exams', 'like', '%CBC%')
                                      ->orWhere('other_exams', 'like', '%FECA%')
                                      ->orWhere('other_exams', 'like', '%Urine%')
                                      ->orWhere('other_exams', 'like', '%Blood%')
                                      ->orWhere('other_exams', 'like', '%Laboratory%');
                                });
                            })
                            ->where(function($q) {
                                $q->whereDoesntHave('medicalChecklist')
                                  ->orWhereHas('medicalChecklist', function($subQ) {
                                      // Phlebotomy focus: only check blood_extraction_done_by
                                      $subQ->where(function($checkQ) {
                                          $checkQ->whereNull('blood_extraction_done_by')
                                                 ->orWhere('blood_extraction_done_by', '');
                                      });
                                  })
                                  ->orWhereDoesntHave('preEmploymentExamination', function($subQ) {
                                      $subQ->whereNotNull('lab_report');
                                  });
                            })
                            ->count();
                    @endphp
                    <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'needs_attention' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $needsAttentionCount }}
                    </span>
                </a>
                
                <a href="{{ request()->fullUrlWithQuery(['blood_status' => 'collection_completed']) }}" 
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'collection_completed' ? 'bg-red-600 text-white' : 'text-gray-600 hover:text-red-600 hover:bg-red-50' }}">
                    <i class="fas fa-check-circle mr-2"></i>
                    Collection Completed
                    @php
                        // Phlebotomy-focused count: blood_extraction_done_by AND lab_report exists
                        $completedCount = \App\Models\PreEmploymentRecord::where('status', 'approved')
                            ->where(function($query) {
                                // Check medical test relationships OR other_exams column
                                $query->whereHas('medicalTest', function($q) {
                                    $q->where(function($subQ) {
                                        $subQ->where('name', 'like', '%Pre-Employment%')
                                             ->orWhere('name', 'like', '%CBC%')
                                             ->orWhere('name', 'like', '%FECA%')
                                             ->orWhere('name', 'like', '%Urine%')
                                             ->orWhere('name', 'like', '%Blood%')
                                             ->orWhere('name', 'like', '%Laboratory%');
                                    });
                                })->orWhereHas('medicalTests', function($q) {
                                    $q->where(function($subQ) {
                                        $subQ->where('name', 'like', '%Pre-Employment%')
                                             ->orWhere('name', 'like', '%CBC%')
                                             ->orWhere('name', 'like', '%FECA%')
                                             ->orWhere('name', 'like', '%Urine%')
                                             ->orWhere('name', 'like', '%Blood%')
                                             ->orWhere('name', 'like', '%Laboratory%');
                                    });
                                })->orWhere(function($q) {
                                    $q->where('other_exams', 'like', '%Pre-Employment%')
                                      ->orWhere('other_exams', 'like', '%CBC%')
                                      ->orWhere('other_exams', 'like', '%FECA%')
                                      ->orWhere('other_exams', 'like', '%Urine%')
                                      ->orWhere('other_exams', 'like', '%Blood%')
                                      ->orWhere('other_exams', 'like', '%Laboratory%');
                                });
                            })
                            ->whereHas('medicalChecklist', function($q) {
                                // Phlebotomy focus: only blood_extraction_done_by required
                                $q->whereNotNull('blood_extraction_done_by')
                                  ->where('blood_extraction_done_by', '!=', '');
                            })
                            ->whereHas('preEmploymentExamination', function($q) {
                                // Must have lab_report (created by PleboController)
                                $q->whereNotNull('lab_report');
                            })
                            ->count();
                    @endphp
                    <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'collection_completed' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $completedCount }}
                    </span>
                </a>
            </div>
            
            <a href="{{ route('plebo.pre-employment') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-times mr-1"></i>Clear All Filters
            </a>
        </div>
    </div>

    <!-- Additional Filters -->
    <div class="p-6">
        <form method="GET" action="{{ route('plebo.pre-employment') }}" class="space-y-6">
            <!-- Preserve current tab -->
            <input type="hidden" name="blood_status" value="{{ $currentTab }}">
            
            <!-- Preserve search query -->
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            
            <!-- Filter Row: Company and Gender -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Company Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                    <select name="company" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="">All Companies</option>
                        @php
                            $companies = $preEmployments->pluck('company_name')->filter()->unique()->sort()->values();
                        @endphp
                        @foreach($companies as $company)
                            <option value="{{ $company }}" {{ request('company') === $company ? 'selected' : '' }}>
                                {{ $company }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Gender Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="">All Genders</option>
                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ request()->fullUrlWithQuery(['company' => null, 'gender' => null, 'search' => null]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-undo mr-2"></i>Reset Filters
                    </a>
                </div>
                
                <!-- Active Filters Display -->
                @if(request()->hasAny(['company', 'gender', 'search']))
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        @if(request('search'))
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                Search: "{{ request('search') }}"
                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-red-600 hover:text-red-800">×</a>
                            </span>
                        @endif
                        @if(request('company'))
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                Company: {{ request('company') }}
                                <a href="{{ request()->fullUrlWithQuery(['company' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">×</a>
                            </span>
                        @endif
                        @if(request('gender'))
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                Gender: {{ ucfirst(request('gender')) }}
                                <a href="{{ request()->fullUrlWithQuery(['gender' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">×</a>
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Records -->
    <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300 border-l-4 border-orange-500">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-user-md text-orange-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $preEmployments->total() }}</h3>
                <p class="text-sm text-gray-600">Total Records</p>
            </div>
        </div>
        <p class="text-gray-600 text-sm mt-4">Pre-employment medical examinations</p>
    </div>

    <!-- Approved Records -->
    <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300 border-l-4 border-green-500">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $preEmployments->where('status', 'approved')->count() }}</h3>
                <p class="text-sm text-gray-600">Approved</p>
            </div>
        </div>
        <p class="text-gray-600 text-sm mt-4">Ready for blood collection</p>
    </div>

    <!-- Pending Records -->
    <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300 border-l-4 border-yellow-500">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $preEmployments->where('status', 'pending')->count() }}</h3>
                <p class="text-sm text-gray-600">Pending</p>
            </div>
        </div>
        <p class="text-gray-600 text-sm mt-4">Awaiting approval</p>
    </div>
</div>

<!-- Pre-Employment Records Table -->
<div class="content-card rounded-2xl mb-8 overflow-hidden">
    <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-md text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Pre-Employment Blood Collection</h2>
                    <p class="text-orange-100 text-sm">Manage blood collection for employment medical examinations</p>
                </div>
            </div>
            <div class="bg-white/20 px-3 py-1 rounded-full">
                <span class="text-white font-semibold">{{ $preEmployments->count() }} Records</span>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Company</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($preEmployments as $preEmployment)
                    <tr class="hover:bg-orange-50/50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                    <span class="text-orange-600 font-bold text-sm">
                                        {{ substr($preEmployment->first_name, 0, 1) }}{{ substr($preEmployment->last_name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $preEmployment->first_name }} {{ $preEmployment->last_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $preEmployment->age }} years • {{ ucfirst($preEmployment->sex) }}</p>
                                    @if($preEmployment->email)
                                    <p class="text-xs text-orange-600">
                                        <i class="fas fa-envelope mr-1"></i>{{ $preEmployment->email }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <p class="text-gray-900 font-medium">{{ $preEmployment->company_name }}</p>
                                @if($preEmployment->phone_number)
                                <p class="text-gray-500">
                                    <i class="fas fa-phone mr-1"></i>{{ $preEmployment->phone_number }}
                                </p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = match($preEmployment->status) {
                                    'approved' => ['class' => 'bg-green-100 text-green-800 border-green-200', 'icon' => 'fa-check-circle'],
                                    'declined' => ['class' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'fa-times-circle'],
                                    'pending' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'icon' => 'fa-clock'],
                                    default => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'fa-question-circle']
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['class'] }}">
                                <i class="fas {{ $statusConfig['icon'] }} mr-1"></i>
                                {{ ucfirst($preEmployment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <p class="font-medium">{{ $preEmployment->created_at->format('M d, Y') }}</p>
                                <p class="text-gray-500">{{ $preEmployment->created_at->format('h:i A') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <!-- Blood Collection Checklist -->
                                <a href="{{ route('plebo.medical-checklist.pre-employment', $preEmployment->id) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg transition-colors duration-200"
                                   title="Blood Collection Checklist">
                                    <i class="fas fa-vial mr-2"></i>
                                    Checklist
                                </a>
                                
                                <!-- Generate Barcode -->
                                <button type="button" 
                                        data-record-id="{{ $preEmployment->id }}"
                                        class="generate-barcode-btn inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors duration-200"
                                        title="Generate Patient Barcode">
                                    <i class="fas fa-barcode mr-2"></i>
                                    Barcode
                                </button>
                               
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-md text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No pre-employment records found</p>
                                <p class="text-gray-400 text-sm">Pre-employment patients will appear here when available</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($preEmployments->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $preEmployments->links() }}
    </div>
    @endif
</div>

<!-- Enhanced Barcode Modal -->
<div id="barcodeModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50" style="display: none;">
    <div class="relative top-20 mx-auto p-0 border-0 w-96 max-w-md shadow-2xl rounded-2xl bg-white overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-barcode text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Patient Barcode</h3>
                        <p class="text-orange-100 text-sm">Pre-employment record identifier</p>
                    </div>
                </div>
                <button onclick="closeBarcodeModal()" class="text-orange-200 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-xl transition-all duration-200">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6">
            <div class="text-center">
                <!-- Barcode Display Area -->
                <div id="barcodeContainer" class="mb-6 p-6 bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl">
                    <div class="flex items-center justify-center py-8">
                        <div class="text-center space-y-3">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600 mx-auto"></div>
                            <p class="text-gray-500 text-sm">Generating barcode...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Record Information -->
                <div class="mb-6 p-4 bg-orange-50 rounded-xl">
                    <p class="text-sm text-orange-700 font-medium mb-1">Record Number</p>
                    <p id="recordNumber" class="text-xl font-bold text-orange-900"></p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-center space-x-3">
                    <button onclick="printBarcode()" 
                            class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl transition-colors duration-200 font-medium">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>
                    <button onclick="downloadBarcode()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors duration-200 font-medium">
                        <i class="fas fa-download mr-2"></i>
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js" 
        onerror="loadBarcodeFallback()"></script>

<script>
// Fallback barcode generation using a simple text-based approach
function loadBarcodeFallback() {
    console.warn('JsBarcode CDN failed to load, using fallback method');
    window.JsBarcode = {
        fallback: true
    };
}

// Simple barcode-like visualization fallback
function createFallbackBarcode(container, text) {
    container.innerHTML = `
        <div class="text-center p-4">
            <div class="inline-block bg-white border-2 border-black p-4">
                <div class="text-xs font-mono tracking-widest mb-2">${text}</div>
                <div class="flex justify-center space-x-1">
                    ${text.split('').map(char => 
                        `<div class="w-1 h-8 bg-black inline-block"></div>`
                    ).join('')}
                </div>
                <div class="text-xs font-mono tracking-widest mt-2">${text}</div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Simple barcode representation</p>
        </div>
    `;
}
function generateBarcode(recordId) {
    const modal = document.getElementById('barcodeModal');
    const barcodeContainer = document.getElementById('barcodeContainer');
    const recordNumberElement = document.getElementById('recordNumber');
    
    // Generate the record number (same format as in PleboController)
    const recordNumber = 'EMP-' + String(recordId).padStart(4, '0');
    
    // Update the modal content
    recordNumberElement.textContent = recordNumber;
    
    // Show loading state
    barcodeContainer.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="text-center space-y-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600 mx-auto"></div>
                <p class="text-gray-500 text-sm">Generating barcode...</p>
            </div>
        </div>
    `;
    
    // Show modal with animation
    modal.style.display = 'block';
    document.body.classList.add('overflow-hidden');
    
    // Add entrance animation
    setTimeout(() => {
        modal.querySelector('.relative').style.transform = 'scale(1)';
        modal.querySelector('.relative').style.opacity = '1';
    }, 10);
    
    // Wait a bit for modal to be visible, then generate barcode
    setTimeout(() => {
        try {
            // Check if JsBarcode is loaded and not in fallback mode
            if (typeof JsBarcode === 'undefined' || JsBarcode.fallback) {
                console.log('Using fallback barcode generation');
                createFallbackBarcode(barcodeContainer, recordNumber);
                return;
            }
            
            // Create canvas element for barcode
            const canvas = document.createElement('canvas');
            canvas.id = 'barcodeCanvas';
            barcodeContainer.innerHTML = '';
            barcodeContainer.appendChild(canvas);
            
            // Generate barcode using JsBarcode
            JsBarcode(canvas, recordNumber, {
                format: "CODE128",
                width: 2,
                height: 100,
                displayValue: true,
                fontSize: 16,
                margin: 10,
                background: "#ffffff",
                lineColor: "#000000",
                textAlign: "center",
                textPosition: "bottom",
                textMargin: 2
            });
            
            console.log('Barcode generated successfully for:', recordNumber);
        } catch (error) {
            console.error('Error generating barcode:', error);
            // Try fallback method
            try {
                createFallbackBarcode(barcodeContainer, recordNumber);
            } catch (fallbackError) {
                console.error('Fallback also failed:', fallbackError);
                barcodeContainer.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <p class="text-red-600 font-medium mb-2">Error generating barcode</p>
                        <p class="text-gray-500 text-sm">Please try again or contact support</p>
                    </div>
                `;
            }
        }
    }, 300);
}

function closeBarcodeModal() {
    const modal = document.getElementById('barcodeModal');
    
    // Add exit animation
    modal.querySelector('.relative').style.transform = 'scale(0.95)';
    modal.querySelector('.relative').style.opacity = '0.8';
    
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.classList.remove('overflow-hidden');
    }, 200);
}

function printBarcode() {
    const canvas = document.querySelector('#barcodeCanvas');
    const recordNumber = document.getElementById('recordNumber').textContent;
    const barcodeContainer = document.getElementById('barcodeContainer');
    
    if (!canvas && !barcodeContainer.innerHTML.includes('fallback')) {
        alert('No barcode to print. Please generate a barcode first.');
        return;
    }
    
    const printWindow = window.open('', '_blank');
    let barcodeContent;
    
    if (canvas) {
        // Use canvas data for real barcode
        const barcodeDataURL = canvas.toDataURL('image/png');
        barcodeContent = `<img src="${barcodeDataURL}" alt="Barcode for ${recordNumber}" class="barcode-image">`;
    } else {
        // Use fallback content
        barcodeContent = barcodeContainer.innerHTML;
    }
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Barcode - ${recordNumber}</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        text-align: center; 
                        padding: 20px;
                        margin: 0;
                    }
                    .barcode-container { 
                        margin: 20px 0; 
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }
                    .record-number { 
                        font-size: 18px; 
                        font-weight: bold; 
                        margin: 10px 0; 
                    }
                    .barcode-image {
                        max-width: 100%;
                        height: auto;
                    }
                    @media print {
                        body { margin: 0; padding: 10px; }
                        .barcode-container { page-break-inside: avoid; }
                    }
                </style>
            </head>
            <body>
                <h2>Medical Record Barcode</h2>
                <div class="record-number">${recordNumber}</div>
                <div class="barcode-container">
                    ${barcodeContent}
                </div>
                <p>Generated on: ${new Date().toLocaleString()}</p>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function downloadBarcode() {
    const canvas = document.querySelector('#barcodeCanvas');
    const recordNumber = document.getElementById('recordNumber').textContent;
    const barcodeContainer = document.getElementById('barcodeContainer');
    
    if (!canvas && !barcodeContainer.innerHTML.includes('fallback')) {
        alert('No barcode to download. Please generate a barcode first.');
        return;
    }
    
    try {
        if (canvas) {
            // Download as PNG image
            const link = document.createElement('a');
            link.download = `barcode-${recordNumber}.png`;
            link.href = canvas.toDataURL('image/png');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            console.log('Barcode downloaded successfully:', recordNumber);
        } else {
            // For fallback, create a simple text file
            const link = document.createElement('a');
            link.download = `barcode-${recordNumber}.txt`;
            link.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(`Medical Record Barcode\nRecord Number: ${recordNumber}\nGenerated: ${new Date().toLocaleString()}\n\nThis is a text representation of the barcode.`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            console.log('Fallback barcode info downloaded:', recordNumber);
        }
    } catch (error) {
        console.error('Error downloading barcode:', error);
        alert('Error downloading barcode. Please try again.');
    }
}

// Close modal when clicking outside
document.getElementById('barcodeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBarcodeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBarcodeModal();
    }
});

// Initialize modal animations and event listeners
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('barcodeModal');
    const modalContent = modal.querySelector('.relative');
    
    // Set initial animation state
    modalContent.style.transition = 'all 0.3s ease';
    modalContent.style.transform = 'scale(0.95)';
    modalContent.style.opacity = '0.8';
    
    // Add click event listeners to all barcode buttons
    document.querySelectorAll('.generate-barcode-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const recordId = this.getAttribute('data-record-id');
            if (recordId) {
                generateBarcode(parseInt(recordId));
            }
        });
    });
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeBarcodeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display !== 'none') {
            closeBarcodeModal();
        }
    });
    
    console.log('Pre-employment page functionality initialized');
});
</script>
@endsection