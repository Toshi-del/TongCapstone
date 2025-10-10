@extends('layouts.radiologist')

@section('title', 'OPD X-Ray')
@section('page-title', 'OPD Chest X-Ray')
@section('page-description', 'Review and analyze OPD X-ray images')

@section('content')
<div class="space-y-8">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-200 rounded-xl p-6 flex items-center space-x-4 shadow-lg">
            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-emerald-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-emerald-800 font-semibold text-lg">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors p-2">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 flex items-center space-x-4 shadow-lg">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-red-800 font-semibold text-lg">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors p-2">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border-2 border-white/20">
                        <i class="fas fa-walking text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">OPD X-Ray Services</h2>
                        <p class="text-purple-100 text-sm">X-Ray examinations for outpatient department walk-in consultations</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-white/90 text-sm">Total Patients</div>
                    <div class="text-white font-bold text-3xl">{{ count($opdPatients) }}</div>
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
                        All X-Rays
                        @php
                            $allCount = \App\Models\User::where('role', 'opd')
                                ->whereHas('medicalChecklists', function($q) {
                                    $q->where('examination_type', 'opd')
                                      ->whereNotNull('xray_image_path')
                                      ->where('xray_image_path', '!=', '');
                                })
                                ->count();
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
                                ->whereHas('medicalChecklists', function($q) {
                                    $q->where('examination_type', 'opd')
                                      ->whereNotNull('xray_image_path')
                                      ->where('xray_image_path', '!=', '');
                                })
                                ->whereDoesntHave('opdExamination', function($q) {
                                    $q->whereNotNull('lab_findings')
                                      ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_findings, '$.\"chest_xray\".\"result\"')) IS NOT NULL")
                                      ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_findings, '$.\"chest_xray\".\"result\"')) != ''");
                                })
                                ->count();
                        @endphp
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'needs_attention' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $needsAttentionCount }}
                        </span>
                    </a>
                    
                    <a href="{{ request()->fullUrlWithQuery(['xray_status' => 'completed']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'completed' ? 'bg-purple-600 text-white' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50' }}">
                        <i class="fas fa-check-circle mr-2"></i>
                        Completed
                        @php
                            $completedCount = \App\Models\User::where('role', 'opd')
                                ->whereHas('medicalChecklists', function($q) {
                                    $q->where('examination_type', 'opd')
                                      ->whereNotNull('xray_image_path')
                                      ->where('xray_image_path', '!=', '');
                                })->whereHas('opdExamination', function($q) {
                                    $q->whereNotNull('lab_findings')
                                      ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_findings, '$.\"chest_xray\".\"result\"')) IS NOT NULL")
                                      ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_findings, '$.\"chest_xray\".\"result\"')) != ''");
                                })
                                ->count();
                        @endphp
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'completed' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $completedCount }}
                        </span>
                    </a>
                </div>
                
                <!-- Search Form -->
                <form method="GET" action="{{ route('radiologist.opd.xray') }}" class="flex items-center space-x-3">
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
                            $medicalChecklist = $patient->medicalChecklists()
                                ->where('examination_type', 'opd')
                                ->whereNotNull('xray_image_path')
                                ->first();
                            
                            // Check for radiologist findings in OpdExamination lab_findings
                            $opdExamination = $patient->opdExamination;
                            $hasFindings = false;
                            if ($opdExamination && isset($opdExamination->lab_findings['chest_xray']['result'])) {
                                $hasFindings = !empty($opdExamination->lab_findings['chest_xray']['result']);
                            }
                        @endphp
                        
                        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <!-- Patient Info -->
                                    <div class="flex items-center space-x-4">
                                        <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">
                                                {{ trim(($patient->fname ?? '') . ' ' . ($patient->lname ?? '')) ?: 'N/A' }}
                                            </h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                                <span><i class="fas fa-envelope mr-1"></i>{{ $patient->email }}</span>
                                                @if($patient->age)
                                                    <span><i class="fas fa-birthday-cake mr-1"></i>{{ $patient->age }} years old</span>
                                                @endif
                                                @if($patient->gender)
                                                    <span><i class="fas fa-venus-mars mr-1"></i>{{ ucfirst($patient->gender) }}</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                <span class="font-medium">Patient ID:</span> OPD-{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status and Actions -->
                                    <div class="flex items-center space-x-6">
                                        <!-- X-Ray Status -->
                                        <div class="text-center">
                                            @if($hasFindings)
                                                <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    Review Completed
                                                </div>
                                                @if($opdExamination && isset($opdExamination->lab_findings['chest_xray']['reviewed_at']))
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Reviewed: {{ \Carbon\Carbon::parse($opdExamination->lab_findings['chest_xray']['reviewed_at'])->format('M d, Y') }}
                                                    </p>
                                                @endif
                                            @else
                                                <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    Pending Review
                                                </div>
                                            @endif
                                        </div>

                                        <!-- X-Ray Image Info -->
                                        @if($medicalChecklist && $medicalChecklist->xray_image_path)
                                            <div class="text-center">
                                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-image mr-1"></i>
                                                    X-Ray Available
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    By: {{ $medicalChecklist->chest_xray_done_by ?? 'N/A' }}
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Action Button -->
                                        <a href="{{ route('radiologist.opd.show', $patient->id) }}" 
                                           class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                                            <i class="fas fa-x-ray mr-2"></i>
                                            {{ $hasFindings ? 'View Review' : 'Review X-Ray' }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                @if($medicalChecklist)
                                    <div class="mt-6 pt-4 border-t border-gray-100">
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Registration:</span>
                                                <span class="font-medium ml-1">{{ $patient->created_at->format('M d, Y') }}</span>
                                            </div>
                                            @if($medicalChecklist->date)
                                                <div>
                                                    <span class="text-gray-500">X-Ray Date:</span>
                                                    <span class="font-medium ml-1">{{ \Carbon\Carbon::parse($medicalChecklist->date)->format('M d, Y') }}</span>
                                                </div>
                                            @endif
                                            @if($opdExamination && isset($opdExamination->lab_findings['chest_xray']['result']))
                                                <div>
                                                    <span class="text-gray-500">Result:</span>
                                                    <span class="font-medium ml-1 {{ $opdExamination->lab_findings['chest_xray']['result'] === 'Normal' ? 'text-green-600' : 'text-orange-600' }}">
                                                        {{ $opdExamination->lab_findings['chest_xray']['result'] }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if($hasFindings)
                                                <div>
                                                    <span class="text-gray-500">Status:</span>
                                                    <span class="font-medium ml-1 text-green-600">Ready for Doctor</span>
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
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-x-ray text-purple-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">
                        @if($currentTab === 'needs_attention')
                            No OPD X-Rays Need Attention
                        @else
                            No Completed OPD X-Ray Reviews
                        @endif
                    </h3>
                    <p class="text-gray-600 max-w-md mx-auto">
                        @if($currentTab === 'needs_attention')
                            All OPD X-ray images have been reviewed or there are no pending cases at this time.
                        @else
                            No OPD X-ray reviews have been completed yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
