@extends('layouts.radtech')

@section('title', 'OPD X-Ray - RSS Citi Health Services')
@section('page-title', 'OPD X-Ray')
@section('page-description', 'X-Ray services for OPD walk-in medical examinations')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="content-card rounded-xl shadow-xl border-2 border-gray-200">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-walking text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">OPD X-Ray Services</h2>
                        <p class="text-purple-100 text-sm">X-Ray examinations for outpatient department walk-in consultations</p>
                    </div>
                </div>
                <div class="px-4 py-2 bg-white/10 text-white rounded-lg backdrop-blur-sm border border-white/20 font-medium">
                    <i class="fas fa-x-ray mr-2"></i>{{ $opdPatients->count() }} Patients
                </div>
            </div>
        </div>
    </div>

    <!-- X-Ray Status Tabs -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        @php
            $currentTab = request('xray_status', 'needs_attention');
        @endphp
        
        <!-- Tab Navigation -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-1">
                    <a href="{{ request()->fullUrlWithQuery(['xray_status' => 'all']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'all' ? 'bg-purple-600 text-white' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50' }}">
                        <i class="fas fa-list mr-2"></i>
                        All Patients
                        @php
                            $allCount = \App\Models\User::where('role', 'opd')->count();
                        @endphp
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'all' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $allCount }}
                        </span>
                    </a>
                    
                    <a href="{{ request()->fullUrlWithQuery(['xray_status' => 'needs_attention']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'needs_attention' ? 'bg-purple-600 text-white' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50' }}">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Needs Attention
                        @php
                            $needsAttentionCount = \App\Models\User::where('role', 'opd')
                                ->whereDoesntHave('medicalChecklists', function($q) {
                                    $q->where('examination_type', 'opd')
                                      ->whereNotNull('chest_xray_done_by')
                                      ->where('chest_xray_done_by', '!=', '');
                                })
                                ->count();
                        @endphp
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'needs_attention' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $needsAttentionCount }}
                        </span>
                    </a>
                    
                    <a href="{{ request()->fullUrlWithQuery(['xray_status' => 'xray_completed']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'xray_completed' ? 'bg-purple-600 text-white' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50' }}">
                        <i class="fas fa-check-circle mr-2"></i>
                        X-Ray Completed
                        @php
                            $xrayCompletedCount = \App\Models\User::where('role', 'opd')
                                ->whereHas('medicalChecklists', function($q) {
                                    $q->where('examination_type', 'opd')
                                      ->whereNotNull('chest_xray_done_by')
                                      ->where('chest_xray_done_by', '!=', '');
                                })
                                ->count();
                        @endphp
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'xray_completed' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $xrayCompletedCount }}
                        </span>
                    </a>
                </div>
                
                <!-- Search Form -->
                <form method="GET" action="{{ route('radtech.opd.xray') }}" class="flex items-center space-x-3">
                    <!-- Preserve current filter -->
                    @if(request('xray_status'))
                        <input type="hidden" name="xray_status" value="{{ request('xray_status') }}">
                    @endif
                    
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm w-64" 
                               placeholder="Search by name, email...">
                    </div>
                    
                    <!-- Search Button -->
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-search text-sm"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Patients List -->
        <div class="p-6">
            @if($opdPatients->count() > 0)
                <div class="grid gap-6">
                    @foreach($opdPatients as $patient)
                        @php
                            $medicalChecklist = $patient->medicalChecklists->where('examination_type', 'opd')->first();
                            $hasXray = $medicalChecklist && !empty($medicalChecklist->chest_xray_done_by);
                            
                            // Debug: Log individual patient status
                            \Log::info('Radtech OPD Patient Debug:', [
                                'patient_id' => $patient->id,
                                'patient_name' => trim(($patient->fname ?? '') . ' ' . ($patient->lname ?? '')),
                                'has_checklist' => $medicalChecklist ? true : false,
                                'chest_xray_done_by' => $medicalChecklist->chest_xray_done_by ?? null,
                                'has_xray' => $hasXray,
                                'current_tab' => request('xray_status', 'needs_attention')
                            ]);
                        @endphp
                        
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <!-- Patient Info -->
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">
                                                {{ trim(($patient->fname ?? '') . ' ' . ($patient->lname ?? '')) ?: 'N/A' }}
                                            </h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                <span><i class="fas fa-envelope mr-1"></i>{{ $patient->email }}</span>
                                                @if($patient->age)
                                                    <span><i class="fas fa-birthday-cake mr-1"></i>{{ $patient->age }} years old</span>
                                                @endif
                                                @if($patient->gender)
                                                    <span><i class="fas fa-venus-mars mr-1"></i>{{ ucfirst($patient->gender) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status and Actions -->
                                    <div class="flex items-center space-x-4">
                                        <!-- X-Ray Status -->
                                        <div class="text-center">
                                            @if($hasXray)
                                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    X-Ray Completed
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    By: {{ $medicalChecklist->chest_xray_done_by }}
                                                </p>
                                            @else
                                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Pending X-Ray
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Action Button -->
                                        <a href="{{ route('radtech.medical-checklist.opd', $patient->id) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                            <i class="fas fa-x-ray mr-2"></i>
                                            {{ $hasXray ? 'View X-Ray' : 'Process X-Ray' }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                @if($medicalChecklist)
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Patient ID:</span>
                                                <span class="font-medium ml-1">OPD-{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Registration:</span>
                                                <span class="font-medium ml-1">{{ $patient->created_at->format('M d, Y') }}</span>
                                            </div>
                                            @if($medicalChecklist->created_at)
                                                <div>
                                                    <span class="text-gray-500">Checklist Created:</span>
                                                    <span class="font-medium ml-1">{{ $medicalChecklist->created_at->format('M d, Y') }}</span>
                                                </div>
                                            @endif
                                            @if($hasXray && $medicalChecklist->updated_at)
                                                <div>
                                                    <span class="text-gray-500">X-Ray Date:</span>
                                                    <span class="font-medium ml-1">{{ $medicalChecklist->updated_at->format('M d, Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-x-ray text-purple-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        @if($currentTab === 'needs_attention')
                            No OPD Patients Need X-Ray Attention
                        @else
                            No X-Ray Completed Records
                        @endif
                    </h3>
                    <p class="text-gray-600 max-w-md mx-auto">
                        @if($currentTab === 'needs_attention')
                            All OPD patients have completed their X-ray examinations or there are no pending cases at this time.
                        @else
                            No OPD patients have completed X-ray examinations yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
