@extends('layouts.doctor')

@section('title', 'Annual Physical Examination')
@section('page-title', 'Annual Physical Examination')
@section('page-description', 'View and manage annual physical medical examination')

@section('content')
<div class="space-y-8">
    
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-purple-600">
        <div class="px-8 py-6 bg-gradient-to-r from-purple-600 to-purple-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-2">
                        <i class="fas fa-heartbeat mr-3"></i>Annual Physical Medical Examination
                    </h1>
                    <p class="text-purple-100">Comprehensive annual health assessment and medical evaluation</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-purple-800 bg-opacity-50 rounded-lg px-4 py-2 border border-purple-500">
                        <p class="text-purple-200 text-sm font-medium">Exam ID</p>
                        <p class="text-white text-lg font-bold">#{{ $examination->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Patient Information Section -->
        @if($examination->patient)
        <div class="px-8 py-6 bg-gradient-to-r from-green-600 to-green-700 border-l-4 border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-user mr-3"></i>Patient Information
                    </h2>
                    <p class="text-green-100 mt-1">Patient details and company information</p>
                </div>
            </div>
        </div>
        
        <div class="p-8 bg-green-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                    <div class="text-lg font-bold text-blue-800">{{ $examination->patient->full_name ?? ($examination->patient->first_name . ' ' . $examination->patient->last_name) }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border-l-4 border-green-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Age</label>
                    <div class="text-lg font-bold text-green-800">{{ $examination->patient->age ?? 'N/A' }} years</div>
                </div>
                <div class="bg-white rounded-lg p-4 border-l-4 border-indigo-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Sex</label>
                    <div class="text-lg font-bold text-indigo-800">{{ $examination->patient->sex ? ucfirst($examination->patient->sex) : 'N/A' }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border-l-4 border-yellow-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Company</label>
                    <div class="text-sm font-semibold text-yellow-800 truncate">{{ $examination->patient->company_name ?? ($examination->patient->company ?? 'N/A') }}</div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Examination Details Section -->
        <div class="p-8">
            <!-- Examination Status -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-green-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-check text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Examination Status</h3>
                    </div>
                </div>
                <div class="p-6 bg-green-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Examination Date</label>
                            <p class="text-gray-900">{{ $examination->date ? \Carbon\Carbon::parse($examination->date)->format('F j, Y') : 'N/A' }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $examination->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($examination->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($examination->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($examination->status) }}
                            </span>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Examined By</label>
                            <p class="text-gray-900">{{ $examination->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chest X-Ray Section -->
            @php
                // Get chest X-ray data from lab_findings
                $chestXrayData = null;
                $xrayImage = null;
                
                if($examination->lab_findings && is_array($examination->lab_findings)) {
                    $chestXrayData = $examination->lab_findings['chest_xray'] ?? ($examination->lab_findings['Chest X-Ray'] ?? null);
                }
                
                // Get the medical checklist with X-ray image
                if($examination->patient) {
                    $xrayChecklist = \App\Models\MedicalChecklist::where('patient_id', $examination->patient->id)
                        ->whereNotNull('xray_image_path')
                        ->latest('date')
                        ->first();
                    
                    // Fallback: attempt match by full name if still null
                    if (!$xrayChecklist) {
                        $fullName = $examination->patient->first_name . ' ' . $examination->patient->last_name;
                        $xrayChecklist = \App\Models\MedicalChecklist::where('name', $fullName)
                            ->whereNotNull('xray_image_path')
                            ->latest('date')
                            ->first();
                    }
                    
                    if($xrayChecklist && $xrayChecklist->xray_image_path) {
                        $xrayImage = $xrayChecklist->xray_image_path;
                    }
                }
            @endphp
            
            @if($chestXrayData || $xrayImage)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-purple-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600">
                    <div class="flex items-center">
                        <i class="fas fa-x-ray text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Chest X-Ray Results</h3>
                    </div>
                </div>
                <div class="p-6 bg-purple-50">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- X-Ray Image -->
                        @if($xrayImage)
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-image text-purple-500 mr-2"></i>
                                X-Ray Image
                            </h4>
                            <div class="border rounded p-3 bg-gray-50">
                                <img src="{{ asset('storage/' . $xrayImage) }}" 
                                     alt="Chest X-Ray" 
                                     class="w-full h-64 object-contain bg-white border rounded cursor-zoom-in" 
                                     id="doctor-xray-thumb" />
                                <div class="text-xs text-gray-500 mt-2 text-center">
                                    <i class="fas fa-search-plus mr-1"></i>Click image to open fullscreen and zoom
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- X-Ray Findings -->
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-clipboard-list text-purple-500 mr-2"></i>
                                Radiologist Findings
                            </h4>
                            
                            @if($chestXrayData)
                                <div class="space-y-4">
                                    <!-- Current Result -->
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Result</label>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                            {{ (isset($chestXrayData['result']) && strtolower($chestXrayData['result']) === 'normal') ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200' }}">
                                            @if(isset($chestXrayData['result']) && strtolower($chestXrayData['result']) === 'normal')
                                                <i class="fas fa-check-circle mr-1"></i>
                                            @else
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                            @endif
                                            {{ $chestXrayData['result'] ?? '—' }}
                                        </span>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Findings</label>
                                        <p class="text-gray-900 text-sm">{{ $chestXrayData['finding'] ?? '—' }}</p>
                                    </div>
                                    
                                    <!-- Radiologist Info -->
                                    @if(isset($chestXrayData['reviewed_by']) || isset($chestXrayData['reviewed_at']))
                                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                        <label class="block text-sm font-medium text-blue-700 mb-2">
                                            <i class="fas fa-user-md mr-1"></i>Review Information
                                        </label>
                                        @if(isset($chestXrayData['reviewed_at']))
                                            <p class="text-blue-800 text-xs">
                                                <i class="fas fa-clock mr-1"></i>
                                                Reviewed: {{ \Carbon\Carbon::parse($chestXrayData['reviewed_at'])->format('M j, Y g:i A') }}
                                            </p>
                                        @endif
                                        @if(isset($chestXrayData['reviewed_by']))
                                            @php
                                                $radiologist = \App\Models\User::find($chestXrayData['reviewed_by']);
                                            @endphp
                                            @if($radiologist)
                                            <p class="text-blue-800 text-xs mt-1">
                                                <i class="fas fa-user mr-1"></i>
                                                By: {{ $radiologist->name }}
                                            </p>
                                            @endif
                                        @endif
                                    </div>
                                    @endif
                                    
                                    <!-- Multiple Reviews (if available) -->
                                    @if(isset($chestXrayData['reviews']) && is_array($chestXrayData['reviews']) && count($chestXrayData['reviews']) > 1)
                                    <div class="bg-amber-50 rounded-lg p-3 border border-amber-200">
                                        <label class="block text-sm font-medium text-amber-700 mb-2">
                                            <i class="fas fa-users mr-1"></i>Multiple Reviews Available
                                        </label>
                                        <p class="text-amber-800 text-xs">
                                            This X-ray has been reviewed by {{ count($chestXrayData['reviews']) }} radiologists. 
                                            The current result shows the most recent review.
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-exclamation-circle text-gray-400 text-3xl mb-2"></i>
                                    <p class="text-gray-500">No radiologist findings available yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- X-Ray Image Viewer Modal -->
            @if($xrayImage)
            <div id="doctor-image-viewer-overlay" class="fixed inset-0 bg-black bg-opacity-95 hidden z-50">
                <div class="absolute top-3 right-3 flex items-center space-x-2">
                    <button type="button" id="doctor-zoom-out" class="px-3 py-2 bg-gray-800 text-white rounded shadow hover:bg-gray-700">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button type="button" id="doctor-zoom-reset" class="px-3 py-2 bg-gray-800 text-white rounded shadow hover:bg-gray-700">
                        <i class="fas fa-expand-arrows-alt"></i> Reset
                    </button>
                    <button type="button" id="doctor-zoom-in" class="px-3 py-2 bg-gray-800 text-white rounded shadow hover:bg-gray-700">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button type="button" id="doctor-close-viewer" class="px-3 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
                <div id="doctor-viewer-canvas" class="w-full h-full flex items-center justify-center overflow-hidden cursor-grab">
                    <img id="doctor-viewer-image" src="{{ asset('storage/' . $xrayImage) }}" alt="Chest X-Ray Full" class="select-none" draggable="false" />
                </div>
            </div>
            @endif
            @endif

            <!-- Findings and Recommendations -->
            @if($examination->findings || $examination->physical_findings || $examination->lab_findings)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-red-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-red-500 to-red-600">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-check text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Findings & Recommendations</h3>
                    </div>
                </div>
                <div class="p-6 bg-red-50">
                    @if($examination->findings)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">General Findings</label>
                        <div class="bg-white p-4 rounded-lg border border-gray-200 prose max-w-none">
                            @if(is_string($examination->findings))
                                {!! nl2br(e($examination->findings)) !!}
                            @elseif(is_array($examination->findings) || is_object($examination->findings))
                                <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($examination->findings, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                {{ $examination->findings }}
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($examination->physical_findings)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Physical Examination Findings</label>
                        <div class="bg-white p-4 rounded-lg border border-gray-200 prose max-w-none">
                            @if(is_string($examination->physical_findings))
                                {!! nl2br(e($examination->physical_findings)) !!}
                            @elseif(is_array($examination->physical_findings) || is_object($examination->physical_findings))
                                <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($examination->physical_findings, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                {{ $examination->physical_findings }}
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($examination->lab_findings)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Laboratory Findings</label>
                        <div class="bg-white p-4 rounded-lg border border-gray-200 prose max-w-none">
                            @if(is_string($examination->lab_findings))
                                {!! nl2br(e($examination->lab_findings)) !!}
                            @elseif(is_array($examination->lab_findings) || is_object($examination->lab_findings))
                                <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($examination->lab_findings, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                {{ $examination->lab_findings }}
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0 sm:space-x-4 mt-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('doctor.annual-physical') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @if($examination->patient)
                        <form action="{{ route('doctor.annual-physical.by-patient.submit', $examination->patient->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <i class="fas fa-paper-plane mr-2"></i>Submit to Admin
                            </button>
                        </form>
                    @endif
                    @if($examination->patient)
                        <a href="{{ route('doctor.annual-physical.edit', $examination->id) }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i>Edit Examination
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chest X-Ray Section -->
    @php
        // Get chest X-ray data from lab_findings
        $chestXrayData = null;
        $xrayImage = null;
        
        // Debug: Log what we're working with in the view
        \Log::info('Doctor View - Annual Physical Examination Debug:', [
            'examination_id' => $examination->id,
            'patient_name' => $examination->patient->full_name ?? 'Unknown',
            'lab_findings_raw' => $examination->lab_findings,
            'lab_findings_type' => gettype($examination->lab_findings),
            'is_array' => is_array($examination->lab_findings),
            'available_keys' => is_array($examination->lab_findings) ? array_keys($examination->lab_findings) : 'not_array'
        ]);
        
        if($examination->lab_findings && is_array($examination->lab_findings)) {
            $chestXrayData = $examination->lab_findings['chest_xray'] ?? ($examination->lab_findings['Chest X-Ray'] ?? null);
            
            \Log::info('Doctor View - Annual Physical Chest X-ray Data Retrieved:', [
                'chest_xray_found' => !is_null($chestXrayData),
                'chest_xray_data' => $chestXrayData
            ]);
        }
        
        // Get the medical checklist with X-ray image
        if($examination->patient) {
            $xrayChecklist = \App\Models\MedicalChecklist::where('patient_id', $examination->patient->id)
                ->whereNotNull('xray_image_path')
                ->latest('date')
                ->first();
            
            // Fallback: attempt match by full name if still null
            if (!$xrayChecklist) {
                $fullName = $examination->patient->full_name ?? ($examination->patient->first_name . ' ' . $examination->patient->last_name);
                $xrayChecklist = \App\Models\MedicalChecklist::where('name', $fullName)
                    ->whereNotNull('xray_image_path')
                    ->latest('date')
                    ->first();
            }
            
            if($xrayChecklist && $xrayChecklist->xray_image_path) {
                $xrayImage = $xrayChecklist->xray_image_path;
            }
        }
    @endphp
    
    <!-- Debug Section (temporary) -->
    @if(config('app.debug'))
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
        <h4 class="font-bold text-yellow-800 mb-2">🔍 Debug Information - Annual Physical</h4>
        <div class="text-sm text-yellow-700 space-y-1">
            <p><strong>Examination ID:</strong> {{ $examination->id }}</p>
            <p><strong>Patient Name:</strong> {{ $examination->patient->full_name ?? 'Unknown' }}</p>
            <p><strong>Lab Findings Exists:</strong> {{ $examination->lab_findings ? 'YES' : 'NO' }}</p>
            <p><strong>Lab Findings Type:</strong> {{ gettype($examination->lab_findings) }}</p>
            @if(is_array($examination->lab_findings))
                <p><strong>Available Keys:</strong> {{ implode(', ', array_keys($examination->lab_findings)) }}</p>
                <p><strong>Chest X-ray Data Found:</strong> {{ isset($examination->lab_findings['chest_xray']) ? 'YES' : 'NO' }}</p>
                @if(isset($examination->lab_findings['chest_xray']))
                    <p><strong>X-ray Result:</strong> {{ $examination->lab_findings['chest_xray']['result'] ?? 'Not set' }}</p>
                    <p><strong>X-ray Finding:</strong> {{ $examination->lab_findings['chest_xray']['finding'] ?? 'Not set' }}</p>
                @endif
            @else
                <p><strong>Raw Lab Findings:</strong> {{ json_encode($examination->lab_findings) }}</p>
            @endif
        </div>
    </div>
    @endif
    
    <!-- Always show chest X-ray section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-purple-500 mb-8">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-x-ray text-white text-xl mr-3"></i>
                    <h3 class="text-lg font-bold text-white">Chest X-Ray Results</h3>
                </div>
                <div class="flex items-center space-x-2">
                    @if($chestXrayData)
                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-check-circle mr-1"></i>Connected
                        </span>
                    @else
                        <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="p-6 bg-purple-50">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- X-Ray Image -->
                @if($xrayImage)
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-image text-purple-500 mr-2"></i>
                        X-Ray Image
                    </h4>
                    <div class="border rounded p-3 bg-gray-50">
                        <img src="{{ asset('storage/' . $xrayImage) }}" 
                             alt="Chest X-Ray" 
                             class="w-full h-64 object-contain bg-white border rounded cursor-zoom-in" 
                             id="annual-xray-thumb" />
                        <div class="text-xs text-gray-500 mt-2 text-center">
                            Click to view fullscreen
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- X-Ray Findings -->
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-clipboard-list text-purple-500 mr-2"></i>
                        Radiologist Findings
                    </h4>
                    
                    @if($chestXrayData)
                        <div class="space-y-4">
                            <!-- Current Result -->
                            <div class="bg-gray-50 rounded-lg p-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Result</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    {{ (isset($chestXrayData['result']) && strtolower($chestXrayData['result']) === 'normal') ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200' }}">
                                    @if(isset($chestXrayData['result']) && strtolower($chestXrayData['result']) === 'normal')
                                        <i class="fas fa-check-circle mr-1"></i>
                                    @else
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                    @endif
                                    {{ $chestXrayData['result'] ?? '—' }}
                                </span>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Findings</label>
                                <p class="text-gray-900 text-sm">{{ $chestXrayData['finding'] ?? '—' }}</p>
                            </div>
                            
                            <!-- Radiologist Info -->
                            @if(isset($chestXrayData['reviewed_by']) || isset($chestXrayData['reviewed_at']))
                            <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                <label class="block text-sm font-medium text-blue-700 mb-2">
                                    <i class="fas fa-user-md mr-1"></i>Review Information
                                </label>
                                @if(isset($chestXrayData['reviewed_at']))
                                    <p class="text-blue-800 text-xs">
                                        <i class="fas fa-clock mr-1"></i>
                                        Reviewed: {{ \Carbon\Carbon::parse($chestXrayData['reviewed_at'])->format('M j, Y g:i A') }}
                                    </p>
                                @endif
                                @if(isset($chestXrayData['reviewed_by']))
                                    @php
                                        $radiologist = \App\Models\User::find($chestXrayData['reviewed_by']);
                                    @endphp
                                    @if($radiologist)
                                    <p class="text-blue-800 text-xs mt-1">
                                        <i class="fas fa-user mr-1"></i>
                                        By: {{ $radiologist->name }}
                                    </p>
                                    @endif
                                @endif
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-circle text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500 mb-2">No radiologist findings available yet</p>
                            <p class="text-xs text-gray-400">
                                Waiting for radiologist to complete chest X-ray interpretation
                            </p>
                            @if(config('app.debug'))
                            <div class="mt-4 p-3 bg-gray-100 rounded text-left text-xs">
                                <strong>Debug Info:</strong><br>
                                Lab Findings: {{ $examination->lab_findings ? 'Present' : 'Null' }}<br>
                                @if($examination->lab_findings && is_array($examination->lab_findings))
                                    Available Keys: {{ implode(', ', array_keys($examination->lab_findings)) }}<br>
                                @endif
                                Chest X-ray Data: {{ $chestXrayData ? 'Found' : 'Not Found' }}
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
// X-Ray Image Viewer for Doctor
(function(){
    var thumb = document.getElementById('doctor-xray-thumb');
    if(!thumb) return;
    var overlay = document.getElementById('doctor-image-viewer-overlay');
    var img = document.getElementById('doctor-viewer-image');
    var canvas = document.getElementById('doctor-viewer-canvas');
    var btnIn = document.getElementById('doctor-zoom-in');
    var btnOut = document.getElementById('doctor-zoom-out');
    var btnReset = document.getElementById('doctor-zoom-reset');
    var btnClose = document.getElementById('doctor-close-viewer');

    var scale = 1;
    var minScale = 0.25;
    var maxScale = 6;
    var translateX = 0, translateY = 0;
    var isPanning = false; var startX = 0; var startY = 0;

    function applyTransform(){
        img.style.transform = 'translate(' + translateX + 'px,' + translateY + 'px) scale(' + scale + ')';
        img.style.transformOrigin = 'center center';
        img.style.maxWidth = 'none';
        img.style.maxHeight = 'none';
    }

    function enterFullscreen(el){
        if(el.requestFullscreen){ el.requestFullscreen(); }
        else if(el.webkitRequestFullscreen){ el.webkitRequestFullscreen(); }
        else if(el.msRequestFullscreen){ el.msRequestFullscreen(); }
    }

    function exitFullscreen(){
        if(document.exitFullscreen){ document.exitFullscreen(); }
        else if(document.webkitExitFullscreen){ document.webkitExitFullscreen(); }
        else if(document.msExitFullscreen){ document.msExitFullscreen(); }
    }

    function openViewer(){
        overlay.classList.remove('hidden');
        scale = 1; translateX = 0; translateY = 0; applyTransform();
        setTimeout(function(){ enterFullscreen(overlay); }, 0);
    }

    function closeViewer(){
        overlay.classList.add('hidden');
        exitFullscreen();
    }

    function zoom(delta, centerX, centerY){
        var oldScale = scale;
        scale = Math.min(maxScale, Math.max(minScale, scale * delta));
        var rect = img.getBoundingClientRect();
        var cx = centerX - rect.left; var cy = centerY - rect.top;
        var factor = scale / oldScale;
        translateX = (translateX - cx) * factor + cx;
        translateY = (translateY - cy) * factor + cy;
        applyTransform();
    }

    thumb.addEventListener('click', openViewer);
    btnClose.addEventListener('click', closeViewer);
    btnIn.addEventListener('click', function(){ zoom(1.2, window.innerWidth/2, window.innerHeight/2); });
    btnOut.addEventListener('click', function(){ zoom(1/1.2, window.innerWidth/2, window.innerHeight/2); });
    btnReset.addEventListener('click', function(){ scale = 1; translateX = 0; translateY = 0; applyTransform(); });

    canvas.addEventListener('wheel', function(e){
        e.preventDefault();
        var delta = e.deltaY < 0 ? 1.1 : 1/1.1;
        zoom(delta, e.clientX, e.clientY);
    }, { passive: false });

    canvas.addEventListener('mousedown', function(e){
        isPanning = true; startX = e.clientX - translateX; startY = e.clientY - translateY; canvas.classList.remove('cursor-grab'); canvas.classList.add('cursor-grabbing');
    });
    window.addEventListener('mouseup', function(){
        isPanning = false; canvas.classList.remove('cursor-grabbing'); canvas.classList.add('cursor-grab');
    });
    window.addEventListener('mousemove', function(e){
        if(!isPanning) return; translateX = e.clientX - startX; translateY = e.clientY - startY; applyTransform();
    });

    document.addEventListener('keydown', function(e){
        if(overlay.classList.contains('hidden')) return;
        if(e.key === 'Escape') closeViewer();
        if(e.key === '+') btnIn.click();
        if(e.key === '-') btnOut.click();
        if(e.key === '0') btnReset.click();
    });
})();
</script>
@endsection
